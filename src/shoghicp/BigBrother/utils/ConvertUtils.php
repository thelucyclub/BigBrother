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

declare(strict_types=1);

namespace shoghicp\BigBrother\utils;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\entity\Human;
use pocketmine\entity\projectile\Projectile;
use pocketmine\nbt\LittleEndianNBTStream;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntArrayTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Tile;
use pocketmine\timings\TimingsHandler;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\Binary;
use shoghicp\BigBrother\BigBrother;

class ConvertUtils{
	/** @var TimingsHandler */
	private static $timingConvertItem;
	/** @var TimingsHandler */
	private static $timingConvertBlock;

	/** @var array */
	private static $idlist = [
		//************** ITEMS ***********//
		[[325,   8], [326,   0]], //Water bucket,
		[[325,  10], [327,   0]], //Lava bucket
		[[325,   1], [335,   0]], //Milk bucket
		[[450,   0], [449,   0]], //Totem of Undying
		[[444,   0], [443,   0]], //Elytra
		[[443,   0], [422,   0]], //Minecart with Command Block
		[[333,   1], [444,   0]], //Spruce Boat
		[[333,   2], [445,   0]], //Birch Boat
		[[333,   3], [446,   0]], //Jungle Boat
		[[333,   4], [447,   0]], //Acacia Boat
		[[333,   5], [448,   0]], //Dark Oak Boat
		[[445,   5], [448,   0]], //Dark Oak Boat
		[[445,   0], [450,   0]], //Shulker Shell
		[[125,  -1], [158,  -1]], //Dropper
		[[410,  -1], [154,  -1]], //Hopper
		[[425,  -1], [416,  -1]], //Armor Stand
		[[446,  -1], [425,  -1]], //Banner
		[[466,   0], [322,   1]], //Enchanted golden apple
		//************ Discs ***********//
		//NOTE: it's the real value, no joke
		[[500,   0], [2256,  0]],
		[[501,   0], [2257,  0]],
		[[502,   0], [2258,  0]],
		[[503,   0], [2258,  0]],
		[[504,   0], [2260,  0]],
		[[505,   0], [2261,  0]],
		[[506,   0], [2262,  0]],
		[[507,   0], [2263,  0]],
		[[508,   0], [2264,  0]],
		[[509,   0], [2265,  0]],
		[[510,   0], [2266,  0]],
		[[511,   0], [2267,  0]],
		//******** Tipped Arrows *******//
		/*
		[[262,  -1], [440,  -1]], //TODO
		*/
		//*******************************//
		[[458,   0], [435,   0]], //Beetroot Seeds
		[[459,   0], [436,   0]], //Beetroot Soup
		[[460,   0], [349,   1]], //Raw Salmon
		[[461,   0], [349,   2]], //Clownfish
		[[462,   0], [350,   3]], //Pufferfish
		[[463,   0], [350,   1]], //Cooked Salmon
		[[466,   0], [422,   1]], //Enchanted Golden Apple
		//********************************//


		//************ BLOCKS *************//
		[[243,   0], [  3,   2]], //Podzol
		[[198,  -1], [208,  -1]], //Grass Path
		[[247,  -1], [ 49,   0]], //Nether Reactor core is now a obsidian
		[[157,  -1], [125,  -1]], //Double slab
		[[158,  -1], [126,  -1]], //Stairs
		//******** End Rod ********//
		[[208,   0], [198,   0]],
		[[208,   1], [198,   1]],
		[[208,   2], [198,   3]],
		[[208,   3], [198,   2]],
		[[208,   4], [198,   4]],
		[[208,   5], [198,   5]],
		//*************************//
		[[241,  -1], [ 95,  -1]], //Stained Glass
		[[182,   1], [205,   0]], //Purpur Slab
		[[181,   1], [204,   0]], //Double Purpur Slab
		[[ 95,   0], [166,   0]], //Extended Piston is now a barrier
		[[ 43,   6], [ 43,   7]], //Double Quartz Slab
		[[ 43,   7], [ 43,   6]], //Double Nether Brick Slab
		[[ 44,   6], [ 44,   7]], //Quartz Slab
		[[ 44,   7], [ 44,   6]], //Nether Brick Slab
		[[ 44,  14], [ 44,  15]], //Upper Quartz Slab
		[[ 44,  15], [ 44,  14]], //Upper Nether Brick Slab
		[[155,  -1], [155,   0]], //Quartz Block | TODO: convert meta
		[[168,   1], [168,   2]], //Dark Prismarine
		[[168,   2], [168,   1]], //Prismarine Bricks
		[[201,   1], [201,   0]], //Unused Purpur Block
		[[201,   2], [202,   0]], //Pillar Purpur Block
		[[ 85,   1], [188,   0]], //Spruce Fence
		[[ 85,   2], [189,   0]], //Birch Fence
		[[ 85,   3], [190,   0]], //Jungle Fence
		[[ 85,   4], [192,   0]], //Acacia Fence
		[[ 85,   5], [191,   0]], //Dark Oak Fence
		[[240,   0], [199,   0]], //Chorus Plant
		[[199,  -1], [ 68,  -1]], //Item Frame is temporaly a standing sign | TODO: Convert Item Frame block to its entity. #blamemojang
		[[252,  -1], [255,  -1]], //Structures Block
		[[236,  -1], [251,  -1]], //Concretes
		[[237,  -1], [252,  -1]], //Concretes Powder
		//******** Glazed Terracota ********//
		[[220,   0], [235,   0]],
		[[221,   0], [236,   0]],
		[[222,   0], [237,   0]],
		[[223,   0], [238,   0]],
		[[224,   0], [239,   0]],
		[[225,   0], [240,   0]],
		[[226,   0], [241,   0]],
		[[227,   0], [242,   0]],
		[[228,   0], [243,   0]],
		[[229,   0], [244,   0]],
		[[219,   0], [245,   0]],
		[[231,   0], [246,   0]],
		[[232,   0], [247,   0]],
		[[233,   0], [248,   0]],
		[[234,   0], [249,   0]],
		[[235,   0], [250,   0]],
		//*************************//
		[[251,  -1], [218,  -1]], //Observer
		//******** Shulker Box ********//
		//dude mojang, whyy
		[[205,  -1], [229,  -1]], //Undyed
		[[218,   0], [219,   0]],
		[[218,   1], [220,   0]],
		[[218,   2], [221,   0]],
		[[218,   3], [222,   0]],
		[[218,   4], [223,   0]],
		[[218,   5], [224,   0]],
		[[218,   6], [225,   0]],
		[[218,   7], [226,   0]],
		[[218,   8], [227,   0]],
		[[218,   9], [228,   0]],
		[[218,  10], [229,   0]],
		[[218,  11], [230,   0]],
		[[218,  12], [231,   0]],
		[[218,  13], [232,   0]],
		[[218,  14], [233,   0]],
		[[218,  15], [234,   0]],
		//*************************//
		[[188,  -1], [210,  -1]], //Repeating Command Block
		[[189,  -1], [211,  -1]], //Chain Command Block
		[[244,  -1], [207,  -1]], //Beetroot Block
		[[207,  -1], [212,  -1]], //Frosted Ice
		[[  4,  -1], [  4,  -1]], //For Stonecutter
		[[245,  -1], [  4,  -1]] //Stonecutter - To avoid problems, it's now a stone block
		//******************************//
		/*
		[[  P  E  ], [  P  C  ]],
		*/
	];

