<?php
	class CARD_CFG{
		function __construct(){
			
			## ALL CARD CONFIG VALUE ##
			
			$this->field = array(
				"id",
				"type",
				"quality",
				"name",
				"mana",
				"attack",
				"hp",
				"description",
				"race",
				"class",
				"son",
				"get_level",
				"gold_get_level",
				"gold_get_class",
				"artist",
				"story",
			);
			
			$this->type = array(
				0 => "手下",
				1 => "法術",
				2 => "武器",
			);
			
			$this->quality = array(
				0 => "基本",
				1 => "專家",
				2 => "精良",
				3 => "史詩",
				4 => "傳說",
			);
			
			$this->race = array(
				0 => "無",
				1 => "野獸",
				2 => "魚人",
				3 => "海盜",
				4 => "惡魔",
				5 => "龍族",
			);
			
			$this->class = array(
				0 => "中立",
				1 => "戰士",
				2 => "薩滿",
				3 => "盜賊",
				4 => "聖騎士",
				5 => "獵人",
				6 => "德魯伊",
				7 => "術士",
				8 => "法師",
				9 => "牧師",
			);
		}
	}
	
?>