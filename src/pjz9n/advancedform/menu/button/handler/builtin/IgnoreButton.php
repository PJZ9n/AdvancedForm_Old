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

namespace pjz9n\advancedform\menu\button\handler\builtin;

use pjz9n\advancedform\menu\button\handler\HandlerButton;
use pjz9n\advancedform\menu\button\icon\MenuButtonIcon;
use pjz9n\advancedform\menu\button\MenuButton;
use pocketmine\form\Form;
use pocketmine\player\Player;

/**
 * This button is ignored when pressed
 * It can be used for close buttons etc
 */
class IgnoreButton extends MenuButton implements HandlerButton
{
    final public static function create(string $text, ?MenuButtonIcon $icon = null): self
    {
        return new self($text, $icon, null, null);
    }

    public function handle(Form $form, Player $player): bool
    {
        return true;//Do not process: MenuForm::handleSubmit()
    }
}
