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
use pocketmine\plugin\PluginBase;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;

use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\Server;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;

//use pocketmine\nbt\tag\ByteArray;
//use pocketmine\nbt\tag\Compound;
//use pocketmine\nbt\tag\Short;
//use pocketmine\nbt\tag\String;


class SCHmain extends PluginBase implements Listener
{
    const SCH_VERSION = 0.1;

    private $players = [];
    private $tobesent = [];
    private $set = [];

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
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new SCHscheduler($this), 21);

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
                            '', '', str_replace('.schematic', '', array_shift($args)));

                        $path = $this->getDataFolder() . 'schematic_files/' . $SCHname . '.schematic';
                        if (is_file($path)) {
                            $sender->sendMessage('§b→ §f' . realpath($path) . '§c already exists');
                            break;
                        }
                        touch($path);

                        $this->players[$sender->getName()] = [$path];
                        $sender->sendMessage('§cBreak the 1st block');
                    }
                    break;


                case 'load':
                    if (count($args) < 1 or count($args) > 2) {
                        $sender->sendMessage('§b→§cUsage: /sch §aload [FileName] §7[seconds]');
                        break;
                    }

                    $SCHname = str_replace('.schematic', '', array_shift($args));

                    $path = $this->getDataFolder() . 'schematic_files/' . $SCHname . '.schematic';
                    if (!is_file($path)) {
                        $sender->sendMessage('§b→ §f' . $path . '§c not found');
                        break;
                    }

                    if (!empty($args)) {
                        $seconds = array_shift($args);
                        if (!is_numeric($seconds)) {
                            $sender->sendMessage('§b→§c [seconds] must be an int.An Higher value means less lag');
                        }
                        $seconds = $seconds + 0;
                        $seconds <= 0 ? $seconds = 5 : $seconds = $seconds + 0;
                    } else {
                        $seconds = 5;
                    }

                    touch($path);
                    $nbt = new NBT(NBT::BIG_ENDIAN);
                    $nbt->readCompressed(file_get_contents($path));
                    $dataa = $nbt->getData();
                    $blocks = $dataa->Blocks->getValue();
                    $data = $dataa->Data->getValue();
                    $height = (int)$dataa->Height->getValue();
                    $length = (int)$dataa->Length->getValue();
                    $width = (int)$dataa->Width->getValue();
                    $i = -1;
                    $sblocks = [];
                    if ($sender instanceof Player) {
                        $yaw = (int)floor($sender->getYaw());
                        $xa = 1;
                        $za = 1;
                        $ax = 1;
                        $az = 1;
                        if ($yaw > 0) {
                            $ax = -$xa;
                            $az = $za;
                        }
                        if ($yaw > 90) {
                            $ax = -$xa;
                            $az = -$za;
                        }
                        if ($yaw > 180) {
                            $ax = $xa;
                            $az = -$za;
                        }
                        if ($yaw > 270) {
                            $ax = $xa;
                            $az = $za;
                        }
                        $pp = $sender->getPosition()->floor()->add($ax, 0, $az);
                    }
                    for ($y = 0; $y < $height; $y++) {
                        for ($z = 0; $z < $length; $z++) {
                            for ($x = 0; $x < $width; $x++) {
                                $i++;
                                $id = self::readByte($blocks, $i);
                                $damage = self::readByte($data, $i);
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

                                if ($sender instanceof Player) {
                                    if ($yaw > 270) {
                                        $ax = $x;
                                        $az = $z;
                                    } elseif ($yaw > 180) {
                                        $ax = $x;
                                        $az = -$z;
                                        $damage = self::rotate180($id, $damage);
                                    } elseif ($yaw > 90) {
                                        $ax = -$x;
                                        $az = -$z;
                                        $damage = self::rotate90($id, $damage);
                                    } elseif ($yaw > 0) {
                                        $ax = -$x;
                                        $az = $z;
                                        $damage = self::rotate0($id, $damage);
                                    }
                                    $pos = $pp->add($ax, $y, $az);
                                    if ($pos->y > 128) break 3;
                                    //if (!$sender->getLevel()->isChunkLoaded($pos->x, $pos->z))
                                    //   $sender->getLevel()->loadChunk($pos->x, $pos->z, true);
                                    //$sender->getLevel()->setBlock($pos, Block::get($id, $damage), false, false);
                                    //$sender->getLevel()->setBlockLightAt($pos->x, $pos->y, $pos->z, 15);
                                    $sblocks[] = ['x' => $pos->x, 'y' => $pos->y, 'z' => $pos->z, 'id' => $id, 'damage' => $damage];
                                }
                            }
                        }
                    }
                    if ($sender instanceof Player) {
                        $c = (int)floor((($width * $length * $height) / $seconds));
                        $this->tobesent[$sender->getName()] = array_chunk($sblocks, $c);
                        $this->set[$sender->getName()] = $sblocks;
                    }
                    unset($sblocks);
                    $sender->sendMessage('§aLoaded!');
                    break;


                case 'paste':
                    if (!empty($args)) {
                        $sender->sendMessage('§b→§cUsage: /sch paste');
                        break;
                    }

                    if ($sender instanceof Player) {
                        if (!array_key_exists($sender->getName(), $this->set)) {
                            $sender->sendMessage('§b→§cBlocks not found, try §f/sch load');
                            break;
                        }
                        $blocks = $this->set[$sender->getName()];

                        foreach ($blocks as $block) {
                            if (!$sender->getLevel()->isChunkLoaded($block['x'], $block['z']))
                                $sender->getLevel()->loadChunk($block['x'], $block['z'], true);
                            $sender->getLevel()->setBlockIdAt($block['x'], $block['y'], $block['z'], $block['id']);
                            $sender->getLevel()->setBlockDataAt($block['x'], $block['y'], $block['z'], $block['damage']);
                            $sender->getLevel()->setBlockLightAt($block['x'], $block['y'], $block['z'], 15);
                        }

                        unset($this->set[$sender->getName()], $blocks);
                        if (array_key_exists($sender->getName(), $this->tobesent))
                            unset($this->tobesent[$sender->getName()]);

                        $sender->sendMessage('§aSchematic pasted successfully!');
                    }
                    break;


                default:
                    //No option found, usage
                    $sender->sendMessage('§b→§cUsage: /sch [create|load|paste]');
                    break;


            endswitch;
            return true;
        }
        return true;
    }

    public function onBlockBreak(BlockBreakEvent $ev)
    {
        if (array_key_exists($ev->getPlayer()->getName(), $this->players)) {
            $ev->setCancelled();
            switch (count($this->players[$ev->getPlayer()->getName()])) {
                case 1:
                    $this->players[$ev->getPlayer()->getName()][] = [$ev->getBlock()->x, $ev->getBlock()->y, $ev->getBlock()->z];
                    $ev->getPlayer()->sendMessage('§cBreak the block 2');
                    break;
                case 2:
                    $this->players[$ev->getPlayer()->getName()][] = [$ev->getBlock()->x, $ev->getBlock()->y, $ev->getBlock()->z];
                    $ev->getPlayer()->sendMessage('§aCreating...');
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

        $h = max($pos1->y, $pos2->y) - min($pos1->y, $pos2->y) + 1;
        $l = max($pos1->z, $pos2->z) - min($pos1->z, $pos2->z) + 1;
        $w = max($pos1->x, $pos2->x) - min($pos1->x, $pos2->x) + 1;

        $pos1->x < $pos2->x ? $minx = $pos1->x : $minx = $pos2->x;
        $pos1->y < $pos2->y ? $miny = $pos1->y : $miny = $pos2->y;
        $pos1->z < $pos2->z ? $minz = $pos1->z : $minz = $pos2->z;
        $origin = new Vector3($minx, $miny, $minz);

        $blocks = '';
        $data = '';

        for ($y = 0; $y < $h; $y++) {
            for ($z = 0; $z < $l; $z++) {
                for ($x = 0; $x < $w; $x++) {
                    $block = $sender->getLevel()->getBlock($origin->add($x, $y, $z));
                    $id = $block->getId();
                    $damage = $block->getDamage();

                    switch ($id):
                        case 158:
                            $id = 126;
                            break;
                        case 157:
                            $id = 125;
                            break;
                        case 126:
                            $id = 157;
                            break;
                        case 85:
                            switch ($damage) {
                                case 1:
                                    $id = 188;
                                    $damage = 0;
                                    break;
                                case 2:
                                    $id = 189;
                                    $damage = 0;
                                    break;
                                case 3:
                                    $id = 190;
                                    $damage = 0;
                                    break;
                                case 4:
                                    $id = 191;
                                    $damage = 0;
                                    break;
                                case 5:
                                    $id = 192;
                                    $damage = 0;
                                    break;
                                default:
                                    $damage = 0;
                                    break;
                            }
                            break;
                    endswitch;

                    $blocks .= self::writeByte($id);
                    $data .= self::writeByte($damage);
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
        return ord($c{$i});
    }

    private static function writeByte($c)
    {
        return chr($c);
    }

    public function sendBlocks(array $target, array $blocks, $flags = UpdateBlockPacket::FLAG_ALL_PRIORITY)
    {
        $pk = new UpdateBlockPacket();
        foreach ($blocks as $b) {
            $pk->records[] = [$b['x'], $b['z'], $b['y'], $b['id'], $b['damage'], $flags];
        }
        Server::broadcastPacket($target, $pk);
    }

    public function tick()
    {
        foreach ($this->tobesent as $playername => &$chunks) {
            $player = $this->getServer()->getPlayerExact($playername);
            if ($player instanceof Player) {
                $blocks = array_shift($chunks);
                self::sendBlocks([$player], $blocks);
                if (empty($this->tobesent[$playername])) {
                    $player->sendMessage('§aHologram pasted successfully! §f/sch paste §ato save changes');
                    unset($this->tobesent[$playername]);
                }
            } else {
                unset($this->tobesent[$playername]);
            }
        }
    }


    /**
     * @param int $id
     * @param int $damage
     * @return int
     */
    private static function rotate180(int $id, int $damage) : int
    {
        switch ($id) {
            case 1:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 2:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 3:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 4:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
        }
        return $damage;
    }

    /**
     * @param int $id
     * @param int $damage
     * @return int
     */
    private static function rotate90(int $id, int $damage) : int
    {
        switch ($id) {
            case 1:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 2:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 3:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 4:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
        }
        return $damage;
    }

    /**
     * @param int $id
     * @param int $damage
     * @return int
     */
    private static function rotate0(int $id, int $damage) : int
    {
        switch ($id) {
            case 1:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 2:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 3:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
            case 4:
                switch ($damage) {
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                    case 4:
                        break;
                }
                break;
        }
        return $damage;
    }
}