<?php
namespace uc;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class UtterCaution extends PluginBase {
  
  const MESSAGE_NORMAL = TF::GRAY;
  const MESSAGE_WARNING = TF::RED;
  const MESSAGE_SUCCESS = TF::GREEN;
  const MESSAGE_NOTICE = TF::YELLOW;

  public function onEnable(){
    @mkdir($this->getDataFolder());
    $this->warnings = (new Config($this->getDataFolder() . "warnings.yml", Config::YAML))->getAll();
    $this->saveDefaultConfig();
  }
  public function onDisable(){
    (new Config($this->getDataFolder() . "warnings.yml", Config::YAML, $this->warnings))->save();
    $this->getConfig()->save();
  }
  
  
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
      
      $sender->sendMessage(self::format("Warning for {$player->getName()} has been expressed!", self::MESSAGE_SUCCESS));
    }
    return true;
  }
  
  /*  ___  ______ _____
  *  / _ \ | ___ \_   _|
  * / /_\ \| |_/ / | |  
  * |  _  ||  __/  | |  
  * | | | || |    _| |_ 
  * \_| |_/\_|    \___/ 
  */
  
  /**
  * @param CommandSender $player who gave warning
  * @param Player $target
  * @param string $reason
  * @param bool $announce
  */
  public function warn(CommandSender $player, Player $target, $points = null, $reason = "", $announce = true){
    // Save the warning
    if(!is_int($points)) {
      // First lets try to get points from config
      if(!empty($reason)) {
        $points = $this->getWarningPoints($reason);
      } else {
       $points = 0; 
      }
    }
    $this->warnings[$target->getName()][] = [
        "issuer" => $player->getName(),
        "reason" => $reason,
        "points" => $points
      ];
    if($announce){
      $output = [TF::GOLD."> ".TF::GRAY];
      $output[] = "Player ".TF::GOLD.$target->getName().TF::GOLD." has been warned by ".TF::GOLD.$player->getName().TF::GRAY."!";
      if($reason != "") $output[] = " Reason: '$reason'";
      if($points > 0) $output[] = "Warning points: ".$points;
      foreach($output as $line) $this->getServer()->broadcastMessage($line);
      return true;
    } else {
      $target->sendMessage(self::format("You've been warned by ".TF::GOLD.$player->getName().", reason: ".TF::GRAY."$reason ($points wp.)", self::MESSAGE_WARNING));
      return true;
    }
    $this->check($target);
  }
  
  public function getWarningPoints(string $reason) : int {
    // TODO
  }
  
  public function check(Player $player) {
    // TODO
  }
  
  /**
  * @param string $message
  * @param string $type message color
  * @return string
  */
  private static function format($message, $type = self::MESSAGE_NORMAL){
    return TF::GRAY."[".TF::GOLD."UtterCaution".TF::GRAY."] ".$type.$message;
  }
  
}
