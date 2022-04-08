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

namespace pjz9n\advancedform\custom\element;

use pocketmine\form\FormValidationException;
use function gettype;
use function is_string;

class Input extends Element
{
    public static function create(string $name, string $text, string $placeholder = "", string $defualt = ""): self
    {
        return new self($name, $text, $placeholder, $defualt);
    }

    public function __construct(
        string         $name,
        string         $text,
        private string $placeholder,
        private string $default,
    )
    {
        parent::__construct($name, $text);
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function getDefault(): string
    {
        return $this->default;
    }

    public function setDefault(string $default): self
    {
        $this->default = $default;
        return $this;
    }

    public function validate(mixed $value): void
    {
        if (!is_string($value)) {
            throw new FormValidationException("Excepted string, got " . gettype($value));
        }
    }

    final protected function getNetworkType(): string
    {
        return ElementTypes::INPUT;
    }

    final protected function getNetworkData(): array
    {
        return [
            "placeholder" => $this->getPlaceholder(),
            "default" => $this->getDefault(),
        ];
    }
}
