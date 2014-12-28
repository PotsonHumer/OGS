<?php

	class AD{
		function __construct(){}
		
		// 廣告圖片列表
		public static function ad_list($ad_cate=1){
			
			$select = array(
				'table' => CORE::$config["prefix"].'_ad',
				'field' => '*',
				'where' => "ad_status = '1' and ad_cate = '".$ad_cate."'",
				'order' => 'ad_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_AD_BLOCK_".$ad_cate);
				
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_AD_LIST_".$ad_cate);
					VIEW::assign(array(
						"VALUE_AD_SUBJECT" => $row["ad_subject"] ,
						"VALUE_AD_IMG" => $row["ad_img"],
						"VALUE_AD_LINK" => (!empty($row["ad_link"]))?$row["ad_link"]:'#',
					));
				}
			}
		}
	}
	

?>