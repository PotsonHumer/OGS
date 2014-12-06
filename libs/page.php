<?php

	class PAGE{
		
		public static $page_args;
		public static $sql;
		private static $now;
		
		function __construct(){} // No need
		
		// 啟動頁次功能
		public static function handle($sql,$link){
			$item_num = DB::num($sql); // 取得所有資料數
			
			// 確定資料數
			if($item_num > CORE::$config["item_num"]){
				
				// 資料數超過一頁
				$page_num = ceil($item_num / CORE::$config["item_num"]); // 總頁次
				self::$page_args = (empty(self::$page_args))?1:self::$page_args; // 如果沒有頁次參數強制為 1
				self::$now = str_replace('page-', '', self::$page_args);
				
				self::make_link($page_num,$link); // 產生頁次連結
				return self::sql_handle($page_num); // 處理 sql select
			}else{
				// 資料數低於單頁最大值
				return $sql;
			}
		}
		
		// 產生頁次連結
		private static function make_link($page_num,$link){
			
			// 求得目前頁次顯示區段
			$section = (CORE::$config["list_num"] < self::$now)?ceil(self::$now / CORE::$config["list_num"]):1;
			$p_start = ($section * CORE::$config["list_num"] - CORE::$config["list_num"]) + 1;
			$section_end = ($p_start + 9);
			$p_end = ($section_end > $page_num)?$page_num:$section_end;
			
			// 頁次連結
			for($p=$p_start;$p<=$p_end;$p++){
				if(self::$now == $p){
					$now_p = 'class="current"';
				}else{
					$now_p = '';
				}
				
				$link_str .= '<li><a '.$now_p.' href="'.$link.'page-'.$p.'/">'.$p.'</a></li>';
			}
			
			// 往前連結
			if(self::$now > 1){
				$prev = self::$now - 1;
				$prev_str = '<li><a href="'.$link.'page-'.$prev.'/"> << </a></li>';
			}
			
			// 往後連結
			if(self::$now < $page_num){
				$next = self::$now + 1;
				$next_str = '<li><a href="'.$link.'page-'.$next.'/"> >> </a></li>';
			}
			
			$page_str = $prev_str.$link_str.$next_str; // 組合連結
			
			VIEW::newBlock("TAG_PAGE_BLOCK");
			VIEW::assign("TAG_PAGE_STR",$page_str);
		}
		
		// sql select
		private static function sql_handle($page_num){
			
			$start_num = CORE::$config["item_num"] * self::$now - CORE::$config["item_num"]; // 起始值
			
			$sql_str = DB::$sql;
			$sql_str .= " limit ".$start_num.",".CORE::$config["item_num"];
			
			DB::$sql = $sql_str;
			return DB::execute($sql_str);
		}
	}

?>