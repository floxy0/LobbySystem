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
        $this->saveResource("spawn.yml");
        $this->config = new Config($this->getDataFolder() . "spawn.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $worldName = $this->config->get("spawn-world");
        $posString = $this->config->get("spawn-position");
        $parts = explode(" ", $posString);
        if(count($parts) !== 3) return;
        $x = (float)$parts[0];
        $y = (float)$parts[1];
        $z = (float)$parts[2];
        $worldManager = Server::getInstance()->getWorldManager();
        $world = $worldManager->getWorldByName($worldName);
        if($world === null) return;
        $player = $event->getPlayer();
        $player->teleport(new Position($x, $y, $z, $world));
    }
}
