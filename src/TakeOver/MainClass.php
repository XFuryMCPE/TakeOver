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

namespace TakeOver;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\utils\TextFormat;

use TakeOver\commands\TakeOver;

class MainClass extends PluginBase implements Listener{

	public $takeOvers = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register("takeover",new TakeOver($this,"takeover","Takeover a player!"));
	}

	public function onMove(PlayerMoveEvent $e){
		$p = $e->getPlayer();
		if(isset($this->takeOvers[$p->getName()])){
			$e->setCancelled();
			return;
		}
		foreach($this->getServer()->getOnlinePlayers() as $pl){
			if(isset($this->takeOvers[$pl->getName()])){
				if($this->takeOvers[$pl->getName()] == $p->getName()){
					$pl->teleport($p,$p->yaw,$p->pitch);
					$pl->hidePlayer($p);
					$p->hidePlayer($pl);
				}
			}
		}
	}

	public function onQuit(PlayerQuitEvent $e){
		$p = $e->getPlayer();
		if(isset($this->takeOvers[$p->getName()])){
			$target = $this->getServer()->getPlayer($this->takeOvers[$p->getName()]);
			$target->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
			$target->sendMessage(TextFormat::GREEN."The player you tookover has left the server!");
			unset($this->takeOvers[$p->getName()]);
			return;
		}
		foreach($this->getServer()->getOnlinePlayers() as $pl){
			if(isset($this->takeOvers[$pl->getName()])){
				if($this->takeOvers[$pl->getName()] == $p->getName()){
					$p->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
					unset($this->takeOvers[$pl->getName()]);
				}
			}
		}
	}
}