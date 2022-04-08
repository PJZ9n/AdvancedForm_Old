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

namespace pjz9n\advancedform\custom\response;

use InvalidArgumentException;
use pjz9n\advancedform\custom\CustomForm;
use pjz9n\advancedform\custom\element\Selector;
use pjz9n\advancedform\util\Utils;
use function array_key_exists;
use function array_values;
use function is_numeric;

final class CustomFormResponse
{
    /**
     * @param mixed[] $response
     * @phpstan-param array<string, mixed> $response
     */
    public function __construct(private CustomForm $form, private array $response)
    {
    }

    public function getForm(): CustomForm
    {
        return $this->form;
    }

    /**
     * @param mixed[] $response
     * @phpstan-param array<string, mixed>|list<mixed> $response
     */
    public function getAll(bool $indexed = false): array
    {
        return $indexed ? array_values($this->response) : $this->response;
    }

    public function getInputValue(string $name): string
    {
        $this->checkExists($name);
        return $this->response[$name];
    }

    /**
     * @throws InvalidResponseException
     */
    public function getInputNumericValue(string $name): int
    {
        $input = $this->getInputValue($name);
        if (!is_numeric($input)) {
            throw new InvalidResponseException("Non-numeric string");
        }
        return (int)$input;
    }

    public function getSelectorIndex(string $name): int
    {
        $this->checkExists($name);
        return $this->response[$name];
    }

    /**
     * @return mixed|null
     */
    public function getSelectorValue(string $name): mixed
    {
        $index = $this->getSelectorIndex($name);//this first, check exists

        $selector = $this->getForm()->getElement($name);
        if (!($selector instanceof Selector)) {
            throw new InvalidArgumentException("Element \"$name\" is not a " . Selector::class);
        }
        return $selector->getOption($index)->getValue();
    }

    public function getSliderValue(string $name): float
    {
        $this->checkExists($name);
        return (float)$this->response[$name];
    }

    public function getSliderValueInt(string $name): int
    {
        $this->checkExists($name);
        $value = $this->response[$name];
        if (Utils::isDecimal($value)) {
            throw new InvalidArgumentException("Element \"$name\" value($value) is decimal, did you mean? " . self::class . "::getSliderValue()");
        }
        return (int)$value;
    }

    public function getToggleValue(string $name): bool
    {
        $this->checkExists($name);
        return $this->response[$name];
    }

    private function checkExists(string $name): void
    {
        if (!array_key_exists($name, $this->response)) {
            throw new InvalidArgumentException("Response for $name does not exist");
        }
    }
}
