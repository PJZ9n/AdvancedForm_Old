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

namespace pjz9n\advancedform;

use pocketmine\form\Form;
use function array_merge;

abstract class BaseForm implements Form
{
    /**
     * Returns the type of form (e.g. form, custom_form, modal)
     */
    abstract protected function getNetworkType(): string;

    /**
     * Returns the form data send to the client
     * This usually includes buttons, text, etc.
     */
    abstract protected function getNetworkData(): array;

    public function __construct(private string $title)
    {
    }

    final public function getTitle(): string
    {
        return $this->title;
    }

    final public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    final public function jsonSerialize(): array
    {
        return array_merge([
            "type" => $this->getNetworkType(),
            "title" => $this->getTitle(),
        ], $this->getNetworkData());
    }
}
