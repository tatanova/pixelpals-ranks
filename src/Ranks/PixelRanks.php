<?php
namespace Ranks;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
class PixelRanks extends PluginBase implements Listener {
  public $ranks = [];
  public $pdat = [];
  public $perms = [];
  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    @mkdir($this->getDataFolder());
    @mkdir($this->getDataFolder()."p/");
    if(!file_exists($this->getDataFolder()."ranks.pp")){
      touch($this->getDataFolder()."ranks.pp");
      $def4dat = array(
      "player" => array(
        "perms" => array(
         "default.player",
         "help.cmd.use"
        ),
        "chat" => "§6* §fPlayer §7{username} §8» §f{msg}",
        "nametag" => "§fPlayer §6* §7{nick}"
      ),
      "owner" => array(
        "perms" => array(
         'op'
        ),
        "chat" => "§6* §eOwner §7{username} §8» §e{msg}",
        "nametag" => "§eOwner §6* §7{nick}"
      )
      );
      yaml_emit_file($this->getDataFolder()."ranks.pp", $def4dat);
      $this->ranks = $def4dat;
    }
    $this->ranks = yaml_parse_file($this->getDataFolder()."ranks.pp");
    if(!file_exists($this->getDataFolder()."p/users.dpp")){
      touch($this->getDataFolder()."p/users.dpp");
      yaml_emit_file($this->getDataFolder()."p/users.dpp", array(0));
      $this->pdat = yaml_parse_file($this->getDataFolder()."p/users.dpp");
    }
    $this->pdat = yaml_parse_file($this->getDataFolder()."p/users.dpp");
  }
  public function onCommand(CommandSender $s, Command $cmd, $text, array $args){
    if($cmd->getName() == "rank"){
      if(isset($args[0])){
        switch($args[0]){
          case "set":
          case "add":
          if(isset($args[1]) && isset($args[2])){
            $p = $this->getServer()->getPlayer($args[1]);
            if($p !== null){
              if(isset($this->ranks[strtolower($args[2])])){
                $this->setPlayerRank($p, strtolower($args[2]));
                $s->sendMessage("§6* §ePixelPals §a|§f You have successfully set §b".$p->getName()."§f's rank to §e".$args[2]);
                $p->sendMessage("§6* §ePixelPals §a|§f Your rank is now §e".$args[2]);
              }else{
                $s->sendMessage("§6* §ePixelPals §c|§f This rank doesn't exists!");
              }
            }else{
              $s->sendMessage("§6* §ePixelPals §c|§f That player is offline!");
            }
          }else{
            $s->sendMessage("§6* §ePixelPals §c|§f Invalid arguments!");
          }
          break;
          case "list":
          case "ls":
          $rankls = array();
          foreach($this->ranks as $k => $v){
            $rankls[] = $k;
          }
          $rankls = implode(", ", $rankls);
          $s->sendMessage("§6* §ePixelPals §b|§f All ranks: §b".$rankls);
          break;
          default:
          $s->sendMessage("§6--==[§e Help §6]==--");
          $s->sendMessage(" ");
          $s->sendMessage("§6- §e/rank set <player> <rank>");
          $s->sendMessage("§6- §e/rank list");
          break;
        }
      }
    }
  }
  public function onChat(PlayerChatEvent $e){
    $msg = $e->getMessage();
    $p = $e->getPlayer();
    $rank = $this->getPlayerRank($p);
    $format = $this->getRankChat($rank, $p, $msg);
    $e->setFormat($format);
  }
  public function getRankChat($name, Player $p, $msg){
    $nick = $p->getName();
    $m = $this->ranks[$name]["chat"];
    $m = str_replace("{username}", $nick, str_replace("{msg}", $msg, $m));
    return $m;
  }
  public function onJoin(PlayerJoinEvent $e){
    $p = $e->getPlayer();
    $name = $p->getName();
    $this->regPlayer($p);
    $rank = $this->getPlayerRank($p);
    $ntag = $this->getRankNametag($rank);
    $ntag = str_replace("{nick}", $name, $ntag);
    $p->setNametag($ntag);
  }
  public function onQuit(PlayerQuitEvent $e){
    $p = $e->getPlayer();
    $this->remPlayerAttchm($p);
  }
  public function getRankNametag($name){
    return $this->ranks[$name]["nametag"];
  }
  public function regPlayer(Player $p){
    $name = $p->getName();
    if(!isset($this->pdat[$name])){
      $this->setPlayerRank($p);
    }
    $this->addPlayerAttchm($p);
    $this->reloadPlayerPerms($p);
  }
  public function reloadPlayerPerms(Player $p){
    $attchm = $this->getPlayerAttchm($p);
    $def4rank = $this->getPlayerRank($p);
    $rankPerms = $this->getRankPerms($def4rank);
    $attchm->clearPermissions();
    
    $playerPerms = [];
    if($rankPerms[0] === 'op'){
      foreach($this->getServer()->getPluginManager()->getDefaultPermissions(true) as $ppm){
        $playerPerms[$ppm->getName()] = true;
      }
    }else{
      foreach($rankPerms as $perm){
        $playerPerms[$perm] = true;
      }
    }
    $attchm->setPermissions($playerPerms);
  }
  public function remPlayerAttchm(Player $p){
    $uuid = $p->getClientId();
    unset($this->perms[$uuid]);
  }
  public function addPlayerAttchm(Player $p){
    $uuid = $p->getClientId();
    $attchm = $p->addAttachment($this);
    $this->perms[$uuid] = $attchm;
  }
  public function getPlayerAttchm(Player $p){
    $uuid = $p->getClientId();
    if(isset($this->perms[$uuid])){
      return $this->perms[$uuid];
    }else{
      $this->addPlayerAttchm($p);
      return $this->perms[$uuid];
    }
  }
  public function getPlayerRank(Player $p){
    $keyName = $p->getName();
    $dat = $this->pdat[$keyName];
    return $dat;
  }
  public function getRankPerms($name){
    if(isset($this->ranks[$name])){
      $perms = $this->ranks[$name]["perms"];
      return $perms;
    }else{
      return array();
    }
  }
  public function setPlayerRank(Player $p, $rank = "player"){
    $name = $p->getName();
    $this->pdat[$name] = $rank;
    $ntag = $this->getRankNametag($rank);
    $ntag = str_replace("{nick}", $name, $ntag);
    $p->setNametag($ntag);
    $this->saveFiles();
    $this->reloadPlayerPerms($p);
  }
  public function saveFiles(){
    yaml_emit_file($this->getDataFolder()."ranks.pp", $this->ranks);
    yaml_emit_file($this->getDataFolder()."p/users.dpp", $this->pdat);
  }
}