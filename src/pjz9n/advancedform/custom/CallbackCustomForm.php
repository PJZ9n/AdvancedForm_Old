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

namespace pjz9n\advancedform\custom;

use Closure;
use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pjz9n\advancedform\custom\element\Element;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CallbackCustomForm extends CustomForm
{
    /**
     * @phpstan-param Closure(Player, CustomFormResponse): void $handleSubmit
     * @phpstan-param Closure(Player): void $handleClose
     * @param Element[]
     * @phpstan-param array<mixed, Element>
     */
    public function __construct(
        string           $title,
        private Closure  $handleSubmit,
        private ?Closure $handleClose = null,
        array            $elements = [],
    )
    {
        Utils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("player", Player::class),
            new ParameterType("response", CustomFormResponse::class)),
            $this->handleSubmit,
        );
        if ($this->handleClose !== null) {
            Utils::validateCallableSignature(new CallbackType(
                new ReturnType(BuiltInTypes::VOID),
                new ParameterType("player", Player::class),
            ), $this->handleClose);
        }
        parent::__construct($title, $elements);
    }

    protected function handleSubmit(Player $player, CustomFormResponse $response): void
    {
        ($this->handleSubmit)($player, $response);
    }

    protected function handleClose(Player $player): void
    {
        if ($this->handleClose !== null) {
            ($this->handleClose)($player);
        }
    }
}