	/** @var array */
	private static $idlistIndex = [
		[/* Index for PE => PC */],
		[/* Index for PC => PE */],
	];

	/** @var array */
	private static $spawnEggList = [
		10 => "minecraft:chicken",
		11 => "minecraft:cow",
		12 => "minecraft:pig",
		13 => "minecraft:sheep",
		14 => "minecraft:wolf",
		15 => "minecraft:villager",
		16 => "minecraft:cow",
		17 => "minecraft:squid",
		18 => "minecraft:rabbit",
		19 => "minecraft:bat",
		20 => "minecraft:iron_golem",
		21 => "minecraft:snowman",
		22 => "minecraft:cat",
		23 => "minecraft:horse",
		28 => "minecraft:polar_bear",
		32 => "minecraft:zombie",
		33 => "minecraft:creeper",
		34 => "minecraft:skeleton",
		35 => "minecraft:spider",
		36 => "minecraft:zombie_pigman",
		37 => "minecraft:slime",
		38 => "minecraft:enderman",
		39 => "minecraft:silverfish",
		40 => "minecraft:spider",
		41 => "minecraft:ghast",
		42 => "minecraft:magmacube",
		43 => "minecraft:blaze",
		44 => "minecraft:zombie_village",
		45 => "minecraft:witch",
		46 => "minecraft:stray",
		47 => "minecraft:husk",
		48 => "minecraft:wither_skeleton",
		49 => "minecraft:guardian",
		50 => "minecraft:elder_guardian",
		53 => "minecraft:enderdragon",
		54 => "minecraft:shulker",
	];

