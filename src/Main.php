<?php

namespace floxy\LobbySystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\Server;

class LobbySystem extends PluginBase implements Listener {

    private Config $config;

    public function onEnable(): void {
        @mkdir($this->getDataFolder());
        $this->saveResource("spawn.yml");
        $this->config = new Config($this->getDataFolder() . "spawn.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $worldName = $this->config->get("spawn-world", "");
        $posString = $this->config->get("spawn-position", "");
        $parts = explode(" ", $posString);
        if(count($parts) !== 3) return;
        $x = floatval($parts[0]);
        $y = floatval($parts[1]);
        $z = floatval($parts[2]);
        $worldManager = Server::getInstance()->getWorldManager();
        if(!$worldManager->isWorldLoaded($worldName)) {
            $worldManager->loadWorld($worldName);
        }
        $world = $worldManager->getWorldByName($worldName);
        if($world === null) return;
        $player = $event->getPlayer();
        $player->teleport(new Position($x, $y, $z, $world));
    }
}
