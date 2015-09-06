<?php

namespace EasyKeep;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
	public function onEnable(){
		$this->getLogger()->info(TextFormat::YELLOW."Loading...");
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if($this->getConfig()->get("AllEnableStart") == "true"){
			$this->allEnabled = "true";
		}else{
			$this->allEnabled = "false";
		}
		$this->playersEnabled = array();
		$this->getLogger()->info(TextFormat::GREEN."Done!");
	}
	public function onDisable(){
		$this->getLogger()->info(TextFormat::RED."EasyKeep disabled");
	}
	
	public function onPlayerJoinEvent(PlayerJoinEvent $event){
		$name = $event->getPlayer()->getName();
		if($this->getConfig()->get("EnableOnJoin") == "true"){
			if(!(in_array($name, $this->playersEnabled))){
				$this->playersEnabled[$name] = $name;
			}
		}
	}
	
	public function onPlayerQuitEvent(PlayerQuitEvent $event){
		$name = $event->getPlayer()->getName();
		if(in_array($name, $this->playersEnabled)){
			if($this->getConfig()->get("DisableOnLeave") == "true"){
				unset($this->playersEnabled[$name]);
			}
		}
	}
	
	public function onPlayerDeathEvent(PlayerDeathEvent $event){
		if($event->getEntity() instanceof Player){
			$player = $event->getEntity();
			$name = $event->getEntity()->getName();
			if($this->allEnabled == "true"){
				$event->setKeepInventory(true);
				$player->sendMessage(TextFormat::YELLOW."Your inventory has been saved");
			}else{
				if(in_array($name, $this->playersEnabled)){
					$event->setKeepInventory(true);
					$player->sendMessage(TextFormat::YELLOW."Your inventory has been saved");
				}
			}
		}
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if(strtolower($command->getName()) == "ek"){
			if(isset($args[0])){
				if(isset($args[1])){
					if($sender->hasPermission("ek") || $sender->hasPermission("ek.toggle") || $sender->hasPermission("ek.toggle.all")){
						if($args[1] == "all"){
							if($args[0] == "on"){
								if($this->allEnabled == "true"){
									$sender->sendMessage("EasyKeep is already enabled for everyone");
									return true;
								}else{
									$this->allEnabled = "true";
									$this->getServer()->broadcastMessage(TextFormat::YELLOW."EasyKeep has been enabled for everyone");
									return true;
								}
							}elseif($args[0] == "off"){
								if($this->allEnabled == "false"){
									$sender->sendMessage("EasyKeep isn't enabled for everyone");
									return true;
								}else{
									$this->allEnabled = "true";
									$this->getServer()->broadcastMessage(TextFormat::YELLOW."EasyKeep has been disabled for everyone");
									foreach($this->getServer()->getOnlinePlayers() as $p){
										if(in_array($p->getName(), $this->playersEnabled)){
											unset($this->playersEnabled[$p->getName()]);
										}
									}
								}
							}else{
								$sender->sendMessage("Unknown subcommand: ".$args[0]);
								return false;
							}
						}
					}else{
						$sender->sendMessage("You don't have permission to toggle EasyKeep for everyone");
						return true;
					}
				}else{
					if($sender->hasPermission("ek") || $sender->hasPermission("ek.toggle")){
						if($args[0] == "on"){
							if(in_array($sender->getName(), $this->playersEnabled)){
								$sender->sendMessage("EasyKeep is already enabled for you");
								return true;
							}else{
								$this->playersEnabled[$sender->getName()] = $sender->getName();
								$sender->sendMessage(TextFormat::YELLOW."You have enabled EasyKeep for yourself.");
								return true;
							}
						}elseif($args[0] == "off"){
							if(in_array($sender->getName(), $this->playersEnabled)){
								unset($this->playersEnabled[$sender->getName()]);
								$sender->sendMessage(TextFormat::YELLOW."You have disabled EasyKeep for yourself.");
								return true;
							}else{
								$sender->sendMessage("You don't have EasyKeep enabled. To enable it, Use ".TextFormat::YELLOW."/ek on");
								return true;
							}
						}else{
							$sender->sendMessage("Unknown subcommand: ".$args[0]);
							return false;
						}
					}else{
						$sender->sendMessage("You don't have permission to toggle EasyKeep");
						return true;
					}
				}
			}else{
				return false;
			}
		}
	}
}