	/** @var array */
	private static $reverseSpawnEggList;

	public static function init() : void{
		self::$timingConvertItem = new TimingsHandler("BigBrother - Convert Item Data");
		self::$timingConvertBlock = new TimingsHandler("BigBrother - Convert Block Data");

		//reset all index
		self::$idlistIndex = [
			[/* PE => PC */],
			[/* PC => PE */]
		];

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

		self::$reverseSpawnEggList = array_flip(self::$spawnEggList);
	}

	/**
	 * @param NamedTag  $nbt
	 * @param bool $isListTag
	 * @return string converted nbt tag data
	 */
	public static function convertNBTDataFromPEtoPC(NamedTag $nbt, $isListTag = false) : string{
		$stream = new BinaryStream();

		if(!$isListTag){
			$stream->putByte($nbt->getType());

			if($nbt instanceof NamedTag){
				$stream->putShort(strlen($nbt->getName()));
				$stream->put($nbt->getName());
			}
		}

		switch($nbt->getType()){
			case NBT::TAG_Compound:
				assert($nbt instanceof CompoundTag);
				foreach($nbt as $tag){
					$stream->put(self::convertNBTDataFromPEtoPC($tag));
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
				$stream->put(Binary::writeDouble($nbt->getValue()));
			break;
			case NBT::TAG_ByteArray:
				$stream->putInt(strlen($nbt->getValue()));
				$stream->put($nbt->getValue());
			break;
			case NBT::TAG_String:
				$stream->putShort(strlen($nbt->getValue()));
				$stream->put($nbt->getValue());
			break;
			case NBT::TAG_List:
				assert($nbt instanceof ListTag);

				$count = count($nbt);
				$type = $nbt->getTagType();

				foreach($nbt as $tag){
					if($tag instanceof NamedTag){
						if($type !== $tag->getType()){
							throw new \UnexpectedValueException("ListTag must consists of tags which types are the same");
						}
					}
				}

				$stream->putByte($type);
				$stream->putInt($count);

				foreach($nbt as $tag){
					$stream->put(self::convertNBTDataFromPEtoPC($tag, true));
				}
			break;
			case NBT::TAG_IntArray:
				$stream->putInt(count($nbt->getValue()));
				$stream->put(pack("N*", ...$nbt->getValue()));
			break;
		}



		return $stream->getBuffer();
	}

	/**
	 * @param string $buffer
	 * @param bool 	 $isListTag
	 * @param int 	 $listTagId
	 * @return CompoundTag|NamedTag|null
	 */
	public static function convertNBTDataFromPCtoPE(string $buffer, $isListTag = false, $listTagId = NBT::TAG_End) : ?NamedTag{
		$stream = new BinaryStream($buffer);
		$nbt = null;

		if($isListTag){
			$type = $listTagId;
			$name = "";
		}else{
			$type = $stream->getByte();
			if($type !== NBT::TAG_End){
				$name = $stream->get($stream->getShort());
			}
		}

		switch($type){
			case NBT::TAG_End://unused
				$nbt = null;
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
				$nbt = new DoubleTag($name, Binary::readDouble($stream->get(8)));
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
					$tag = self::convertNBTDataFromPCtoPE(substr($buffer, $stream->getOffset()), true, $id);
					if($tag instanceof NamedTag){
						$stream->offset += strlen(self::convertNBTDataFromPEtoPC($tag, true));
					}else{
						$stream->offset += 1;
					}

					if($tag instanceof NamedTag){
						$tags[] = $tag;
					}
				}

				$nbt = new ListTag($name, $tags, $id);
			break;
			case NBT::TAG_Compound:
				$tags = [];
				do{
					$tag = self::convertNBTDataFromPCtoPE(substr($buffer, $stream->getOffset()));
					if($tag instanceof NamedTag){
						$stream->offset += strlen(self::convertNBTDataFromPEtoPC($tag));
					}else{
						$stream->offset += 1;
					}

					if($tag instanceof NamedTag){
						$tags[] = $tag;
					}
				}while($tag !== null and !$stream->feof());

				$nbt = new CompoundTag($name, $tags);
			break;
			case NBT::TAG_IntArray:
				$nbt = new IntArrayTag($name, unpack("N*", $stream->get($stream->getInt()*4)));
			break;
		}

		return $nbt;
	}

