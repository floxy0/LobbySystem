<?php

namespace floxy\LobbySystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\Server;

class LobbySystem extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->saveResource("spawn.yml");
        $config = new Config($this->getDataFolder() . "spawn.yml", Config::YAML);
        $worldName = $config->get("spawn-world", "");
        $posString = $config->get("spawn-position", "");
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
        $this->getServer()->getPluginManager()->registerEvents(new class($x, $y, $z, $world) implements Listener {
            private float $x;
            private float $y;
            private float $z;
            private $world;

            public function __construct(float $x, float $y, float $z, $world) {
                $this->x = $x;
                $this->y = $y;
                $this->z = $z;
                $this->world = $world;
            }

            public function onJoin(PlayerJoinEvent $event): void {
                $event->getPlayer()->teleport(new Position($this->x, $this->y, $this->z, $this->world));
            }
        }, $this);
    }
}
