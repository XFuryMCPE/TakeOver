<?php

/*    ___                 
 *   / __\   _ _ __ _   _ 
 *  / _\| | | | '__| | | |
 * / /  | |_| | |  | |_| |
 * \/    \__,_|_|   \__, |
 *                  |___/
 *
 * No copyright 2016 blahblah
 * Plugin made by fury and is FREE SOFTWARE
 * Do not sell or i will sue you lol
 * but fr tho I will sue ur face
 * DO NOT SELL
 */

namespace TakeOver\commands;

use TakeOver\MainClass;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;
use pocketmine\Player;

class TakeOver extends Command implements PluginIdentifiableCommand{

	public function __construct(MainClass $plugin,$name,$description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("takeover.cmd");
	}

	public function execute(CommandSender $sender, $label, array $args){
		if(!$sender->hasPermission("takeover.cmd")){
			$sender->sendMessage(TextFormat::RED."You do not have permission to use this command!");
			return;
		}
		if(count($args) != 1){
			$sender->sendMessage(TextFormat::RED."Usage: /takeover <player:stop>");
			return;
		}
		if(strtolower($args[0]) == "stop"){
			$search = false;
			$toname = null;
			foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
				if(isset($this->plugin->takeOvers[$p->getName()])){
					if($this->plugin->takeOvers[$p->getName()] == $sender->getName()){
						unset($this->plugin->takeOvers[$p->getName()]);
						$p->showPlayer($sender);
						$sender->showPlayer($p);
						$sender->teleport($this->plugin->getServer()->getDefaultLevel()->getSpawnLocation());
						$search = true;
						$toname = $p->getName();
					}
				}
			}
			switch($search){
				case true:
					$sender->sendMessage(TextFormat::GREEN."You stopped taking over ".$p->getName()."!");
				break;
				case false:
					$sender->sendMessage(TextFormat::RED."You are not taking over anyone!");
				break;
			}
			return;
		}
		$target = $this->plugin->getServer()->getPlayer($args[0]);
		if(!$target instanceof Player){
			$sender->sendMessage(TextFormat::RED."Player not found!");
			return;
		}
		if($target == $sender){
			$sender->sendMessage(TextFormat::RED."You cannot takeover yourself!");
			return;
		}
		if(isset($this->plugin->takeOvers[$sender->getName()])){
			$sender->sendMessage(TextFormat::RED."You cannot takeover while being taken over!");
			return;
		}
		foreach($this->plugin->getServer()->getOnlinePlayers() as $pl){
			if(isset($this->plugin->takeOvers[$pl->getName()])){
				$sender->sendMessage(TextFormat::RED."This player is already being taken over!");
				return;
			}
		}
		$this->plugin->takeOvers[$target->getName()] = $sender->getName();
		$sender->sendMessage(TextFormat::RED."You are now taking over ".$target->getName()."!");
		$sender->teleport($target);
	}

	public function getPlugin(){
		return $this->plugin;
	}
}