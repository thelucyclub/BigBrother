<?php
/**
 *  ______  __         ______               __    __
 * |   __ \|__|.-----.|   __ \.----..-----.|  |_ |  |--..-----..----.
 * |   __ <|  ||  _  ||   __ <|   _||  _  ||   _||     ||  -__||   _|
 * |______/|__||___  ||______/|__|  |_____||____||__|__||_____||__|
 *             |_____|
 *
 * BigBrother plugin for PocketMine-MP
 * Copyright (C) 2014-2015 shoghicp <https://github.com/shoghicp/BigBrother>
 * Copyright (C) 2016- BigBrotherTeam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author BigBrotherTeam
 * @link   https://github.com/BigBrotherTeam/BigBrother
 *
 */

namespace shoghicp\BigBrother\utils;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\entity\Human;
use pocketmine\entity\Projectile;
use pocketmine\event\TimingsHandler;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\EndTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntArrayTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\utils\BinaryStream;
use pocketmine\tile\Tile;
use shoghicp\BigBrother\BigBrother;

class ConvertUtils{
	private static $timingConvertItem;
	private static $timingConvertBlock;

	private static $idlist = [
		//************** ITEMS ***********//
		[
			[325, 8], [326, 0] //Water bucket
		],
		[
			[325, 10], [327, 0] //Lava bucket
		],
		[
			[325, 1], [335, 0] //Milk bucket
		],
		[
			[450, 0], [449, 0] //Totem of Undying
		],
		[
			[444, 0], [443, 0] //Elytra
		],
		[
			[443, 0], [422, 0] //Minecart with Command Block
		],
		[
			[333, 1], [444, 0] //Spruce Boat
		],
		[
			[333, 2], [445, 0] //Birch Boat
		],
		[
			[333, 3], [446, 0] //Jungle Boat
		],
		[
			[333, 4], [447, 0] //Acacia Boat
		],
		[
			[333, 5], [448, 0] //Dark Oak Boat
		],
		[
			[445, 5], [448, 0] //Dark Oak Boat
		],
		[
			[445, 0], [450, 0] //Shulker Shell
		],
		[
			[125, -1], [158, -1] //Dropper
		],
		[
			[410, -1], [154, -1] //Hopper
		],
		//******** Tipped Arrows *******//
		/*[
			[262, -1], [440, -1] //TODO: Fix that
		],*/
		//*******************************//
		[
			[458, 0], [435, 0] //Beetroot Seeds
		],
		[
			[459, 0], [436, 0] //Beetroot Soup
		],
		[
			[460, 0], [349, 1] //Raw Salmon
		],
		[
			[461, 0], [349, 2] //Clownfish
		],
		[
			[462, 0], [350, 3] //Pufferfish
		],
		[
			[463, 0], [350, 1] //Cooked Salmon
		],
		[
			[466, 0], [422, 1] //Enchanted Golden Apple
		],
		//********************************//


		//************ BLOCKS *************//
		[
			[243, 0], [3, 2] //Podzol
		],
		[
			[198, -1], [208, -1] //Grass Path
		],
		[
			[247, -1], [49, 0] //Nether Reactor core is now a obsidian
		],
		[
			[157, -1], [125, -1] //Double slab
		],
		[
			[158, -1], [126, -1] //Stairs
		],
		//******** End Rod ********//
		[
			[208, 0], [198, 0]
		],
		[
			[208, 1], [198, 1]
		],
		[
			[208, 2], [198, 3]
		],
		[
			[208, 3], [198, 2]
		],
		[
			[208, 4], [198, 4]
		],
		[
			[208, 5], [198, 5]
		],
		//*************************//
		[
			[241, -1], [95, -1] //Stained Glass
		],
		[
			[182, 1], [205, 0] //Purpur Slab
		],
		[
			[181, 1], [204, 0] //Double Purpur Slab
		],
		[
			[95, 0], [166, 0] //Extended Piston is now a barrier
		],
		[
			[43, 6], [43, 7] //Double Quartz Slab
		],
		[
			[43, 7], [43, 6] //Double Nether Brick Slab
		],
		[
			[44, 6], [44, 7] //Quartz Slab
		],
		[
			[44, 7], [44, 6] //Nether Brick Slab
		],
		[
			[44, 14], [44, 15] //Upper Quartz Slab
		],
		[
			[44, 15], [44, 14] //Upper Nether Brick Slab
		],
		[
			[168, 1], [168, 2] //Dark Prismarine
		],
		[
			[168, 2], [168, 1] //Prismarine Bricks
		],
		[
			[201, 1], [201, 0] //Unused Purpur Block
		],
		[
			[201, 2], [202, 0] //Pillar Purpur Block
		],
		[
			[85, 1], [188, 0] //Spruce Fence
		],
		[
			[85, 2], [189, 0] //Birch Fence
		],
		[
			[85, 3], [190, 0] //Jungle Fence
		],
		[
			[85, 4], [192, 0] //Acacia Fence
		],
		[
			[85, 5], [191, 0] //Dark Oak Fence
		],
		[
			[240, 0], [199, 0] //Chorus Plant
		],
		[
			[199, -1], [68, -1] //Item Frame is temporaly a standing sign | TODO: Convert Item Frame block to its entity. #blamemojang
		],
		[
			[236, -1], [252, -1] //Concretes
		],
		//******** Glazed Terracota ********//
		[
			[220, 0], [235, 0]
		],
		[
			[221, 0], [236, 0]
		],
		[
			[222, 0], [237, 0]
		],
		[
			[223, 0], [238, 0]
		],
		[
			[224, 0], [239, 0]
		],
		[
			[225, 0], [240, 0]
		],
		[
			[226, 0], [241, 0]
		],
		[
			[227, 0], [242, 0]
		],
		[
			[228, 0], [243, 0]
		],
		[
			[229, 0], [244, 0]
		],
		[
			[219, 0], [245, 0]
		],
		[
			[231, 0], [246, 0]
		],
		[
			[232, 0], [247, 0]
		],
		[
			[233, 0], [248, 0]
		],
		[
			[234, 0], [249, 0]
		],
		[
			[235, 0], [250, 0]
		],
		//*************************//
		[
			[251, -1], [218, -1] //Observer
		],
		//******** Shulker Box ********//
		//dude mojang, whyy
		[
			[218, 0], [219, 0]
		],
		[
			[218, 1], [220, 0]
		],
		[
			[218, 2], [221, 0]
		],
		[
			[218, 3], [222, 0]
		],
		[
			[218, 4], [223, 0]
		],
		[
			[218, 5], [224, 0]
		],
		[
			[218, 6], [225, 0]
		],
		[
			[218, 7], [226, 0]
		],
		[
			[218, 8], [227, 0]
		],
		[
			[218, 9], [228, 0]
		],
		[
			[218, 10], [229, 0]
		],
		[
			[218, 11], [230, 0]
		],
		[
			[218, 12], [231, 0]
		],
		[
			[218, 13], [232, 0]
		],
		[
			[218, 14], [233, 0]
		],
		[
			[218, 15], [234, 0]
		],
		//*************************//
		[
			[188, -1], [210, -1] //Repeating Command Block
		],
		[
			[189, -1], [211, -1] //Chain Command Block
		],
		[
			[244, -1], [207, -1] //Beetroot Block
		],
		[
			[207, -1], [212, -1] //Frosted Ice
		],
		[
			[245, -1], [61, -1] //Stonecutter - To avoid problems, it's now a simple furnace
		],
		//******************************//
		/*
		[
			[PE], [PC]
		],
		*/
	];
	private static $idlistIndex = [
		[/* Index for PE => PC */],
		[/* Index for PC => PE */],
	];


