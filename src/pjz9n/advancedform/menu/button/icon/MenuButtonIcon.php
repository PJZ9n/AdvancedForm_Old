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

namespace pjz9n\advancedform\menu\button\icon;

use JsonSerializable;

class MenuButtonIcon implements JsonSerializable
{
    final public static function url(string $url): self
    {
        return new self(KnownIconTypes::URL, $url);
    }

    final public static function path(string $path): self
    {
        return new self(KnownIconTypes::PATH, $path);
    }

    /**
     * @see KnownIconTypes
     */
    public function __construct(
        private string $type,
        private string $data,
    )
    {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => $this->getType(),
            "data" => $this->getData(),
        ];
    }
}
