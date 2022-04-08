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
use pocketmine\form\Form;
use pocketmine\player\Player;
use WeakMap;
use function array_key_last;
use function array_pop;
use function array_reverse;

/**
 * TODO: The end of the chain is incomplete
 * WARNING: closing the form may not end the chain!
 */
final class FormChain
{
    /**@phpstan-var WeakMap<Player, list<Form>> */
    private static WeakMap $formChains;

    /**
     * @internal
     * @see AdvancedForm::register()
     */
    public static function init(): void
    {
        self::$formChains = new WeakMap();
    }

    public static function push(Player $player, Form $form): void
    {
        if (!isset(self::$formChains[$player])) {
            self::$formChains[$player] = [];
        }
        self::$formChains[$player][] = $form;
    }

    public static function back(Player $player): ?Form
    {
        if (!isset(self::$formChains[$player])) {
            return null;
        }
        return array_pop(self::$formChains[$player]);
    }

    public static function getCurrent(Player $player): ?Form
    {
        $forms = self::get($player);
        return $forms[array_key_last($forms)] ?? null;
    }

    public static function getPrevious(Player $player): ?Form
    {
        return array_reverse(self::get($player))[1] ?? null;
    }

    public static function reset(Player $player): void
    {
        self::$formChains[$player] = [];
        AdvancedForm::getLogger()->debug("reset the tracking forms");
    }

    /**
     * @return Form[]
     * @phpstan-return list<Form>
     */
    public static function get(Player $player): array
    {
        return self::$formChains[$player] ?? [];
    }

    private function __construct()
    {
        //NOOP
    }
}
