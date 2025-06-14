<?php

namespace floxy\LobbySystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\Server;

class Main extends PluginBase implements Listener {

    private ?float $x = null;
    private ?float $y = null;
    private ?float $z = null;
    private ?World $world = null;

    public function onEnable(): void {
        $this->saveResource("spawn.yml");
        $config = new Config($this->getDataFolder() . "spawn.yml", Config::YAML);
        $worldName = $config->get("spawn-world", "");
        $posString = $config->get("spawn-position", "");
        $parts = explode(" ", $posString);
        if (count($parts) !== 3) {
            $this->getLogger()->error("Invalid spawn-position format.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->x = floatval($parts[0]);
        $this->y = floatval($parts[1]);
        $this->z = floatval($parts[2]);
        $worldManager = Server::getInstance()->getWorldManager();
        if (!$worldManager->isWorldLoaded($worldName)) {
            $worldManager->loadWorld($worldName);
        }
        $this->world = $worldManager->getWorldByName($worldName);
        if ($this->world === null) {
            $this->getLogger()->error("World '$worldName' could not be loaded.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        if ($this->world !== null && $this->x !== null && $this->y !== null && $this->z !== null) {
            $event->getPlayer()->teleport(new Position($this->x, $this->y, $this->z, $this->world));
        }
    }
}
