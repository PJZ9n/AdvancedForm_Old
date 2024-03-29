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

namespace pjz9n\advancedform\menu\response;

use LogicException;
use pjz9n\advancedform\menu\button\MenuButton;
use pjz9n\advancedform\menu\button\NamedMenuButton;

class MenuFormResponse
{
    public function __construct(private int $index, private MenuButton $button)
    {
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getButton(): MenuButton
    {
        return $this->button;
    }

    public function getNamedButton(): NamedMenuButton
    {
        if (!($this->button instanceof NamedMenuButton)) {
            throw new LogicException("Button does not have a name");
        }
        return $this->button;
    }

    public function getValue(): mixed
    {
        return $this->getButton()->getValue();
    }
}
