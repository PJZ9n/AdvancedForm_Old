<?php

/*
 * Copyright (c) 2022 PJZ9n.
 *
 * This file is part of AdvancedForm.
 *
 * AdvancedForm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * AdvancedForm is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with AdvancedForm. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace pjz9n\advancedform\chain;

use pjz9n\advancedform\AdvancedForm;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\form\Form;
use pocketmine\network\mcpe\handler\InGamePacketHandler;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\AssumptionFailedError;
use ReflectionClass;
use ReflectionException;
use function array_key_exists;
use function array_search;
use function array_values;

class FormChainListener implements Listener
{
    /**
     * @var Form[]
     * @phpstan-var list<Form>
     */
    private static array $ignoreForms = [];

    public static function addIgnoreForm(Form $form): void
    {
        self::$ignoreForms[] = $form;
    }

    public function __construct(private Plugin $plugin)
    {
    }

    public function onModalFormRequest(DataPacketSendEvent $event): void
    {
        foreach ($event->getPackets() as $packet) {
            if (!($packet instanceof ModalFormRequestPacket)) continue;
            foreach ($event->getTargets() as $target) {
                if (($player = $target->getPlayer()) === null) continue;
                //It will be added to the Player::$forms queue after it completes sending the packet and should be delayed
                $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($packet, $player): void {
                    $forms = $this->getForms($player);
                    $requestForm = $forms[$packet->formId] ?? null;
                    if ($requestForm === null) {
                        throw new AssumptionFailedError("The form has not been sent");
                    }

                    if (($index = array_search($requestForm, self::$ignoreForms, true)) !== false) {
                        AdvancedForm::getLogger()->debug("ignored tracking");

                        unset(self::$ignoreForms[$index]);
                        self::$ignoreForms = array_values(self::$ignoreForms);
                        return;
                    }
                    FormChain::push($player, clone $requestForm);
                }), 0);
            }
        }
    }

    public function onModalFormResponse(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        if (!($packet instanceof ModalFormResponsePacket)) {
            return;
        }
        $player = $event->getOrigin()->getPlayer();
        if ($player === null) {
            return;
        }

        if (!array_key_exists($packet->formId, $this->getForms($player))) {
            return;//Invalid response
        }

        $inGamePacketHandlerReflection = new ReflectionClass(InGamePacketHandler::class);
        $stupidJsonDecodeMethod = $inGamePacketHandlerReflection->getMethod("stupid_json_decode");
        $stupidJsonDecodeMethod->setAccessible(true);
        try {
            $decodedFormData = $stupidJsonDecodeMethod->invoke(null, $packet->formData, true);
        } catch (ReflectionException $exception) {
            throw new AssumptionFailedError(previous: $exception);
        }
        if ($decodedFormData === null) {
            //When close the form, the form chain is end
            AdvancedForm::getLogger()->debug("end of form chain: closed form");
            FormChain::reset($player);
        }
    }

    /**
     * @return Form[]
     * @phpstan-return array<int, Form>
     */
    private function getForms(Player $player): array
    {
        $playerReflection = new ReflectionClass($player);
        $formsProperty = $playerReflection->getProperty("forms");
        $formsProperty->setAccessible(true);
        return $formsProperty->getValue($player);
    }
}
