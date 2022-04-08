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

namespace pjz9n\advancedform\menu\button;

use JsonSerializable;
use pjz9n\advancedform\menu\button\icon\MenuButtonIcon;

class MenuButton implements JsonSerializable
{
    public function __construct(
        private string          $text,
        private ?MenuButtonIcon $icon = null,
        private mixed           $value = null,
    )
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getIcon(): ?MenuButtonIcon
    {
        return $this->icon;
    }

    public function setIcon(?MenuButtonIcon $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    final public function jsonSerialize(): array
    {
        $result = [
            "text" => $this->getText(),
        ];
        if ($this->getIcon() !== null) {
            $result["image"] = $this->getIcon();
        }
        return $result;
    }
}
