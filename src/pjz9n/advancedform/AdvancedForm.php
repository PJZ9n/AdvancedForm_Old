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

use Logger;
use LogicException;
use pjz9n\advancedform\chain\FormChain;
use pjz9n\advancedform\chain\FormChainListener;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginLogger;
use pocketmine\Server;

final class AdvancedForm
{
    private static bool $isRegistered = false;

    private static Logger $logger;

    public static function isRegistered(): bool
    {
        return self::$isRegistered;
    }

    public static function register(Plugin $plugin): void
    {
        if (self::isRegistered()) {
            throw new LogicException("Already registered");
        }
        FormChain::init();
        Server::getInstance()->getPluginManager()->registerEvents(new FormChainListener($plugin), $plugin);
    }

    public static function getLogger(): Logger
    {
        if (!isset(self::$logger)) {
            self::$logger = new PluginLogger(Server::getInstance()->getLogger(), "AdvancedForm");
        }
        return self::$logger;
    }

    private function __construct()
    {
    }
}