	public static function init(){
		self::$timingConvertItem = new TimingsHandler("BigBrother - Convert Item Data");
		self::$timingConvertBlock = new TimingsHandler("BigBrother - Convert Block Data");

		foreach(self::$idlist as $entry){
			//append index (PE => PC)
			if(isset(self::$idlistIndex[0][$entry[0][0]])){
				self::$idlistIndex[0][$entry[0][0]][] = $entry;
			}else{
				self::$idlistIndex[0][$entry[0][0]] = [$entry];
			}

			//append index (PC => PE)
			if(isset(self::$idlistIndex[1][$entry[1][0]])){
				self::$idlistIndex[1][$entry[1][0]][] = $entry;
			}else{
				self::$idlistIndex[1][$entry[1][0]] = [$entry];
			}
		}
	}

	public static function convertNBTDataFromPEtoPC(Tag $nbt, bool $convert = false) : ?string{
		$stream = new BinaryStream();
		$stream->putByte($nbt->getType());

		if($nbt->getType() !== NBT::TAG_End){
			$stream->putShort(strlen($nbt->getName()));
			$stream->put($nbt->getName());
		}

		switch($nbt->getType()){
			case NBT::TAG_Compound:
				foreach($nbt as $tag){
					if($nbt["id"] === Tile::SIGN){
						if($tag->getType() === NBT::TAG_String){
							$convert = true;
						}else{
							$convert = false;
						}
					}else{
						$convert = false;
					}
					$stream->put(self::convertNBTDataFromPEtoPC($tag, $convert));
				}

				$stream->putByte(0);
			break;
			case NBT::TAG_End: //No named tag
			break;
			case NBT::TAG_Byte:
				$stream->putByte($nbt->getValue());
			break;
			case NBT::TAG_Short:
				$stream->putShort($nbt->getValue());
			break;
			case NBT::TAG_Int:
				$stream->putInt($nbt->getValue());
			break;
			case NBT::TAG_Long:
				$stream->putLong($nbt->getValue());
			break;
			case NBT::TAG_Float:
				$stream->putFloat($nbt->getValue());
			break;
			case NBT::TAG_Double:
				$stream->put(pack("d", $nbt->getValue()));
			break;
			case NBT::TAG_ByteArray:
				$stream->putInt(strlen($nbt->getValue()));
				$stream->put($nbt->getValue());
			break;
			case NBT::TAG_String:
				if($convert){
					$value = BigBrother::toJSON($nbt->getValue());
					$stream->putShort(strlen($value));
					$stream->put($value);
				}else{
					$stream->putShort(strlen($nbt->getValue()));
					$stream->put($nbt->getValue());
				}
			break;
			case NBT::TAG_List:
				$id = null;
				foreach($nbt as $tag){
					if($tag instanceof Tag){
						if(!isset($id)){
							$id = $tag->getType();
						}elseif($id !== $tag->getType()){
							return null;
						}
					}
				}

				$stream->putByte($id);

				$tags = [];
				foreach($nbt as $tag){
					if($tag instanceof Tag){
						$tags[] = $tag;
					}
				}
				$stream->putInt(count($tags));

				foreach($tags as $tag){
					$stream->put(self::convertNBTDataFromPCtoPE($tag));
				}
			break;
			case NBT::TAG_IntArray:
				$stream->putInt(count($nbt->getValue()));
				$stream->put(pack("N*", ...$nbt->getValue()));
			break;
		}

		return $stream->getBuffer();
	}

