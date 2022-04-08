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

namespace pjz9n\advancedform\util;

use pjz9n\advancedform\AdvancedForm;
use pjz9n\advancedform\chain\FormChainListener;
use pjz9n\advancedform\chain\FormChain;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function implode;
use function in_array;

final class FormUtils
{
    public static function canBack(Player $player): bool
    {
        return FormChain::getPrevious($player) !== null;
    }

    /**
     * @param string[]|null $backTo The class of the form that is expected to back, If null is passed, it will return to any (Not recommended for security reasons)
     * @phpstan-param list<class-string<Form>>|null $backTo
     */
    public static function back(Player $player, ?array $backTo): void
    {
        $previous = FormChain::getPrevious($player);
        if ($previous !== null) {
            if ($backTo === null || in_array($previous::class, $backTo, true)) {
                FormChain::back($player);
                FormChainListener::addIgnoreForm($previous);//Prevents the previous form from being pushed to the tracker
                $player->sendForm($previous);
            } else {
                AdvancedForm::getLogger()->debug("Failed to back: except [" . implode(", ", $backTo) . "], previous: " . $previous::class);
            }
        } else {
            AdvancedForm::getLogger()->debug("Failed to back: No previous form");
            AdvancedForm::getLogger()->debug("end of form chain: no previous");
            //TODO: This hack exists because the end of the chain is not properly handled, This is not the right way to do it
            FormChain::reset($player);
        }
    }

    private function __construct()
    {
        //NOOP
    }
}
