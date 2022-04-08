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

use JsonSerializable;
use pocketmine\form\FormValidationException;
use pocketmine\utils\TextFormat;
use function array_merge;

abstract class Element implements JsonSerializable
{
    /**
     * @throws FormValidationException
     */
    abstract public function validate(mixed $value): void;

    /**
     * Returns the type of element (e.g. input, slider, toggle)
     */
    abstract protected function getNetworkType(): string;

    /**
     * Returns the element data send to the client
     */
    abstract protected function getNetworkData(): array;

    private bool $highlight = false;
    private string $highlightPrefix = TextFormat::BOLD . TextFormat::YELLOW;

    private ?string $message = null;

    public function __construct(private string $name, private string $text)
    {
    }

    public function getName(): string
    {
        return $this->name;
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

    public function isHighlight(): bool
    {
        return $this->highlight;
    }

    public function setHighlight(bool $highlight): self
    {
        $this->highlight = $highlight;
        return $this;
    }

    public function getHighlightPrefix(): string
    {
        return $this->highlightPrefix;
    }

    public function setHighlightPrefix(string $highlightPrefix): self
    {
        $this->highlightPrefix = $highlightPrefix;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set the message
     * Displayed under the element name
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    final public function jsonSerialize(): array
    {
        return array_merge([
            "type" => $this->getNetworkType(),
            "text" => ($this->isHighlight() ? $this->highlightPrefix : "") . $this->getText() . ($this->getMessage() === null ? "" : TextFormat::EOL . TextFormat::RESET . $this->getMessage()),
        ], $this->getNetworkData());
    }
}
