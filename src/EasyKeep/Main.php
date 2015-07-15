<?php

namespace EasyKeep;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		$this->getLogger()->info(TextFormat::BLUE . "EasyKeep enabled");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->enabled = true;
	}
	public function onDisable(){
		$this->getLogger()->info(TextFormat::RED . "EasyKeep disabled");
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if(strtolower($command->getName()) === "ek"){
			if(!($sender->hasPermission("easykeep") || $sender->hasPermission("easykeep.enable"))){
				$sender->sendMessage(TextFormat::RED . "You don't have permission to use that command");
				return true;
			}else{
				if(!(isset($args[0]))){
					return false;
				}else{
					if($args[0] == "on"){
						if($this->enabled == true){
							$sender->sendMessage("EasyKeep is already enabled!");
							return true;
						}else{
							$this->enabled = true;
							$sender->sendMessage("EasyKeep enabled");
							$this->getLogger()->info("EasyKeep was enabled by " . $sender->getName());
							return true;
						}
					}elseif($args[0] == "off"){
						if($this->enabled == true){
							$this->enabled = false;
							$sender->sendMessage("EasyKeep disabled");
							$this->getLogger()->info("EasyKeep was disabled by " . $sender->getName());
							return true;
						}else{
							$sender->sendMessage("EasyKeep is already disabled!");
							return true;
						}
					}else{
						$sender->sendMessage("Unknown subcommand: " . $args[0]);
						return false;
					}
				}
			}
		}
	}
		
	public function onPlayerDeathEvent(PlayerDeathEvent $event){
		$player = $event->getEntity();
		if($player->hasPermission("easykeep") || $player->hasPermission("easykeep.keep")){
			if($this->enabled == true){
				$event->setKeepInventory(true);
				$player->sendMessage(TextFormat::YELLOW . "Your inventory has been saved!");
			}else{
				$event->setKeepInventory(false);
			}
		}
	}
}
