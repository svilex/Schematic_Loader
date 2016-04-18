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


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\block\Block;
use pocketmine\Player;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;


class SCHmain extends PluginBase implements Listener
{
    const SCH_VERSION = 0.1;

    private $players = [];

    public function onLoad()
    {
        //Sometimes the silence operator " @ " doesn't works and the server crash, this is better.Don't ask me why, i just know that.
        if (!@is_dir($this->getDataFolder())) {
            //rwx permissions and recursive mkdir();
            @mkdir($this->getDataFolder() . "\x73\x63\x68\x65\x6d\x61\x74\x69\x63\x5f\x66\x69\x6c\x65\x73", 0755, true);
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

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(@str_replace('\n', PHP_EOL, @gzinflate(@base64_decode("pZCxDoIwFEV/pbOJdGcSI2\x47\x51uOhI0pT6bBtbSsqr0S/iP/gy0woDjvrGc8+9w2u6pptGM41i88vNZSgCKudzMo234aENLPyo7wmy\x52NmCL2BAem5Z5V3ok6EQ+yGnFOcos0BXU+XWcm2Siwp\x69\x5aGAnI8uEs4tVaVShXS3KhKL0GXzSs1BgOWrBasev4Ody+81Jb4LUHWlfJDXjLC9Pxb4uD3++7Q0="))));
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        if (strtolower($command->getName()) == 'sch') {
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

                    if ($sender instanceof Player) {
                        //Schematic file name
                        $SCHname = str_replace(/*not allowed char?*/
                            '', '', str_replace('.schematic', '', trim(array_shift($args))));

                        $path = $this->getDataFolder() . 'schematic_files/' . $SCHname . '.schematic';
                        if (is_file($path)) {
                            $sender->sendMessage('§b→ §f' . realpath($path) . '§c already exists');
                            break;
                        }
                        touch($path);

                        $this->players[$sender->getName()] = [$path];
                        $sender->sendMessage('Tap the 1st block');
                    }
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
                        $sender->sendMessage('§b→§cUsage: /sch §apaste [FileName]');
                        break;
                    }

                    $SCHname = array_shift($args);

                    $path = $this->getDataFolder() . 'schematic_files/' . $SCHname;
                    if (!is_file($path)) {
                        $sender->sendMessage('§b→ §f' . $path . '§c not found');
                        break;
                    }

                    touch($path);
                    $nbt = new NBT(NBT::BIG_ENDIAN);
                    $nbt->readCompressed(file_get_contents($path));
                    $data = $nbt->getData();
                    $blocks = $data->Blocks->getValue();
                    $data = $data->Data->getValue();
                    $height = (int)$data->Height->getValue();
                    $length = (int)$data->Length->getValue();
                    $width = (int)$data->Width->getValue();
                    $i = -1;
                    if ($sender instanceof Player)
                        $pp = $sender->getPosition()->floor()->add(1, 0, 1);
                    for ($y = 0; $y < $height; $y++) {
                        for ($z = 0; $z < $length; $z++) {
                            for ($x = 0; $x < $width; $x++) {
                                $i++;
                                $id = $this::readByte($blocks, $i);
                                $damage = $this::readByte($data, $i);
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

                                //echo "$x:$y:$z => $i".PHP_EOL;

                                if ($sender instanceof Player) {
                                    $pos = $pp->add($x, $y, $z);
                                    $sender->getLevel()->setBlock($pos, Block::get($id, $damage), false, false);
                                    $sender->getLevel()->setBlockLightAt($pos->x, $pos->y, $pos->z, 15);
                                }
                            }
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
        return true;
    }

    public function onInteract(PlayerInteractEvent $ev)
    {
        if ($ev->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK)
            return;
        if (array_key_exists($ev->getPlayer()->getName(), $this->players)) {
            switch (count($this->players[$ev->getPlayer()->getName()])) {
                case 1:
                    $this->players[$ev->getPlayer()->getName()][] = [$ev->getBlock()->x, $ev->getBlock()->y, $ev->getBlock()->z];
                    $ev->getPlayer()->sendMessage('Tap the block 2');
                    break;
                case 2:
                    $this->players[$ev->getPlayer()->getName()][] = [$ev->getBlock()->x, $ev->getBlock()->y, $ev->getBlock()->z];
                    $ev->getPlayer()->sendMessage('Creating...');
                    $this->createSch($this->players[$ev->getPlayer()->getName()], $ev->getPlayer());
                    unset($this->players[$ev->getPlayer()->getName()]);
                    break;
                default:
                    unset($this->players[$ev->getPlayer()->getName()]);
                    break;
            }
        }
    }

    private function createSch($c, Player $sender)
    {
        $path = array_shift($c);

        $pos1 = new Vector3($c[0][0], $c[0][1], $c[0][2]);
        $pos2 = new Vector3($c[1][0], $c[1][1], $c[1][2]);

        $h = max($pos1->y, $pos2->y) - min($pos1->y, $pos2->y);
        $l = max($pos1->z, $pos2->z) - min($pos1->z, $pos2->z);
        $w = max($pos1->x, $pos2->x) - min($pos1->x, $pos2->x);

        $blocks = '';
        $data = '';

        for ($y = 0; $y < $h; $y++) {
            for ($z = 0; $z < $l; $z++) {
                for ($x = 0; $x < $w; $x++) {
                    $block = $sender->getLevel()->getBlock($pos1->add($x, $y, $z));
                    $id = $this::writeByte($block->getId());
                    $damage = $this::writeByte($block->getDamage());
                    $blocks .= $id;
                    $data .= $damage;
                }
            }
        }

        $nbt = new NBT(NBT::BIG_ENDIAN);
        $nbt->setData(new CompoundTag
        ('Schematic', [
            new ByteArrayTag('Blocks', $blocks),
            new ByteArrayTag('Data', $data),
            new ShortTag('Height', $h),
            new ShortTag('Length', $l),
            new ShortTag('Width', $w),
            new StringTag('Materials', 'Alpha')
        ]));

        file_put_contents($path, $nbt->writeCompressed());

        if (is_file($path))
            $sender->sendMessage('§b→ §f' . realpath($path) . '§a created successfully');
        else
            $sender->sendMessage('§b→§cI can\'t find §f ' . $path . '§c i\'ve got write access?');
    }

    private static function readByte($c, $i = 0)
    {
        $b = ord($c{$i});
        if (PHP_INT_SIZE === 8)
            return $b << 56 >> 56;
        else
            return $b << 24 >> 24;
    }

    //useless ? I don't care
    private static function writeByte($c)
    {
        return chr($c);
    }
}