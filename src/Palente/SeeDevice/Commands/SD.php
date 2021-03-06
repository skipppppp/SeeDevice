<?php
/*
 * SeeDevice is a plugin working under the software pmmp
 *  Copyright (C) 2020  Palente

 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Palente\SeeDevice\Commands;
use Palente\SeeDevice\SeeDevice;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
class SD extends Command {
    private $plugin;
    private $format;
    public function __construct(string $name, SeeDevice $caller){
        parent::__construct(
            $name,
            "See the device/OS of a player",
            "/seedevice [player]",
            ["sd"]
            );
        $this->setPermission("SeeDevice.command.sd");
        $this->plugin = $caller;
        $this->format = $caller->getSDCFormat();
    }
    public function execute(CommandSender $sender, $command, array $args){
        $pr = SeeDevice::$prefix;
        if(!$this->testPermission($sender))return;
        if(!$this->plugin->seeDeviceCommandEnabled)return;
        if(count($args) == 0){
            //Why does a console want to see his device.
            if(!$sender instanceof Player) return;
            if(!$this->plugin->getPlayerOs($sender) OR !$this->plugin->getPlayerDevice($sender)){
                $sender->sendMessage($pr."§4What Happened, i can't get your OS! try again later! ");
                return;
            }
            $sender->sendMessage($pr.$this->replaceFormat($sender));
            return;
        }else{
            $pl = $this->plugin->getServer()->getPlayer($args[0]);
            if(!$pl instanceof Player){$sender->sendMessage($pr."§4ERROR: §fThe player with the name \"$args[0]\" seem to don't be §aONLINE!"); return;}
            if(!$this->plugin->getPlayerOs($pl) OR !$this->plugin->getPlayerDevice($pl)){
                $sender->sendMessage($pr."This player has some problem with SeeDevice, try again later!");
                return;
            }
            $sender->sendMessage($pr.$this->replaceFormat($pl));
            return;
        }
    }

    /**
     * @param Player $player
     * @return string
     * replace the tags with real text! (Magic function)
     */
    private function replaceFormat(Player $player) : string{
        $format = $this->format;
        $format = str_replace("%name%", $player->getName(), $format);
        $format = str_replace("%os%", $this->plugin->getPlayerOs($player) , $format);
        $format = str_replace("%fakeos%", $this->plugin->getFakeOs($player), $format);
        $format = str_replace("%device%", $this->plugin->getPlayerDevice($player), $format);
        $format = str_replace("%ip%", $player->getAddress(), $format);
        return $format;
    }
}