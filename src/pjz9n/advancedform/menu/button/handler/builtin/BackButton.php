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
use pjz9n\advancedform\util\FormUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;

class BackButton extends MenuButton implements HandlerButton
{
    /**
     * @param string[]|null $backTo
     * @phpstan-param list<class-string<Form>>|null $backTo
     *
     * @see FormUtils::back() documentation of $backTo
     */
    final public static function create(string $text, ?array $backTo, ?MenuButtonIcon $icon = null): self
    {
        return new self($text, $backTo, $icon, null, null);
    }

    /**
     * @param string[]|null $backTo
     * @phpstan-param list<class-string<Form>>|null $backTo
     *
     * @see BackButton::create()
     */
    public function __construct(string $text, private ?array $backTo, ?MenuButtonIcon $icon, mixed $value, ?string $name)
    {
        parent::__construct($text, $icon, $value, $name);
    }

    /**
     * @return string[]|null
     * @phpstan-return list<class-string<Form>>|null
     */
    public function getBackTo(): ?array
    {
        return $this->backTo;
    }

    /**
     * @param string[]|null $backTo
     * @phpstan-param list<class-string<Form>>|null $backTo
     */
    public function setBackTo(?array $backTo): void
    {
        $this->backTo = $backTo;
    }

    public function handle(Form $form, Player $player): bool
    {
        FormUtils::back($player, $this->getBackTo());
        return true;
    }
}
