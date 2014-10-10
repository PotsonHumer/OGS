<?php
	include_once($_SERVER['DOCUMENT_ROOT']."/core.php");
	
	$card = new CARD;
	
	class CARD{
		
		function __construct(){
			global $card_cfg,$main_cfg,$main,$red;
			
			$this->parameter_handle();
			//$main_cfg->lang;
			
			if($tpl_load){
				$tpl->printToScreen();
			}
		}
		
		// 參數處理
		function parameter_handle(){
			global $db,$main_cfg,$tpl,$main;
			
			if(is_array($main_cfg->parameter)){
				$func = $main_cfg->parameter[0];
				
				$this->page = '';
			}else{
				$func = ''; // 預設功能
			}
			
			switch($func){
				default:
					$this->tpl_load('temp/card_tpl.html');
					$tpl->printToScreen();
				break;
				case "ajax_input":
					$this->ajax_input();
				break;
			}
		}
		
		// 樣版讀取
	    function tpl_load($tpl_path=false){
	        global $main_cfg,$tpl,$main;
			
			$tpl = new TemplatePower('temp/main_tpl.html');
			if($tpl_path){
		        $tpl->assignInclude("MAIN",$tpl_path);
			}
			
			$tpl->prepare();
			$main->init_load();
			//$tpl->printToScreen();

			$this->seek_option();
			
			$tpl->assignGlobal(array(
				"TAG_MAIN_TITLE" => '卡片',
			));
			
			$main->jQuery_init("form");
			
			return true;
	    }
		
		// 項目設定
		function seek_option(){
			global $card_cfg,$main_cfg,$db,$tpl,$LANG;

			// 非預設項目
			for($mana_num=1;$mana_num<=20;$mana_num++){
				$card_cfg->mana[$mana_num] = ($mana_num <= 10 || $mana_num == 20)?$mana_num:'';
				$card_cfg->hp[$mana_num] = ($mana_num <= 12)?$mana_num:'';
				$card_cfg->attack[$mana_num] = ($mana_num <= 12)?$mana_num:'';
			}
			
			// 卡片預設項目
			foreach($card_cfg as $option => $value_array){
				if($option != 'field'){
					
					$option_str[$option] .= '<option value="NULL">'.$LANG[strtoupper($option)].'</option>';
					
					foreach($value_array as $key => $value){
						if(!empty($value)){
							$option_str[$option] .= '<option value="'.$key.'">'.$value.'</option>';
						}
					}
				}

				$tpl->assignGlobal("TAG_".strtoupper($option)."_OPTION",$option_str[$option]);
			}
		}
		
		// 卡片列表
		function init_list($v_array=''){
			global $db,$card_cfg,$main_cfg,$main;
			
			// 處理參數
			if(is_array($v_array)){
				foreach($v_array as $option => $option_value){
					
					$sk_search = strpos($option,"sk_");
					
					if($option_value != "NULL" && $sk_search !== false){
						$num++;
						
						$field = str_replace('sk_','',$option);

						if($option == "sk_name"){
							$sk_str_array[$num] = $field." like '%".$option_value."%'";
						}else{
							$sk_str_array[$num] = $field." = '".$option_value."'";
						}
					}
				}
				
				if(is_array($sk_str_array)){
					$sk_str = implode(' and ',$sk_str_array);
				}else{
					echo '請至少選取一個搜尋項目';
					exit;
				}
				
				// 儲存頁次於全域變數
				if(!empty($v_array["seek_page"])){
					$_REQUEST["page"] = str_replace('page-', '', $v_array["seek_page"]);
				}
			}else{
				echo '請至少選取一個搜尋項目';
				exit;
			}
			
			// 搜尋
			$sql = $db->select(array(
				'table' => "card",
				'fields' => "*",
				'condition' => $sk_str,
				'order' => "id asc",
				//'limit' => 0
			));
			
			$all_rsnum = $db->num($sql);

			// 頁次處理
			$page_link = $main_cfg->root.'card/'; // 頁次連結
			$sql = $main->init_page($db->select_str,$all_rsnum,8,$page_link,true);
			
			// 輸出
			while($row = $db->field($sql)){
				$rsnum++;
				echo $row['name'].'<br />';
			}
			
			if(empty($rsnum)){
				echo '無符合資料';
			}
			
			// 輸出頁次
			echo '<div id="page">'.$main->page_output.'</div>';
		}
		
		// AJAX 輸入
		function ajax_input(){
			global $main;
			
			$form = $main->ajax_form();
			
			$this->init_list($form);
		}
		
	}
	
?>