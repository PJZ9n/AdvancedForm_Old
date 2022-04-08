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

namespace pjz9n\advancedform\menu;

use InvalidArgumentException;
use pjz9n\advancedform\BaseForm;
use pjz9n\advancedform\FormTypes;
use pjz9n\advancedform\menu\button\handler\HandlerButton;
use pjz9n\advancedform\menu\button\MenuButton;
use pjz9n\advancedform\menu\response\MenuFormResponse;
use pjz9n\advancedform\MessagesTrait;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_map;
use function array_merge;
use function array_search;
use function array_values;
use function count;
use function gettype;
use function implode;
use function is_int;
use function str_repeat;

/**
 * Form with buttons
 */
abstract class MenuForm extends BaseForm
{
    use MessagesTrait;

    /**
     * Called when the form is selected by the player
     * NOTE: Not called when the form is closed
     * @see MenuForm::handleClose()
     */
    abstract protected function handleSelect(Player $player, MenuFormResponse $response): void;

    /**
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     */
    public function __construct(
        string         $title,
        private string $content,
        private array  $buttons = [],
    )
    {
        parent::__construct($title);
        $this->buttons = array_values($this->buttons);
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

    /**
     * @return MenuButton[]
     * @phpstan-return list<MenuButton>
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    /**
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     */
    public function setButtons(array $buttons): self
    {
        $this->buttons = array_values($buttons);
        return $this;
    }

    public function addButton(MenuButton $button): self
    {
        $this->buttons[] = $button;
        return $this;
    }

    /**
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     */
    public function addButtons(array $buttons): self
    {
        $this->buttons = array_merge($this->buttons, array_values($buttons));
        return $this;
    }

    public function removeButton(MenuButton $button): self
    {
        if (($key = array_search($button, $this->buttons, true)) === false) {
            throw new InvalidArgumentException("Button does not exist");
        }

        unset($this->buttons[$key]);
        $this->buttons = array_values($this->buttons);

        return $this;
    }

    /**
     * Called when the form is closed by the player
     */
    protected function handleClose(Player $player): void
    {
        //NOOP
    }

    final public function handleResponse(Player $player, $data): void
    {
        if ($data === null) {
            $this->handleClose($player);
            return;
        }
        if (!is_int($data)) {
            throw new FormValidationException("Excepted int, got " . gettype($data));
        }
        $max = count($this->getButtons()) - 1;
        if ($data < 0 || $data > $max) {
            throw new FormValidationException("Excepted range 0-$max, got " . $data);
        }

        $selectedButton = $this->getButtons()[$data];

        if ($selectedButton instanceof HandlerButton && $selectedButton->handle($this, $player)) {
            return;
        }

        //TODO: Is it correct to do this implicitly?
        $this->clearMessages();

        $this->handleSelect($player, new MenuFormResponse($data, $selectedButton));
    }

    final protected function getNetworkType(): string
    {
        return FormTypes::MENU;
    }

    final protected function getNetworkData(): array
    {
        $messagesString = implode(TextFormat::EOL, array_map(fn(string $message): string => $message . TextFormat::RESET, $this->getMessages()));
        if ($messagesString !== "") {
            $messagesString .= str_repeat(TextFormat::EOL, 2);
        }
        return [
            "content" => $messagesString . $this->getContent(),
            "buttons" => $this->getButtons(),
        ];
    }
}
