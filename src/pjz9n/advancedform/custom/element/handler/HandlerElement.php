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

namespace pjz9n\advancedform\custom\element\handler;

use pjz9n\advancedform\custom\response\CustomFormResponse;
use pocketmine\form\Form;
use pocketmine\player\Player;

interface HandlerElement
{
    /**
     * Handle element
     * Returns true if CustomForm::handleSubmit() does not need to be called (if the handle is successful)
     */
    public function handle(Form $form, Player $player, CustomFormResponse $response): bool;
}