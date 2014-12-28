<?php

	// 麵包屑
	class BREAD{
		
		private static $pc_nav;
		
		function __construct(){} // NO need
		
		public static function fetch(array $row){
			if(CHECK::is_array_exist($row)){
				$row_keys = array_keys($row);
				$id_key = $row_keys[0]; // 取得主要 id key值
				$id_value = array_shift($row); // 取得主要 id value值
				
				$retrun = true;
			}else{
				$retrun = false;
			}
			
			// 取值
			if($retrun){
				$tb_name = self::tb_check($id_key); // 取得對應資料表
				self::nav($tb_name,$id_key,$id_value);
			}
			
			CHECK::check_clear();
			return $retrun;
		}
		
		// 組合麵包屑
		public static function make(array $nav){
			if(CHECK::is_array_exist($nav)){
				$bread_array[] = '<a href="'.CORE::$lang.'">'.CORE::$msg["home"].'</a>';
				
				foreach($nav as $nav_array){
					if(!empty($nav_array["link"])){
						$bread_array[] = '<a href="'.$nav_array["link"].'">'.$nav_array["name"].'</a>';
					}else{
						$bread_array[] = $nav_array["name"];
					}
				}
				
				if(is_array($bread_array)){
					CHECK::check_clear();
					$bread_str = implode(" > ",$bread_array);
					VIEW::assignGlobal("TAG_LAYER",$bread_str);
				}else{
					CHECK::check_clear();
					return false;
				}
			}
		}
		
		//-------------------------------------------------------------------------------------------------
		
		// 取得對應資料表
		private static function tb_check($id_key){
			$sql = mysql_list_tables(CORE::$config["connect"]["db"],DB::$con);
			while($row = DB::fetch($sql)){
				$tb_name = $row["Tables_in_".CORE::$config["connect"]["db"]];
				
				$select = array (
					'table' => $tb_name,
					'field' => "*",
					'where' => '',
					//'order' => '',
					'limit' => '0,1',
				);
				
				$tb_sql = DB::select($select);
				$rsnum = DB::num($tb_sql);
				
				if(!empty($rsnum)){
					$tb_field = DB::field($tb_sql);
					if($tb_field->name == $id_key && preg_match("/".CORE::$config["prefix"]."_/",$tb_name)){
						return $tb_name;
					}
				}
			}
		}
		
		// 判斷並取得麵包屑來源
		private static function nav($tb_name,$id_field,$id_value){
			
			switch($tb_name){
				case CORE::$config["prefix"]."_intro":
					$nav = self::single_nav($tb_name,$id_field,$id_value,'it_subject');
				break;
				case CORE::$config["prefix"]."_news_cate":
					$nav = self::single_nav($tb_name,$id_field,$id_value,'nc_subject');
				break;
				/*
				case CORE::$config["prefix"]."_news":
					
				break;
				*/
				case CORE::$config["prefix"]."_products_cate":
					self::pc_nav($tb_name,$id_field,$id_value);
				break;
				case CORE::$config["prefix"]."_products":
					self::p_nav($tb_name,$id_field,$id_value);
				break;
			}
			
			if(CHECK::is_array_exist($nav)){
				self::make($nav);
			}
		}
		
		// 單層麵包屑
		private static function single_nav($tb_name,$id_field,$id_value,$field){
			$select = array (
				'table' => $tb_name,
				'field' => $field,
				'where' => $id_field."='".$id_value."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				$nav[] = array(
					'name' => $row["it_subject"],
					'link' => false,
				);
				
				return $nav;
			}
			
			return false;
		}
		
		// 產品分類麵包屑
		private static function pc_nav($tb_name,$id_field,$id_value,$output=false){
			
			static $nav;
			
			$select = array (
				'table' => $tb_name,
				'field' => '*',
				'where' => $id_field."='".$id_value."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				new SEO($row["seo_id"],false);
				$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["pc_id"];
				
				$nav[] = array(
					'name' => $row["pc_name"],
					'link' => CORE::$lang.'products/cate/'.$pointer,
				);
				
				if(!empty($row["pc_parent"])){
					self::pc_nav($tb_name,'pc_id',$row["pc_parent"],$output);
				}else{
					
					$nav[] = array(
						'name' => 'Products',
						'link' => CORE::$lang.'products/',
					);
					
					krsort($nav);
					
					// 刪除第三層分類之後的分類 (益詮特有設定)
					foreach($nav as $key => $nav_value_array){
						++$i;
						
						if($i > 3){
							unset($nav[$key]);
						}
					}
					
					if(!$output){
						self::make($nav);
					}else{
						self::$pc_nav = $nav;
					}
				}
				
			}
		}
		
		private static function p_nav($tb_name,$id_field,$id_value){
			
			$select = array (
				'table' => $tb_name,
				'field' => '*',
				'where' => $id_field."='".$id_value."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				self::pc_nav(CORE::$config["prefix"].'_products_cate','pc_id',$row["pc_id"],true);
				
				$nav = self::$pc_nav;
				$nav[] = array(
					'name' => $row["p_name"],
					'link' => CORE::$lang.'products/detail/'.$row["p_id"],
				);
				
				self::make($nav);
			}
		}
	}
	
?>