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

namespace shoghicp\BigBrother\network\protocol\Play\Server;

use shoghicp\BigBrother\network\OutboundPacket;
use shoghicp\BigBrother\utils\ConvertUtils;
use pocketmine\nbt\NBT;

class ChunkDataPacket extends OutboundPacket{

	public $chunkX;
	public $chunkZ;
	public $groundUp;
	public $primaryBitmap;
	public $payload;
	public $biomes;
	public $blockEntities = [];

	public function pid(){
		return self::CHUNK_DATA_PACKET;
	}

	public function encode(){
		$this->putInt($this->chunkX);
		$this->putInt($this->chunkZ);
		$this->putByte($this->groundUp ? 1 : 0);
		$this->putVarInt($this->primaryBitmap);
		if($this->groundUp){
			$this->putVarInt(strlen($this->payload.$this->biomes));
			$this->put($this->payload);
			$this->put($this->biomes);
		}else{
			$this->putVarInt(strlen($this->payload));
			$this->put($this->payload);
		}
		$this->putVarInt(count($this->blockEntities));

		foreach($this->blockEntities as $blockEntity){
			ConvertUtils::convertNBTData(true, $blockEntity);
			$this->put($blockEntity);
		}
	}
}