	/**
	 * Convert item data from PE => PC when $iscomputer is set to true,
	 * else convert item data opposite way.
	 *
	 * @param bool $iscomputer
	 * @param Item &$item
	 */
	public static function convertItemData(bool $iscomputer, Item &$item) : void{
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
			case Item::WRITABLE_BOOK:
				if($iscomputer){
					if($itemnbt !== ""){
						$nbt = new LittleEndianNBTStream();
						$itemnbt = $nbt->read($itemnbt, true);

						$listTag = [];
						$photoListTag = [];
						foreach($itemnbt["pages"] as $pageNumber => $pageTags){
							if($pageTags instanceof CompoundTag){
								foreach($pageTags as $name => $tag){
									if($tag instanceof StringTag){
										switch($tag->getName()){
											case "text":
												$listTag[] = new StringTag("", $tag->getValue());
											break;
											case "photoname":
												$photoListTag[] = new StringTag("", $tag->getValue());
											break;
										}
									}
								}
							}
						}

						$itemnbt->removeTag("pages");
						$itemnbt->setTag(new ListTag("pages", $listTag));
						$itemnbt->setTag(new ListTag("photoname", $photoListTag));
					}
				}else{
					if($itemnbt !== ""){
						$nbt = new LittleEndianNBTStream();
						$itemnbt = $nbt->read($itemnbt, true);

						$listTag = [];
						foreach($itemnbt["pages"] as $pageNumber => $tag){
							if($tag instanceof StringTag){
								$tag->setName("text");

								$photonameTag = new StringTag("photoname", "");
								if(isset($itemnbt["photoname"][$pageNumber])){
									$photonameTag->setValue($itemnbt["photoname"][$pageNumber]);
								}

								$listTag[] = new CompoundTag("", [
									$tag,
									$photonameTag,
								]);
							}
						}

						$itemnbt->removeTag("pages");
						if($itemnbt->hasTag("photoname")){
							$itemnbt->removeTag("photoname");
						}

						$itemnbt->setTag(new ListTag("pages", $listTag));
					}
				}
			break;
			case Item::WRITTEN_BOOK:
				if($iscomputer){
					if($itemnbt !== ""){
						$nbt = new LittleEndianNBTStream();
						$itemnbt = $nbt->read($itemnbt, true);

						$listTag = [];
						$photoListTag = [];
						foreach($itemnbt["pages"] as $pageNumber => $pageTags){
							if($pageTags instanceof CompoundTag){
								foreach($pageTags as $name => $tag){
									if($tag instanceof StringTag){
										switch($tag->getName()){
											case "text":
												$listTag[] = new StringTag("", $tag->getValue());
											break;
											case "photoname":
												$photoListTag[] = new StringTag("", $tag->getValue());
											break;
										}
									}
								}
							}
						}

						$itemnbt->removeTag("pages");
						$itemnbt->setTag(new ListTag("pages", $listTag));
						$itemnbt->setTag(new ListTag("photoname", $photoListTag));
					}
				}else{
					if($itemnbt !== ""){
						$nbt = new LittleEndianNBTStream();
						$itemnbt = $nbt->read($itemnbt, true);

						$listTag = [];
						foreach($itemnbt["pages"] as $pageNumber => $tag){
							if($tag instanceof StringTag){
								$tag->setName("text");

								$photonameTag = new StringTag("photoname", "");
								if(isset($itemnbt["photoname"][$pageNumber])){
									$photonameTag->setValue($itemnbt["photoname"][$pageNumber]);
								}

								$listTag[] = new CompoundTag("", [
									$tag,
									$photonameTag,
								]);
							}
						}

						$itemnbt->removeTag("pages");
						if($itemnbt->hasTag("photoname")){
							$itemnbt->removeTag("photoname");
						}

						$itemnbt->setTag(new ListTag("pages", $listTag));
					}
				}
			break;
			case Item::SPAWN_EGG:
				if($iscomputer){
					if($type = self::$spawnEggList[$itemdamage] ?? ""){
						$itemnbt = new CompoundTag("", [
							new CompoundTag("EntityTag", [
								new StringTag("id", $type),
							])
						]);
					}
				}else{
					$entitytag = "";
					if($itemnbt !== ""){
						$nbt = new LittleEndianNBTStream();
						$itemnbt = $nbt->read($itemnbt, true);
						if($itemnbt->getType() === NBT::TAG_Compound){
							$entitytag = $itemnbt["EntityTag"]["id"];
						}
					}

					$itemdamage = self::$reverseSpawnEggList[$entitytag] ?? 0;
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

	/**
	 * Convert block data from PE => PC when $iscomputer is set to true,
	 * else convert block data opposite way.
	 *
	 * @param bool $iscomputer
	 * @param int  &$blockid to convert
	 * @param int  &$blockdata to convert
	 */
	public static function convertBlockData(bool $iscomputer, int &$blockid, int &$blockdata) : void{
		self::$timingConvertBlock->startTiming();

		switch($blockid){
			case Block::WOODEN_TRAPDOOR:
			case Block::IRON_TRAPDOOR:
				self::convertTrapdoor($blockdata);
			break;
			case Block::STONE_BUTTON:
			case Block::WOODEN_BUTTON:
				self::convertButton($blockdata);
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

	/**
	 * @param array $olddata
	 * @return array converted
	 */
	public static function convertPEToPCMetadata(array $olddata) : array{
		$newdata = [];

		foreach($olddata as $bottom => $d){
			switch($bottom){
				case Human::DATA_FLAGS: //Flags
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

					if(((int) $d[1] & (1 << Human::DATA_FLAG_INVISIBLE)) > 0){
						$flags |= 0x20;
					}

					if(((int) $d[1] & (1 << Human::DATA_FLAG_CAN_SHOW_NAMETAG)) > 0){
						$newdata[3] = [6, true];
					}

					if(((int) $d[1] & (1 << Human::DATA_FLAG_ALWAYS_SHOW_NAMETAG)) > 0){
						$newdata[3] = [6, true];
					}

					if(((int) $d[1] & (1 << Human::DATA_FLAG_IMMOBILE)) > 0){//TODO
						//$newdata[11] = [0, true];
					}

					if(((int) $d[1] & (1 << Human::DATA_FLAG_SILENT)) > 0){
						$newdata[4] = [6, true];
					}

					$newdata[0] = [0, $flags];
				break;
				case Human::DATA_AIR: //Air
					$newdata[1] = [1, $d[1]];
				break;
				case Human::DATA_NAMETAG: //Custom name
					$newdata[2] = [3, str_replace("\n", "", $d[1])];//TODO
				break;
				case Human::DATA_FUSE_LENGTH: //TNT
					$newdata[6] = [1, $d[1]];
				break;
				case Human::DATA_POTION_COLOR:
					$newdata[8] = [1, $d[1]];
				break;
				case Human::DATA_POTION_AMBIENT:
					$newdata[9] = [6, $d[1] ? true : false];
				break;
				// try fixing DATA_BOUNDING_BOX_HEIGHT and DATA_BOUNDING_BOX_WIDTH
				case Human::DATA_VARIANT:
				break;
				case Human::DATA_PLAYER_FLAGS:
				break;
				case Human::DATA_PLAYER_BED_POSITION:
				break;
				case Human::DATA_LEAD_HOLDER_EID:
				break;
				case Human::DATA_SCALE:
				break;
				case Human::DATA_MAX_AIR:
				break;
				case Human::DATA_OWNER_EID:
				break;
				case Human::DATA_BOUNDING_BOX_WIDTH:
					//welp
					$newdata[53] = [2, $d[1] ? float : 2.0];
				break;
				case Human::DATA_BOUNDING_BOX_HEIGHT:
					//welp
					$newdata[54] = [2, $d[1] ? float : 1.0];
				break;
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
	 * @param int &$blockdata
	 *
	 * #blamemojang
	 */
	private static function convertTrapdoor(int &$blockdata) : void{
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

	/*
	 * Blame Mojang!! :-@
	 * Why Mojang change the order of flag bits?
	 * Why Mojang change the directions??
	 *
	 * @param int &$blockdata
	 *
	 * #blamemojang
	 */
	private static function convertButton(int &$blockdata) : void{
		/*//var_dump($blockdata);

		//swap bits
		$blockdata ^= (($blockdata & 0x04) << 1);
		$blockdata ^= (($blockdata & 0x08) >> 1);
		$blockdata ^= (($blockdata & 0x04) << 1);

		$blockdata = (($blockdata >> 2) << 2) | $blockdata & 0x03;*/
	}

}


class ComputerItem extends Item{
	/**
	 * @param int                $id
	 * @param int                $meta
	 * @param int                $count
	 * @param CompoundTag|string $tag
	 */
	public function __construct(int $id = 0, int $meta = 0, int $count = 1, $tag = ""){
		parent::__construct($id, $meta);
		$this->setCount($count);
		$this->setCompoundTag($tag);
	}
}