	public static function convertNBTDataFromPCtoPE(string $buffer) : ?Tag{
		$stream = new BinaryStream($buffer);
		$nbt = null;

		$type = $stream->getByte();
		if($type !== NBT::TAG_End){
			$name = $stream->get($stream->getShort());
		}

		switch($type){
			case NBT::TAG_End: //No named tag
				$nbt = new EndTag();
			break;
			case NBT::TAG_Byte:
				$nbt = new ByteTag($name, $stream->getByte());
			break;
			case NBT::TAG_Short:
				$nbt = new ShortTag($name, $stream->getShort());
			break;
			case NBT::TAG_Int:
				$nbt = new IntTag($name, $stream->getInt());
			break;
			case NBT::TAG_Long:
				$nbt = new LongTag($name, $stream->getLong());
			break;
			case NBT::TAG_Float:
				$nbt = new FloatTag($name, $stream->getFloat());
			break;
			case NBT::TAG_Double:
				$nbt = new DoubleTag($name, unpack("d", $stream->get(4)));
			break;
			case NBT::TAG_ByteArray:
				$nbt = new ByteArrayTag($name, $stream->get($stream->getInt()));
			break;
			case NBT::TAG_String:
				$nbt = new StringTag($name, $stream->get($stream->getShort()));
			break;
			case NBT::TAG_List:
				$id = $stream->getByte();
				$count = $stream->getInt();

				$tags = [];
				for($i = 0; $i < $count and !$stream->feof(); $i++){
					$tag = self::convertNBTDataFromPCtoPE(substr($buffer, $stream->getOffset()));
					$stream->offset += strlen(self::convertNBTDataFromPEtoPC($tag));

					if($tag instanceof NamedTag and $tag->getName() !== ""){
						$tags[] = $tag;
					}
				}

				$nbt = new ListTag($name, $tags);
			break;
			case NBT::TAG_Compound:
				$tags = [];
				do{
					$tag = self::convertNBTDataFromPCtoPE(substr($buffer, $stream->getOffset()));
					$stream->offset += strlen(self::convertNBTDataFromPEtoPC($tag));

					if($tag instanceof NamedTag and $tag->getName() !== ""){
						$tags[] = $tag;
					}
				}while(!($tag instanceof EndTag) and !$stream->feof());

				$nbt = new CompoundTag($name, $tags);
			break;
			case NBT::TAG_IntArray:
				$nbt = new IntArrayTag($name, unpack("N*", ...$stream->get($stream->getInt())));
			break;
		}

		return $nbt;
	}

