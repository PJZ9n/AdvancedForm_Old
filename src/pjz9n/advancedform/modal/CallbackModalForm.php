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

use Closure;
use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CallbackModalForm extends ModalForm
{
    /**
     * @phpstan-param Closure(Player, bool): void $handleChoice
     */
    public static function create(
        string  $title,
        string  $content,
        Closure $handleChoice,
        string  $button1Text = "gui.yes",
        string  $button2Text = "gui.no",
    ): self
    {
        return new self($title, $content, $handleChoice, $button1Text, $button2Text);
    }

    /**
     * @phpstan-param Closure(Player, bool): void $handleChoice
     *
     * @see CallbackModalForm::create()
     */
    public function __construct(
        string          $title,
        string          $content,
        private Closure $handleChoice,
        string          $button1Text = "gui.yes",
        string          $button2Text = "gui.no",
    )
    {
        Utils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("player", Player::class),
            new ParameterType("choice", BuiltInTypes::BOOL),
        ), $this->handleChoice);
        parent::__construct($title, $content, $button1Text, $button2Text);
    }

    public function handleChoice(Player $player, bool $choice): void
    {
        ($this->handleChoice)($player, $choice);
    }
}
