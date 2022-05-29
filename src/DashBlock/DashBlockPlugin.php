<?php
declare(strict_types=1);

namespace DashBlock;

use pocketmine\block\BlockLegacyIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

class DashBlockPlugin extends PluginBase implements Listener{

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();

		$block = $player->getWorld()->getBlock($player->getPosition()->add(0, -1, 0));

		$jump_blocks = [3, 5, 7];

		$dash_blocks = [BlockLegacyIds::GOLD_BLOCK, BlockLegacyIds::DIAMOND_BLOCK];

		if($block->getId() === BlockLegacyIds::WOOL and in_array($block->getMeta(), $jump_blocks)){
			$force = 0;

			switch($block->getMeta()){
				case 3:
					$force = 1;
					break;
				case 5:
					$force = 3;
					break;
				case 7:
					$force = 7;
					break;
			}

			$player->setMotion(new Vector3(0, $force, 0));
		}elseif(in_array($block->getId(), $dash_blocks)){
			$force = 0;
			switch($block->getId()){
				case BlockLegacyIds::GOLD_BLOCK:
					$force = 2;
					break;
				case BlockLegacyIds::DIAMOND_BLOCK:
					$force = 3;
					break;
			}

			$x = -sin($player->getLocation()->getYaw() / 180 * M_PI) * cos($player->getLocation()->getPitch() / 180 * M_PI);
			$y = -sin($player->getLocation()->getPitch() / 180 * M_PI);
			$z = cos($player->getLocation()->getYaw() / 180 * M_PI) * cos($player->getLocation()->getPitch() / 180 * M_PI);

			$dashVector = new Vector3($x * $force, $y * $force, $z * $force);

			$player->setMotion($dashVector);
		}
	}

	public function onKick(PlayerKickEvent $event){
		if($event->getReason() === $this->getServer()->getLanguage()->translateString("kick.reason.cheat", ["%ability.flight"])){
			$event->cancel();
		}
	}

	public function handleEntityDamage(EntityDamageEvent $event){
		if($event->getCause() === EntityDamageEvent::CAUSE_FALL){
			$event->cancel();
		}
	}

}