	/*
	 * $iscomputer = true is PE => PC
	 * $iscomputer = false is PC => PE
	 */
	public static function convertItemData($iscomputer, &$item){
		self::$timingConvertItem->startTiming();

		$itemid = $item->getId();
		$itemdamage = $item->getDamage();
		$itemcount = $item->getCount();
		$itemnbt = $item->getCompoundTag();

		switch($itemid){
			case Item::PUMPKIN:
			case Item::JACK_O_LANTERN:
				$itemdamage = 0;
			break;
			case Item::SPAWN_EGG:
				if($iscomputer){
					switch($itemdamage){
						case 10://Chicken
							$type = "chicken";
						break;
						case 11://Cow
							$type = "cow";
						break;
						case 12://Pig
							$type = "pig";
						break;
						case 13://Sheep
							$type = "sheep";
						break;
						case 14://Wolf
							$type = "wolf";
						break;
						case 15://Villager
							$type = "villager";
						break;
						case 16://Mooshroom
							$type = "cow";
						break;
						case 17://Squid
							$type = "squid";
						break;
						case 18://Rabbit
							$type = "rabbit";
						break;
						case 19://Bat
							$type = "bat";
						break;
						case 20://IronGolem
							$type = "iron_golem";
						break;
						case 21://SnowGolem (Snowman)
							$type = "snowman";
						break;
						case 22://Ocelot
							$type = "cat";
						break;
						case 23://Horse
							$type = "horse";
						break;
						case 28://PolarBear
							$type = "polar_bear";
						break;
						case 32://Zombie
							$type = "zombie";
						break;
						case 33://Creeper
							$type = "creeper";
						break;
						case 34://Skeleton
							$type = "skeleton";
						break;
						case 35://Spider
							$type = "spider";
						break;
						case 36://PigZombie
							$type = "zombie_pigman";
						break;
						case 37://Slime
							$type = "slime";
						break;
						case 38://Enderman
							$type = "enderman";
						break;
						case 39://Silverfish
							$type = "silverfish";
						break;
						case 40://CaveSpider
							$type = "spider";
						break;
						case 41://Ghast
							$type = "ghast";
						break;
						case 42://LavaSlime
							$type = "magmacube";
						break;
						case 43://Blaze
							$type = "blaze";
						break;
						case 44://ZombieVillager
							$type = "zombie_village";
						break;
						case 45://Witch
							$type = "witch";
						break;
						case 46://Stray
							$type = "stray";
						break;
						case 47://Husk
							$type = "husk";
						break;
						case 48://WitherSkeleton
							$type = "wither_skeleton";
						break;
						case 49://Guardian
							$type = "guardian";
						break;
						case 50://ElderGuardian
							$type = "elder_guardian";
						break;
						case 53://EnderDragon
							$type = "enderdragon";
						break;
						case 54://Shulker
							$type = "shulker";
						break;
						default:
							$type = "";
						break;
					}

					if($type !== ""){
						$nbt = new NBT(NBT::LITTLE_ENDIAN);
						$nbt->setData(new CompoundTag("", [
							new CompoundTag("EntityTag", [
								new StringTag("id", "minecraft:".$type),
							])
						]));
						$itemnbt = $nbt->write();
					}
				}else{
					$entitytag = "";
					if($itemnbt !== ""){
						if($itemnbt->getType() === NBT::TAG_Compound){
							$entitytag = $itemnbt["EntityTag"]["id"];
						}
					}

					switch($entitytag){
						case "minecraft:chicken":
							$itemdamage = 10;
						break;
						case "minecraft:cow":
							$itemdamage = 11;
						break;
						case "minecraft:pig":
							$itemdamage = 12;
						break;
						case "minecraft:sheep":
							$itemdamage = 13;
						break;
						case "minecraft:wolf":
							$itemdamage = 14;
						break;
						case "minecraft:villager":
							$itemdamage = 15;
						break;
						case "minecraft:cow":
							$itemdamage = 16;
						break;
						case "minecraft:squid":
							$itemdamage = 17;
						break;
						case "minecraft:rabbit":
							$itemdamage = 18;
						break;
						case "minecraft:bat":
							$itemdamage = 19;
						break;
						case "minecraft:iron_golem":
							$itemdamage = 20;
						break;
						case "minecraft:snowman":
							$itemdamage = 21;
						break;
						case "minecraft:cat":
							$itemdamage = 22;
						break;
						case "minecraft:horse":
							$itemdamage = 23;
						break;
						case "minecraft:polar_bear":
							$itemdamage = 28;
						break;
						case "minecraft:zombie":
							$itemdamage = 32;
						break;
						case "minecraft:creeper":
							$itemdamage = 33;
						break;
						case "minecraft:skeleton":
							$itemdamage = 34;
						break;
						case "minecraft:spider":
							$itemdamage = 35;
						break;
						case "minecraft:zombie_pigman":
							$itemdamage = 36;
						break;
						case "minecraft:slime":
							$itemdamage = 37;
						break;
						case "minecraft:enderman":
							$itemdamage = 38;
						break;
						case "minecraft:silverfish":
							$itemdamage = 39;
						break;
						case "minecraft:spider":
							$itemdamage = 40;
						break;
						case "minecraft:ghast":
							$itemdamage = 41;
						break;
						case "minecraft:magmacube":
							$itemdamage = 42;
						break;
						case "minecraft:blaze":
							$itemdamage = 43;
						break;
						case "minecraft:zombie_village":
							$itemdamage = 44;
						break;
						case "minecraft:witch":
							$itemdamage = 45;
						break;
						case "minecraft:stray":
							$itemdamage = 46;
						break;
						case "minecraft:husk":
							$itemdamage = 47;
						break;
						case "minecraft:wither_skeleton":
							$itemdamage = 48;
						break;
						case "minecraft:guardian":
							$itemdamage = 49;
						break;
						case "minecraft:elder_guardian":
							$itemdamage = 50;
						break;
						case "minecraft:enderdragon":
							$itemdamage = 53;
						break;
						case "minecraft:shulker":
							$itemdamage = 54;
						break;
						default:
							$itemdamage = 0;
						break;
					}

					$itemnbt = "";
				}
			break;
			default:
				if($iscomputer){
					$src = 0; $dst = 1;
				}else{
					$src = 1; $dst = 0;
				}

				foreach(self::$idlistIndex[$src][$itemid] ?? [] as $convertitemdata){
					if($convertitemdata[$src][1] === -1){
						$itemid = $convertitemdata[$dst][0];
						if($convertitemdata[$dst][1] === -1){
							$itemdamage = $item->getDamage();
						}else{
							$itemdamage = $convertitemdata[$dst][1];
						}
						break;
					}elseif($convertitemdata[$src][1] === $item->getDamage()){
						$itemid = $convertitemdata[$dst][0];
						$itemdamage = $convertitemdata[$dst][1];
						break;
					}
				}
			break;
		}

		if($iscomputer){
			$item = new ComputerItem($itemid, $itemdamage, $itemcount, $itemnbt);
		}else{
			$item = Item::get($itemid, $itemdamage, $itemcount, $itemnbt);
		}

		self::$timingConvertItem->stopTiming();
	}

