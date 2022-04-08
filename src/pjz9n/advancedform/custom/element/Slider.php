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
use function is_float;
use function is_int;

class Slider extends Element
{
    public static function create(
        string $name,
        string $text,
        float  $min,
        float  $max,
        float  $step,
        ?float $default = null,
    ): self
    {
        return new self($name, $text, $min, $max, $step, $default);
    }

    public function __construct(
        string         $name,
        string         $text,
        private float  $min,
        private float  $max,
        private float  $step,
        private ?float $default,
    )
    {
        parent::__construct($name, $text);

        //TODO: validations
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): void
    {
        $this->min = $min;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): void
    {
        $this->max = $max;
    }

    public function getStep(): float
    {
        return $this->step;
    }

    public function setStep(float $step): void
    {
        $this->step = $step;
    }

    public function getDefault(): ?float
    {
        return $this->default;
    }

    public function setDefault(?float $default): void
    {
        $this->default = $default;
    }

    public function validate(mixed $value): void
    {
        if ((!is_float($value)) && (!is_int($value))) {
            throw new FormValidationException("Excepted float or int, got " . gettype($value));
        }
        if ($value < $this->getMin() || $value > $this->getMax()) {
            throw new FormValidationException("Excepted range {$this->getMin()}-{$this->getMax()}, got " . $value);
        }
        if ($value !== 0 && fmod((float)$value, $this->getStep()) !== (float)0) {//do not use % operator
            throw new FormValidationException("value($value) is not divisible by step({$this->getStep()})");
        }
    }

    protected function getNetworkType(): string
    {
        return ElementTypes::SLIDER;
    }

    protected function getNetworkData(): array
    {
        $result = [
            "min" => $this->getMin(),
            "max" => $this->getMax(),
            "step" => $this->getStep(),
        ];
        if ($this->getDefault() !== null) {
            $result["default"] = $this->getDefault();
        }
        return $result;
    }
}
