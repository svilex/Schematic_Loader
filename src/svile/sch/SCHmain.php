<?php

/*
 *                _   _
 *  ___  __   __ (_) | |   ___
 * / __| \ \ / / | | | |  / _ \
 * \__ \  \ / /  | | | | |  __/
 * |___/   \_/   |_| |_|  \___|
 *
 * Schematic Loader plugin for PocketMine-MP & forks
 *
 * @Author: svile
 * @Kik: _svile_
 * @Telegram_Gruop: https://telegram.me/svile
 * @E-mail: thesville@gmail.com
 * @Github: https://github.com/svilex/Schematic_Loader
 *
 * Copyright (C) 2016 svile
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * DONORS LIST :
 * - no one
 * - no one
 * - no one
 *
 */

namespace svile\sch;


use pocketmine\plugin\PluginBase;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;


class SCHmain extends PluginBase
{
    const SCH_VERSION = 0.1;

    /** @var SCHcommands */
    private $commands;

    public function onLoad()
    {
        //Sometimes the silence operator " @ " doesn't works and the server crash, this is better.Don't ask me why, i just know that.
        if (!@is_dir($this->getDataFolder())) {
            //rwx permissions and recursive mkdir();
            @mkdir($this->getDataFolder() . "\x73\x63\x68\x65\x6d\x61\x74\x69\x63\x5f\x66\x69\x6c\x65\x73", 0755, true);
            @mkdir($this->getDataFolder() . "\x63\x72\x65\x61\x74\x65\x64\x5f\x73\x63\x68\x65\x6d\x61\x74\x69\x63\x73", 0755);
        }
    }

    public function onEnable()
    {
        if ($this->getDescription()->getVersion() != self::SCH_VERSION) {
            $this->getLogger()->critical(@gzinflate(@base64_decode('C8lILUpVyCxWSFQoKMpPyknNVSjPLMlQKMlIVSjIKU3PzFMoSy0qzszPAwA=')));
        }
        if (@array_shift($this->getDescription()->getAuthors()) != "\x73\x76\x69\x6c\x65" or $this->getDescription()->getName() != "\x53\x63\x68\x65\x6d\x61\x74\x69\x63\x5f\x4c\x6f\x61\x64\x65\x72" or $this->getDescription()->getVersion() != self::SCH_VERSION) {
            $this->getLogger()->notice(@gzinflate(@base64_decode('LYxBDsIwDAS/sg8ozb1/QEICiXOo3NhKiKvYqeD3hcJtNaPZGxNid9YGXeAshrX0JBWfZZsUGrCJif9ckZrhikRfQGgUyz+YwO6rTSEkce6PcdZnOB5e4Zrf99jsdNE5k5+l0g4=')));
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        //svile\sch\SCHcommands object
        $this->commands = new SCHcommands($this);

        //Register timer and listener
        //$this->getServer()->getScheduler()->scheduleRepeatingTask(new SCHtimer($this), 19);
        $this->getServer()->getPluginManager()->registerEvents(new SCHlistener($this), $this);

        $this->getLogger()->info(@str_replace('\n', PHP_EOL, @gzinflate(@base64_decode("pZCxDoIwFEV/pbOJdGcSI2\x47\x51uOhI0pT6bBtbSsqr0S/iP/gy0woDjvrGc8+9w2u6pptGM41i88vNZSgCKudzMo234aENLPyo7wmy\x52NmCL2BAem5Z5V3ok6EQ+yGnFOcos0BXU+XWcm2Siwp\x69\x5aGAnI8uEs4tVaVShXS3KhKL0GXzSs1BgOWrBasev4Ody+81Jb4LUHWlfJDXjLC9Pxb4uD3++7Q0="))));
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        if (strtolower($command->getName()) == 'sch') {
            //If SCH command, just call svile\sch\SCHcommands->onCommand();
            $this->commands->onCommand($sender, $command, $label, $args);
        }
        return true;
    }

    /*
                      _
       __ _   _ __   (_)
      / _` | | '_ \  | |
     | (_| | | |_) | | |
      \__,_| | .__/  |_|
             |_|

    */
}