	/*
	 * $iscomputer = true is PE => PC
	 * $iscomputer = false is PC => PE
	 */
	public static function convertBlockData($iscomputer, &$blockid, &$blockdata){
		self::$timingConvertBlock->startTiming();

		switch($blockid){
			case Block::WOODEN_TRAPDOOR:
			case Block::IRON_TRAPDOOR:
				self::convertTrapdoor($iscomputer, $blockid, $blockdata);
			break;
			default:
				if($iscomputer){
					$src = 0; $dst = 1;
				}else{
					$src = 1; $dst = 0;
				}

				foreach(self::$idlistIndex[$src][$blockid] ?? [] as $convertblockdata){
					if($convertblockdata[$src][1] === -1){
						$blockid = $convertblockdata[$dst][0];
						if($convertblockdata[$dst][1] !== -1){
							$blockdata = $convertblockdata[$dst][1];
						}
						break;
					}elseif($convertblockdata[$src][1] === $blockdata){
						$blockid = $convertblockdata[$dst][0];
						$blockdata = $convertblockdata[$dst][1];
						break;
					}
				}
			break;
		}

		self::$timingConvertBlock->stopTiming();
	}

	public static function convertPEToPCMetadata(array $olddata){
		$newdata = [];

		foreach($olddata as $bottom => $d){
			switch($bottom){
				case Human::DATA_FLAGS://Flags
					$flags = 0;

					if(((int) $d[1] & (1 << Human::DATA_FLAG_ONFIRE)) > 0){
						$flags |= 0x01;
					}

					if(((int) $d[1] & (1 << Human::DATA_FLAG_SNEAKING)) > 0){
						$flags |= 0x02;
					}

					if(((int) $d[1] & (1 << Human::DATA_FLAG_SPRINTING)) > 0){
						$flags |= 0x08;
					}

					if(((int) $d[1] & (1 <<  Human::DATA_FLAG_INVISIBLE)) > 0){
						//$flags |= 0x20;
					}

					if(((int) $d[1] & (1 <<  Human::DATA_FLAG_CAN_SHOW_NAMETAG)) > 0){
						$newdata[3] = [6, true];
					}

					if(((int) $d[1] & (1 <<  Human::DATA_FLAG_ALWAYS_SHOW_NAMETAG)) > 0){
						$newdata[3] = [6, true];
					}

					if(((int) $d[1] & (1 <<  Human::DATA_FLAG_IMMOBILE)) > 0){
						//$newdata[11] = [0, true];
					}

					if(((int) $d[1] & (1 <<  Human::DATA_FLAG_SILENT)) > 0){
						$newdata[4] = [6, true];
					}

					$newdata[0] = [0, $flags];
				break;
				case Human::DATA_AIR://Air
					$newdata[1] = [1, $d[1]];
				break;
				case Human::DATA_NAMETAG://Custom name
					$newdata[2] = [3, str_replace("\n", "", $d[1])];//TODO
				break;
				case Human::DATA_FUSE_LENGTH://TNT
					$newdata[6] = [1, $d[1]];
				break;
				case Human::DATA_VARIANT:
				case Human::DATA_PLAYER_FLAGS:
				case Human::DATA_PLAYER_BED_POSITION:
				case Human::DATA_LEAD_HOLDER_EID:
				case Human::DATA_SCALE:
				case Human::DATA_MAX_AIR:
				case Human::DATA_OWNER_EID:
				case Projectile::DATA_SHOOTER_ID:
					//Unused
				break;
				default:
					echo "key: ".$bottom." Not implemented\n";
				break;
				//TODO: add data type
			}
		}

		$newdata["convert"] = true;

		return $newdata;
	}

