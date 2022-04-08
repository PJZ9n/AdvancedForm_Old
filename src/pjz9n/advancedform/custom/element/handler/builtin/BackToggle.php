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

namespace pjz9n\advancedform\custom\element\handler\builtin;

use pjz9n\advancedform\custom\element\handler\HandlerElement;
use pjz9n\advancedform\custom\element\Toggle;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pjz9n\advancedform\util\FormUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;

class BackToggle extends Toggle implements HandlerElement
{
    /**
     * @param string[]|null $backTo
     * @phpstan-param list<class-string<Form>>|null $backTo
     */
    public function __construct(string $name, string $text, private array $backTo, bool $default = false)
    {
        parent::__construct($name, $text, $default);
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

    public function handle(Form $form, Player $player, CustomFormResponse $response): bool
    {
        if (!$response->getToggleValue($this->getName())) {
            return false;
        }
        FormUtils::back($player, $this->backTo);
        return true;
    }
}
