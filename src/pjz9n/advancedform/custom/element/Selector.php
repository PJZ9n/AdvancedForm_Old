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

use InvalidArgumentException;
use pocketmine\form\FormValidationException;
use function array_key_exists;
use function array_merge;
use function array_search;
use function array_values;
use function gettype;
use function is_int;

abstract class Selector extends Element
{
    /**
     * @param SelectorOption[] $options
     * @phpstan-param list<SelectorOption> $options
     */
    public function __construct(string $name, string $text, private array $options, private ?int $default)
    {
        parent::__construct($name, $text);
        $this->options = array_values($this->options);

        $this->setDefault($this->default);//for validation
    }

    /**
     * @return SelectorOption[]
     * @phpstan-return list<SelectorOption>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(int $index): ?SelectorOption
    {
        return $this->options[$index] ?? null;
    }

    /**
     * @param SelectorOption[] $options
     * @phpstan-param list<SelectorOption> $options
     */
    public function setOptions(array $options): self
    {
        $this->options = array_values($options);
        return $this;
    }

    public function addOption(SelectorOption $option): self
    {
        $this->options[] = $option;
        return $this;
    }

    /**
     * @param SelectorOption[] $options
     * @phpstan-param list<SelectorOption> $options
     */
    public function addOptions(array $options): self
    {
        $this->options = array_merge($this->options, array_values($options));
        return $this;
    }

    public function removeOption(SelectorOption $option): self
    {
        if (($key = array_search($option, $this->options, true)) === false) {
            throw new InvalidArgumentException("Option does not exist");
        }

        unset($this->options[$key]);
        $this->options = array_values($this->options);

        return $this;
    }

    public function getDefault(): ?int
    {
        return $this->default;
    }

    public function setDefault(?int $default): void
    {
        if ($default !== null && !array_key_exists($default, $this->getOptions())) {
            throw new InvalidArgumentException("Default value $default does not exist");
        }
        $this->default = $default;
    }

    public function validate(mixed $value): void
    {
        if (!is_int($value)) {
            throw new FormValidationException("Excepted int, got " . gettype($value));
        }
    }
}
