<?php
	include_once($_SERVER['DOCUMENT_ROOT']."/core.php");
	
	$comment = new COMMENT;
	
	class COMMENT{
		
		function __construct(){
			global $card_cfg,$main_cfg,$main,$red;
			
			$main->sess_loader();
			$this->parameter_handle();
			//$main_cfg->lang;
			
			if($tpl_load){
				$tpl->printToScreen();
			}
			
			echo date("Y-m-d");
		}
				
		// 參數處理
		function parameter_handle(){
			global $db,$main_cfg,$tpl,$main;
			
			if(is_array($main_cfg->parameter)){
				$func = $main_cfg->parameter[0];
				$args = $main_cfg->parameter[1];
			}else{
				$func = ''; // 預設功能
			}
			
			switch($func){
				case "class":
					$this->deck_seek($args);
				default:
					$this->tpl_load('temp/comment_tpl.html');
					$this->init_list();
					$tpl->printToScreen();
				break;
				case "detail":
					$this->deck_detail($args);
				break;
				case "new":
					$this->tpl_load('temp/comment_form_tpl.html');
					$this->new_comment();
					$tpl->printToScreen();
				break;
				case "replace":
					$this->new_comment_replace();
				break;
				case "ajax_deck_search":
					$this->ajax_deck_search();
				break;
				case "ajax_deck_insert":
					$this->ajax_deck_insert();
				break;
				case "ajax_deck_output":
					$this->ajax_deck_output();
				break;
				case "ajax_deck_del":
					$this->ajax_deck_del();
				break;
				/*
				case "ajax_input":
					$this->ajax_input();
				break;
				*/ 
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
				
			$tpl->assignGlobal(array(
				"TAG_MAIN_TITLE" => '牌組列表',
			));
			
			$main->jQuery_init("get");
			
			return true;
	    }
		
		//---- 備註
		#職業選擇先顯示
		
		// 牌組搜尋
		function deck_seek($args=0){
			global $card_cfg,$main_cfg,$db,$tpl,$LANG;
						
			$sql = $db->select(array(
				'table' => "comment_deck",
				'fields' => "*",
				'condition' => "cmd_class='".$args."'",
				//'order' => "",
				//'limit' => 0
			));
			
			$rsnum = $db->num($sql);
			
			if(!empty($rsnum)){
				while($row = $db->field($sql)){
					$mc_id_array[] = $row["mc_id"];
				}
				
				$this->mc_id_str = implode("','",$mc_id_array);
			}
		}
		
		// 評論列表
		function init_list(){
			global $db,$card_cfg,$main_cfg,$main,$tpl;
			
			if(is_array($card_cfg->class)){
				foreach($card_cfg->class as $key => $class){
					if(!empty($key)){
						$tpl->newBlock("TAG_CLASS_LIST");
						$tpl->assign(array(
							"VALUE_CLASS_NAME" => $class,
							"VALUE_CLASS_LINK" => $main_cfg->root.'comment/class/'.$key,
						));
					}
				}
			}
			
			if(!empty($this->mc_id_str)){
				$sk_str = "mc_id in('".$this->mc_id_str."')";
			}
			
			$sql = $db->select(array(
				'table' => "comment",
				'fields' => "*",
				'condition' => $sk_str,
				'order' => "cm_date desc",
				//'limit' => 0
			));
			
			$all_rsnum = $db->num($sql);

			// 頁次處理
			$page_link = $main_cfg->root.'comment/'; // 頁次連結
			$sql = $main->init_page($db->select_str,$all_rsnum,20,$page_link,true);
			
			// 輸出
			while($row = $db->field($sql)){
				$rsnum++;
			}
			
			if(empty($rsnum)){
				echo '無符合資料';
			}
			
			// 輸出頁次
			echo '<div id="page">'.$main->page_output.'</div>';
		}

		// 內頁顯示
		function deck_detail($args=0){
			global $db,$main,$main_cfg;
			
			$sql = $db->select(array(
				'table' => "comment",
				'fields' => "*",
				'condition' => "cm_id='".$args."'",
				'order' => "cm_date desc",
				//'limit' => 0
			));
			
			$rsnum = $db->num($sql);
			
			if(!empty($rsnum)){
				while($row = $db->field($sql)){
					
				}
			}else{
				header("location: ".$main_cfg->root.'comment/');
			}
		}
		
		// 新評論
		function new_comment(){
			global $db,$cms_cfg,$main_cfg,$tpl,$main,$LANG,$card_cfg;
			
			$main->jQuery_init("form","get");
			
			if(!empty($main->sess["m_id"])){
				$tpl->assignGlobal(array(
					"TAG_MAIN_TITLE" => '新增牌組',
				));
				
				// 職業選擇
				foreach($card_cfg->class as $class_id => $class){
					$tpl->newBlock("TAG_CLASS_LIST");
					$tpl->assign(array(
						"VALUE_CLASS_ID" => $class_id,
						"VALUE_CLASS_STR" => $class,
					));
				}
				
				// 耗能選擇
				for($mana=0;$mana<=20;$mana++){
					$tpl->newBlock("TAG_AMOUNT_LIST");
					$tpl->assign("VALUE_AMOUNT_NUM",$mana);
				}
				
				if(is_array($_SESSION[$this->sess]["post"])){
					foreach($_SESSION[$this->sess]["post"] as $field => $post_value){
						$tpl->assignGlobal("VALUE_".strtoupper($field),$post_value);
						unset($_SESSION[$this->sess]["post"]);
					}
				}
			}else{
				$main->js_notice($LANG["LOGIN_FIRST"],$main_cfg->root.'member/');
			}
		}
		
		// 新增評論
		function new_comment_replace(){
			global $db,$LANG,$main_cfg,$main;
			
			if(!is_array($_SESSION[$this->sess]["deck"])){
				$main->js_notice('你不可能組成這樣的牌組 囧rz',$main_cfg->root.'comment/new/');
				$return_false = true;
			}
			
			if(array_sum($_SESSION[$this->sess]["deck"]) != 30){
				$main->js_notice('你不可能組成這樣的牌組 囧rz',$main_cfg->root.'comment/new/');
				$return_false = true;
			}
			
			if($return_false){
				$_SESSION[$this->sess]["post"] = $_POST;
				return false;
			}
			
			if(!empty($main->sess["m_id"])){
				$args["m_id"] = $main->sess["m_id"];
				$args["cm_title"] = $_POST["cm_title"];
				$args["cm_content"] = $_POST["cm_content"];
				$args["cm_date"] = @date("Y-m-d H:i:s");
				
				$db->insert('comment',$args);
				$rs_msg  = $db->error();
				
				if(empty($rs_msg)){
					$cm_id = $db->get_id();
					unset($args);
					$args["cm_id"] = $cm_id;
					$args["cmd_class"] = '';
					$args["id_str"] = serialize($_SESSION[$this->sess]["deck"]);
					
					$db->insert('comment_deck',$args);
					$rs_msg  = $db->error();
					
					if(!empty($rs_msg)){
						$main->js_notice($rs_msg,$main_cfg->root.'comment/');
						return false;
					}
					
					$main->js_notice($LANG["INSERT_SUCC"],$main_cfg->root.'comment/');
					unset($_SESSION[$this->sess]["deck"]);
				}else{
					$main->js_notice($rs_msg,$main_cfg->root.'comment/');
				}
			}else{
				$main->js_notice($LANG["LOGIN_FIRST"],$main_cfg->root.'member/');
			}
		}
		
		// AJAX 牌組搜尋
		function ajax_deck_search(){
			global $db,$main,$main_cfg,$card_cfg;
			
			$form = $main->ajax_form();
			foreach($form as $key => $value){
				$value = ($value == "null")?null:$value;
				
				if(!is_null($value)){
					$sql_str_array[] = $key."='".$value."'";
				}
			}
			
			if(is_array($sql_str_array)){
				$sql_str = implode(" and ",$sql_str_array);
			}
			
			$sql = $db->select(array(
				'table' => "card",
				'fields' => "*",
				'condition' => $sql_str,
				'order' => "mana asc,quality asc",
				//'limit' => 0
			));
			
			$rsnum = $db->num($sql);
			
			if(!empty($rsnum)){
				while($row = $db->field($sql)){
					$card_list_array[] = '<li class="card_id" rel="'.$row["id"].'"><span>'.$row["name"].'</span></li>';
				}
			}
			
			if(is_array($card_list_array)){
				echo implode("\n",$card_list_array);
			}
		}

		// AJAX 牌組紀錄
		function ajax_deck_insert(){
			global $db,$main_cfg,$card_cfg,$main;

			if(is_array($_SESSION[$this->sess]["deck"])){
				$deck_num = array_sum($_SESSION[$this->sess]["deck"]); // 全部牌數
			}
			
			$card_array = $main->card_load($_REQUEST["call"]);
			
			if($deck_num < 30 && ($card_array[0]["quality"] != 4 && $_SESSION[$this->sess]["deck"][$_REQUEST["call"]] < 2 || $card_array[0]["quality"] == 4 && $_SESSION[$this->sess]["deck"][$_REQUEST["call"]] < 1)){
				$_SESSION[$this->sess]["deck"][$_REQUEST["call"]]++;
				echo 1;
			}else{
				// 超過 30 張 或 單一種超過2張
				echo 0;
			}
		}
		
		// AJAX 牌組輸出
		function ajax_deck_output(){
			global $db,$main_cfg,$card_cfg,$main;
			
			if(is_array($_SESSION[$this->sess]["deck"]) && count($_SESSION[$this->sess]["deck"]) > 0){
				foreach($_SESSION[$this->sess]["deck"] as $id => $num){
					$card_array = $main->card_load($id);
					$card = $card_array[0];
					
					// output
					$list_array[] = '<li rel="'.$card["id"].'">'.$card["name"].' * '.$num.'</li>';
				}
				
				echo implode("\n",$list_array);
				return true;
			}
			
			echo false;
			return false;
		}
		
		// AJAX 牌組刪除
		function ajax_deck_del(){
			global $db,$main_cfg,$card_cfg,$main;
			
			if(!empty($_REQUEST["call"])){
				$_SESSION[$this->sess]["deck"][$_REQUEST["call"]] = $_SESSION[$this->sess]["deck"][$_REQUEST["call"]] - 1;
				
				if($_SESSION[$this->sess]["deck"][$_REQUEST["call"]] <= 0){
					unset($_SESSION[$this->sess]["deck"][$_REQUEST["call"]]);
				}
				
				if(count($_SESSION[$this->sess]["deck"]) <= 0){
					unset($_SESSION[$this->sess]["deck"]);
				}
				
				echo 1;
			}
		}
		
	}
	
?>