	/*
	 * Blame Mojang!! :-@
	 * Why Mojang change the order of flag bits?
	 * Why Mojang change the directions??
	 *
	 * #blamemojang
	 */
	private static function convertTrapdoor(bool $iscomputer, int &$blockid, int &$blockdata){
		//swap bits
		$blockdata ^= (($blockdata & 0x04) << 1);
		$blockdata ^= (($blockdata & 0x08) >> 1);
		$blockdata ^= (($blockdata & 0x04) << 1);

		//swap directions
		$directions = [
			0 => 3,
			1 => 2,
			2 => 1,
			3 => 0
		];

		$blockdata = (($blockdata >> 2) << 2) | $directions[$blockdata & 0x03];
	}
}


class ComputerItem{
	public $id = 0, $damage = 0, $count = 0, $nbt = "";

	public function __construct($id = 0, $damage = 0, $count = 1, $nbt = ""){
		$this->id = $id;
		$this->damage = $damage;
		$this->count = $count;

		if($nbt instanceof EndTag){
			$nbt = "";
		}

		$this->nbt = $nbt;
	}

	public function getID(){
		return $this->id;
	}

	public function getDamage(){
		return $this->damage;
	}

	public function getCount(){
		return $this->count;
	}

	public function getCompoundTag(){
		return $this->nbt;
	}

}
