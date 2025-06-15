<?php

declare(strict_types=1);

namespace Floxy\LobbySystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;

class Main extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();
        $world = Server::getInstance()->getWorldManager()->getWorldByName("lobby");
        if($world !== null){
            $player->teleport(new Position($world->getSpawnLocation()->getX(), $world->getSpawnLocation()->getY(), $world->getSpawnLocation()->getZ(), $world));
        }
        $player->getInventory()->clearAll();
        $feather = VanillaItems::FEATHER()->setCustomName("§eBoost §8(right-click)");
        $player->getInventory()->setItem(0, $feather);
    }

    public function onInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($item->getTypeId() === VanillaItems::FEATHER()->getTypeId() && $item->getCustomName() === "§eBoost §8(right-click)"){
            $direction = $player->getDirectionVector()->multiply(1.5)->add(new Vector3(0, 0.8, 0));
            $player->setMotion($direction);
        }
    }

    public function onDrop(PlayerDropItemEvent $event): void{
        $item = $event->getItem();
        if($item->getTypeId() === VanillaItems::FEATHER()->getTypeId() && $item->getCustomName() === "§eBoost §8(right-click)"){
            $event->cancel();
        }
    }

    public function onInventoryMove(InventoryTransactionEvent $event): void{
        foreach($event->getTransaction()->getActions() as $action){
            $item = $action->getSourceItem();
            if($item->getTypeId() === VanillaItems::FEATHER()->getTypeId() && $item->getCustomName() === "§eBoost §8(right-click)"){
                $event->cancel();
                break;
            }
        }
    }
}
