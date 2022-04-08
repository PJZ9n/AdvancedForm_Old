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

use InvalidArgumentException;
use pjz9n\advancedform\menu\button\MenuButton;
use function array_key_exists;
use function array_merge;
use function array_values;

trait MessagesTrait
{
    /**
     * @var string[]
     * @phpstan-var list<string>
     */
    private array $messages = [];

    /**
     * @return MenuButton[]
     * @phpstan-return list<string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function hasMessages(): bool
    {
        return count($this->messages) > 0;
    }

    /**
     * @param string[] $messages
     * @phpstan-param list<string> $messages
     */
    public function setMessages(array $messages): self
    {
        $this->messages = array_values($messages);
        return $this;
    }

    public function addMessage(string $message): self
    {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @param string[] $messages
     * @phpstan-param list<string> $messages
     */
    public function addMessages(array $messages): self
    {
        $this->messages = array_merge($this->messages, array_values($messages));
        return $this;
    }

    public function removeMessage(int $index): self
    {
        if (!array_key_exists($index, $this->messages)) {
            throw new InvalidArgumentException("Message does not exist");
        }
        unset($this->messages[$index]);
        $this->messages = array_values($this->messages);
        return $this;
    }

    public function clearMessages(): self
    {
        $this->messages = [];
        return $this;
    }
}
