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
    private ?Position $spawn = null;

    public function onEnable(): void {
        @mkdir($this->getDataFolder());
        if (!file_exists($this->getDataFolder() . "spawn.yml")) {
            $this->saveResource("spawn.yml");
        }
        $this->config = new Config($this->getDataFolder() . "spawn.yml", Config::YAML);
        $worldName = $this->config->get("spawn-world", "");
        $posString = $this->config->get("spawn-position", "");
        $parts = explode(" ", $posString);
        if (count($parts) !== 3) {
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $x = floatval($parts[0]);
        $y = floatval($parts[1]);
        $z = floatval($parts[2]);
        $worldManager = Server::getInstance()->getWorldManager();
        if (!$worldManager->isWorldLoaded($worldName)) {
            $worldManager->loadWorld($worldName);
        }
        $world = $worldManager->getWorldByName($worldName);
        if ($world === null) {
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->spawn = new Position($x, $y, $z, $world);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        if ($this->spawn !== null) {
            $event->getPlayer()->teleport($this->spawn);
        }
    }
}
