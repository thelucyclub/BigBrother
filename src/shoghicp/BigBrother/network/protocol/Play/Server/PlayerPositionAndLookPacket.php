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

class PlayerPositionAndLookPacket extends OutboundPacket{

	public $x;
	public $y;
	public $z;
	public $yaw;
	public $pitch;
	public $flags;
	public $teleportId;

	public function pid(){
		return self::PLAYER_POSITION_AND_LOOK_PACKET;
	}

	public function encode(){
		$this->putDouble($this->x);
		$this->putDouble($this->y);
		$this->putDouble($this->z);
		$this->putFloat($this->yaw);
		$this->putFloat($this->pitch);
		$this->putByte($this->flags);
		$this->putVarInt($this->teleportId);
	}
}
