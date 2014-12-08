<?php

	class AGENTS{
		
		protected static $pointer; // 指定參數
		protected static $func; // 功能參數
		protected static $seo = false; // seo 判定參數
		
		function __construct($args){
			
			// 各項板模
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-side-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			$temp_option = $temp_option + array("MAIN" => 'ogs-agents-tpl.html');
			self::show();
			
			CORE::res_init('jQueryAssets/jquery.ui.core.min','jQueryAssets/jquery.ui.theme.min','jQueryAssets/jquery.ui.tabs.min','css');
			CORE::res_init('jQueryAssets/jquery.ui-1.10.4.tabs.min','js');
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		// 主分類列表
		public static function show(){
			$select = "SELECT * FROM ".CORE::$config["prefix"]."_agents as ag 
						LEFT JOIN ".CORE::$config["prefix"]."_agents_cate as agc on agc.agc_id = ag.agc_id 
						WHERE agc.agc_status = '1' and ag.ag_status = '1'  
						ORDER BY agc.zone_id asc, agc.agc_sort ".CORE::$config["sort"].", ag.ag_sort ".CORE::$config["sort"];
			
			$sql = DB::select(false,$select);
			$rsnum = DB::num($sql);
			
			//new SEO('news');
			$nav[0] = array('name' => 'Service Location','link' => false);
			BREAD::make($nav);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					$zone_array[] = $row["zone_id"];
					
					if($last_zone_id != $row["zone_id"]){
						VIEW::newBlock("TAG_AGENTS_BLOCK");
						VIEW::assign("VALUE_AG_ROW",++$i);
					}
					
					VIEW::newBlock("TAG_AGENTS_LIST");
					VIEW::assign(array(
						"VALUE_AG_SUBJECT" => $row["ag_subject"],
						"VALUE_AG_CONTENT" => $row["ag_content"],
						"VALUE_AGC_COUNTRY" => ($last_gac_id != $row["agc_id"])?'<h3>'.$row["agc_subject"].'</h3>':'',
						
					));
					
					$last_zone_id = $row["zone_id"];
					$last_gac_id = $row["agc_id"];
				}
				
				if(is_array($zone_array) && is_array(CORE::$config["ag_zone"])){
					
					$zone_array = array_flip($zone_array);
					$zone_list = array_flip($zone_array);
					
					foreach(CORE::$config["ag_zone"] as $zone_id => $zone_name){
						if(in_array($zone_id,$zone_list)){
							VIEW::newBlock("TAG_ZONE_LIST");
							VIEW::assign(array(
								"VALUE_ZONE_NAME" => $zone_name,
								"VALUE_ZONE_ROW" => ++$zone_i,
							));
						}
					}
				}
			}
		}

	}
	

?>