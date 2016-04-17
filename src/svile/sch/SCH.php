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


use pocketmine\nbt\NBT;


class SCH
{
    /** @var SCHmain */
    private $pg;
    /** @var string */
    public $path;
    /** @var NBT */
    public $nbt;
    public $blocks;
    public $data;
    /** @var int */
    public $height = 0;
    /** @var int */
    public $length = 0;
    /** @var int */
    public $width = 0;
    /** @var array */
    public $blocks_array = [];

    /**
     * SCH constructor.
     * @param SCHmain $plugin
     * @param string $path
     */
    public function __construct(SCHmain $plugin, $path)
    {
        $this->pg = $plugin;
        $this->path = $path;

        touch($this->path);
        $this->nbt = new NBT(NBT::BIG_ENDIAN);
        $this->nbt->readCompressed(file_get_contents($path));
        $data = $this->nbt->getData();
        $this->blocks = $data->Blocks->getValue();
        $this->data = $data->Data->getValue();
        $this->height = (int)$data->Height->getValue();
        $this->length = (int)$data->Length->getValue();
        $this->width = (int)$data->Width->getValue();

        for ($z = 0; $z < $this->length; $z++) {
            for ($y = 0; $y < $this->height; $y++) {
                for ($x = 0; $x < $this->width; $x++) {
                    $i = $y * $this->width * $this->length + $z * $this->width + $x;
                    $id = $this::readByte($this->blocks, $i);
                    $damage = $this::readByte($this->data, $i);
                    switch ($id):
                        case 126:
                            $id = 158;
                            break;
                        case 125:
                            $id = 157;
                            break;
                        case 157:
                            $id = 126;
                            break;
                        case 95:
                            $id = 20;
                            $damage = 0;
                            break;
                        case 160:
                            $id = 102;
                            $damage = 0;
                            break;
                        case 188:
                            $id = 85;
                            $damage = 1;
                            break;
                        case 189:
                            $id = 85;
                            $damage = 2;
                            break;
                        case 190:
                            $id = 85;
                            $damage = 3;
                            break;
                        case 191:
                            $id = 85;
                            $damage = 4;
                            break;
                        case 192:
                            $id = 85;
                            $damage = 5;
                            break;
                    endswitch;
                    $this->blocks_array[] = [$x, $y, $z, $id, $damage];

                    //echo PHP_EOL;
                    //echo "$x:$y:$z => $i";
                }
            }
        }
    }

    private static function readByte($c, $i = 0)
    {
        $b = ord($c{$i});
        if (PHP_INT_SIZE === 8)
            return $b << 56 >> 56;
        else
            return $b << 24 >> 24;
    }
}