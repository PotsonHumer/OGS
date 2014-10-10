<?php

	class VIEW extends CORE{
		
		public static $output;
		public static $parameter;
		private static $tpl;
		
		// 主要樣板,附加樣板,輸出方式 (false => 直接輸出 , true => 回傳輸出),輸出樣板路徑以外的檔案
		function __construct($main_tpl='',$assing_tpl=array(),$output_type=false,$not_temp=false){
			
			// 輸出樣板路徑以外的檔案
			if($not_temp){
				self::$config["temp"] = '';
			}
			
			self::$tpl = new TemplatePower(self::$config["temp"].$main_tpl); // 註冊主要樣板
			
			// 附加樣板 (陣列輸入)
			if(is_array($assing_tpl) && count($assing_tpl) > 0){
				foreach($assing_tpl as $tpl_title => $tpl_path){
	        		self::$tpl->assignInclude($tpl_title,self::$config["temp"].$tpl_path);
				}
			}
	        self::$tpl->prepare();
			
			// 建立輸出功能
			if(is_array(self::$parameter) && count(self::$parameter) > 0){
				foreach(self::$parameter as $tpl_key => $tpl_array){
					$tpl_type = array_keys($tpl_array);
					$tpl_value = $tpl_array[$tpl_type[0]];
					
					switch($tpl_type[0]){
						case 0:
							self::assign_do($tpl_value,false);
						break;
						case 1:
							self::block_do($tpl_value,false);
						break;
						case 2:
							self::block_do($tpl_value,true);
						break;
						case 3:
							self::assign_do($tpl_value,true);
						break;
					}
				}
			}
			
			// 輸出
			if(!$output_type){
				self::$tpl->printToScreen();
			}else{
				self::$output = self::$tpl->getOutputContent();
			}
			
			self::$parameter = array();
		}
		
		#######################################################
		# 實際使用樣板功能
		
		// 啟動 Block 功能
		private static function block_do($tag_name='',$switch=false){
			if($switch){
				self::$tpl->gotoBlock($tag_name);
			}else{
				self::$tpl->newBlock($tag_name);
			}
		}
		
		// 啟動 assign 功能
		private static function assign_do(array $array,$switch=false){
			if($switch){
				self::$tpl->assignGlobal($array);
			}else{
				self::$tpl->assign($array);
			}
		}
		
		
		#######################################################
		# 組建樣板參數
		
		public function assign($value,$value_sec=''){
			if(is_array($value)){
				self::$parameter[][0] = $value;
			}else{
				self::$parameter[][0] = array($value => $value_sec);
			}
		}
		
		public function newBlock($tag=''){
			if(!empty($tag)){
				self::$parameter[][1] = $tag;
			}
		}
		
		public function gotoBlock($tag=''){
			if(!empty($tag)){
				self::$parameter[][2] = $tag;
			}
		}
		
		public function assignGlobal($value,$value_sec=''){
			if(is_array($value)){
				self::$parameter[][3] = $value;
			}else{
				self::$parameter[][3] = array($value => $value_sec);
			}
		}
	}
?>