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


use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\block\Block;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;


class SCHcommands
{
    /** @var SCHmain */
    private $pg;

    /**
     * SCHcommands constructor.
     * @param SCHmain $plugin
     */
    public function __construct(SCHmain $plugin)
    {
        $this->pg = $plugin;
    }


    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool
    {
        //Searchs for a valid option
        switch (strtolower(array_shift($args))):


            case 'create':
                /*
                                          _
                  ___  _ __   ___   __ _ | |_   ___
                 / __|| '__| / _ \ / _` || __| / _ \
                | (__ | |   |  __/| (_| || |_ |  __/
                 \___||_|    \___| \__,_| \__| \___|

                */
                if (count($args) != 1) {
                    $sender->sendMessage('§b→§cUsage: /sch§a create [SCHname]');
                    break;
                }

                //Schematic file name
                $SCHname = array_shift($args);

                $path = $this->pg->getDataFolder() . 'created_schematics/' . $SCHname . '.schematic';
                touch($path);

                $nbt = new NBT(NBT::BIG_ENDIAN);
                $nbt->setData(new CompoundTag
                ('Schematic', [
                    new ByteArrayTag('Blocks', chr(1)),
                    new ByteArrayTag('Data', chr(0)),
                    new ShortTag('Height', 1),
                    new ShortTag('Length', 1),
                    new ShortTag('Width', 1),
                    new StringTag('Materials', 'Alpha')
                ]));

                file_put_contents($path, $nbt->writeCompressed());

                if (is_file($path))
                    $sender->sendMessage('§b→ §f' . realpath($path) . '§a created successfully');
                else
                    $sender->sendMessage('§b→§cI can\'t find §f ' . $path . '§c i\'ve got write access?');
                break;


            case 'paste':
                /*
                                       _
                 _ __     __ _   ___  | |_    ___
                | '_ \   / _` | / __| | __|  / _ \
                | |_) | | (_| | \__ \ | |_  |  __/
                | .__/   \__,_| |___/  \__|  \___|
                |_|

                */
                if (count($args) != 1) {
                    $sender->sendMessage('§b→§cUsage: /sch §a[FileName]');
                    break;
                }

                $SCHname = array_shift($args);

                $path = $this->pg->getDataFolder() . 'schematic_files/' . $SCHname;
                if (!is_file($path)) {
                    $sender->sendMessage('§b→ §f' . $path . '§c not found');
                    break;
                }

                $schematic = new SCH($this->pg, $path);

                if ($sender instanceof Player) {
                    foreach ($schematic->blocks_array as $block) {
                        $pos = $sender->getPosition()->add($block[0], $block[1], $block[2]);
                        $sender->getLevel()->setBlock($pos, Block::get($block[3], $block[4]), false, false);
                        $sender->getLevel()->setBlockLightAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ(), 15);
                    }
                }

                $sender->sendMessage('§b→ §f' . realpath($path) . '§a pasted successfully');
                break;


            default:
                //No option found, usage
                $sender->sendMessage('§b→§cUsage: /sch [create|paste]');
                break;


        endswitch;
        return true;
    }
}