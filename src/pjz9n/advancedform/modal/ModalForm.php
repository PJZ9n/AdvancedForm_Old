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

namespace pjz9n\advancedform\modal;

use pjz9n\advancedform\BaseForm;
use pjz9n\advancedform\FormTypes;
use pjz9n\advancedform\MessagesTrait;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_map;
use function gettype;
use function implode;
use function is_bool;
use function str_repeat;

/**
 * Form with two buttons
 */
abstract class ModalForm extends BaseForm
{
    use MessagesTrait;

    /**
     * Called when the form is choiced by the player
     */
    abstract protected function handleChoice(Player $player, bool $choice): void;

    public function __construct(
        string         $title,
        private string $content,
        private string $button1Text = "gui.yes",
        private string $button2Text = "gui.no",
    )
    {
        parent::__construct($title);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getButton1Text(): string
    {
        return $this->button1Text;
    }

    public function setButton1Text(string $button1Text): self
    {
        $this->button1Text = $button1Text;
        return $this;
    }

    public function getButton2Text(): string
    {
        return $this->button2Text;
    }

    public function setButton2Text(string $button2Text): self
    {
        $this->button2Text = $button2Text;
        return $this;
    }

    final public function handleResponse(Player $player, $data): void
    {
        if (!is_bool($data)) {
            throw new FormValidationException("excepted bool, got " . gettype($data));
        }

        //TODO: Is it correct to do this implicitly?
        $this->clearMessages();

        $this->handleChoice($player, $data);
    }

    final protected function getNetworkType(): string
    {
        return FormTypes::MODAL;
    }

    final protected function getNetworkData(): array
    {
        $messagesString = implode(TextFormat::EOL, array_map(fn(string $message): string => $message . TextFormat::RESET, $this->getMessages()));
        if ($messagesString !== "") {
            $messagesString .= str_repeat(TextFormat::EOL, 2);
        }
        return [
            "content" => $messagesString . $this->getContent(),
            "button1" => $this->getButton1Text(),
            "button2" => $this->getButton2Text(),
        ];
    }
}
