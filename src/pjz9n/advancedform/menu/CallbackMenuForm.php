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

use Closure;
use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pjz9n\advancedform\menu\button\MenuButton;
use pjz9n\advancedform\menu\response\MenuFormResponse;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CallbackMenuForm extends MenuForm
{
    /**
     * @phpstan-param Closure(Player, MenuFormResponse): void $handleSelect
     * @phpstan-param Closure(Player): void $handleClose
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     */
    public function __construct(
        string           $title,
        string           $content,
        private Closure  $handleSelect,
        private ?Closure $handleClose = null,
        array            $buttons = [],
    )
    {
        Utils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("player", Player::class),
            new ParameterType("response", MenuFormResponse::class)),
            $this->handleSelect,
        );
        if ($this->handleClose !== null) {
            Utils::validateCallableSignature(new CallbackType(
                new ReturnType(BuiltInTypes::VOID),
                new ParameterType("player", Player::class),
            ), $this->handleClose);
        }

        parent::__construct($title, $content, $buttons);
    }

    protected function handleSelect(Player $player, MenuFormResponse $response): void
    {
        ($this->handleSelect)($player, $response);
    }

    protected function handleClose(Player $player): void
    {
        if ($this->handleClose !== null) {
            ($this->handleClose)($player);
        }
    }
}
