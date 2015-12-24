<?php
namespace uc;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class UtterCaution extends PluginBase {

  public function onEnable(){}
  public function onDisable(){}
  
  
  /**
  * Ughh.. This function tho -.-
  *
  * @param CommandSender $sender
  * @param Command $command
  * @param string $label
  * @param array $args
  *
  * @return bool
  */
  public function onCommand(CommandSender $sender, Command $command, $label, array $args){
    if(strtolower($command->getName()) === 'warn'){
      if(count($args) <= 1){
        $sender->sendMessage(TF::RED."Usage: ".TF::GRAY."/warn <player> <...reason>");
        return true;
      }
    
      $player = $this->getServer()->getPlayer($args[0]);
      if(!$player instanceof Player){
        $sender->sendMessage(self::format("Player '$args[0]' could not be found"));
        return true;
      }
      if($player->hasPermission("uc.warn.exempt")){
        $sender->sendMessage(self::format($player->getName()" could not be warned!"));
        return true;
      }
      $reason = array_shift($args);
      
      $this->warn($player, $sender, $reason, true); // Target, Who warned?, For what?, Should i anounce it?
      
      $sender->sendMessage(self::format("Warning for {$player->getName()} has been expressed!"));
      return true; // Success don't send usage!
    }
    return true;
  }
  
  /**
  * 
  * @param Player $player
  * @param Player $target
  * @param string $reason
  * @param bool $anounce
  */
  public function warn(Player $player, Player $target, $reason, $anounce = true){
    if($anounce){
      $output = TF::GOLD."> ".TF::GRAY;
      $output .= "Player ".TF::GOLD.$target->getName().TF::GOLD." has been warned by ".TF::GOLD.$player->getName().TF::GRAY."!";
      if($reason != "") $output .= " Reason: '$reason'";
      $this->getServer()->broadcastMessage($output);
      return true;
    } else {
      $target->sendMessage(TF::GRAY."[".TF::DARK_RED."WARNING".TF::GRAY."] ".TF::GOLD.$player->getName().": ".TF::GRAY."$reason");
      return true;
    }
  }

  /**
  * Message by default is gray add color code before message to color it!
  *
  * @param string $message
  * @return string
  */
  private static function format($message){
    return TF::GRAY."[".TF::GOLD."UtterCaution".TF::GRAY."] ".$message;
  }
}
