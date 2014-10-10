<?php

	include_once("admin_system.php");
	
	$card = new CARD;
	
	class CARD{
		function __construct(){
			global $login,$tpl,$error;
			
			$login->reverify();
			
			if($login->status){
				switch($_REQUEST["func"]){
					default:
						$tpl_path = 'temp/card_tpl.html';
						$tpl_load = $this->tpl_load($tpl_path);
						$this->card_list();
					break;
					case "card_add":
					case "card_show":
						$tpl_path = 'temp/card_show_tpl.html';
						$tpl_load = $this->tpl_load($tpl_path);
						$this->card_mod();
					break;
					case "card_replace":
						$this->card_replace();
					break;
					case "card_del":
						$this->card_del();
					break;
					case "card_reval":
						//$this->card_reval();
					break;
				}
			}else{
				$error->error_handle();
			}
			
			if($tpl_load){
				$tpl->printToScreen();
			}
		}
		
		// 樣版讀取
	    function tpl_load($tpl_path=false){
	        global $tpl,$main;
			
			if($tpl_path){
		        $tpl = new TemplatePower('temp/main_tpl.html');
		        $tpl->assignInclude("MAIN",$tpl_path);
		        $tpl->prepare();
				$main->init_load();
				//$tpl->printToScreen();
			}
			
			return true;
	    }
		
		// 列表
		function card_list(){
			global $db,$tpl,$main_cfg,$card_cfg,$main;
			
			$sk_str = $this->card_search();
			
			$sql = $db->select(array(
				'table' => "card",
				'fields' => "*",
				'condition' => $sk_str,
				'order' => "id asc",
				//'limit' => 0
			));
			
			$all_rsnum = $db->num($sql);

			// page
			$page_link = $main_cfg->ad_root.'card.php';
			$sql = $main->init_page($db->select_str,$all_rsnum,$main_cfg->page_num,$page_link);
			
			while($row = $db->field($sql)){
				$tpl->newBlock("TAG_CARD_LIST");
				$tpl->assign(array(
					"VALUE_CARD_NAME" => $row["name"],
					"VALUE_CARD_TYPE" => $card_cfg->type[$row["type"]],
					"VALUE_CARD_CLASS" => $card_cfg->class[$row["class"]],
					"VALUE_CARD_QUALITY" => $card_cfg->quality[$row["quality"]],
					"VALUE_CARD_RACE" => $card_cfg->race[$row["race"]],
					"VALUE_CARD_LINK" => $main_cfg->ad_root.'card.php?func=card_show&id='.$row["id"],
					"VALUE_CARD_DEL" => $main_cfg->ad_root.'card.php?func=card_del&id='.$row["id"],
				));
			}
		}
		
		// 新增、修改
		function card_mod(){
			global $db,$tpl,$main_cfg,$card_cfg,$main,$error;
			
			$tpl->assignGlobal("TAG_PAGE",$_SESSION[$main_cfg->sess]["page"]);
			
			$sql = $db->select(array(
				'table' => "card",
				'fields' => "*",
				'condition' => "id='".$_REQUEST["id"]."'"
				//'rder' => "",
				//'limit' => 0
			));
			
			$rsnum = $db->num($sql);
			
			if($_REQUEST["func"] == "card_add"){
				$loop_array = array("type" => $card_cfg->type,"quality" => $card_cfg->quality,"class" => $card_cfg->class,"race" => $card_cfg->race);
				
				foreach($loop_array as $loop_key => $loop_data){
					unset($loop_str);
					foreach($loop_data as $key => $value){
						$loop_str[] = '<option value="'.$key.'">'.$value.'</option>';
						$tpl->assign("VALUE_".strtoupper($loop_key),implode("",$loop_str));
					}
				}
			}
			
			if(!empty($rsnum)){
				$row = $db->field($sql);
				
				foreach($row as $key => $value){
					switch($key){
						default:
							$loop_data = (is_null($row[$key]))?'NULL':$row[$key];
							$tpl->assignGlobal('VALUE_'.strtoupper($key),$loop_data);
						break;
						case "type":
							$loop_data = $card_cfg->type;
							$loop_verify = "type";
						break;
						case "quality":
							$loop_data = $card_cfg->quality;
							$loop_verify = "quality";
						break;
						case "class":
							$loop_data = $card_cfg->class;
							$loop_verify = "class";
						break;
						case "race":
							$loop_data = $card_cfg->race;
							$loop_verify = "race";
						break;
					}
					
					if(!empty($loop_verify)){
						foreach($loop_data as $key => $value){
							$current = ($key == $row[$loop_verify])?'selected':'';
							$loop_str[] = '<option value="'.$key.'" '.$current.'>'.$value.'</option>';
						}
						
						$tpl->assign("VALUE_".strtoupper($loop_verify),implode("",$loop_str));
						unset($loop_str,$loop_verify);
					}
				}
			}else{
				$error->error_handle("goto",'',$main_cfg->ad_root.'card.php');
			}
		}

		// 更新
		function card_replace(){
			global $db,$main_cfg,$card_cfg,$main;
			
			unset($_REQUEST["func"]);
			
			foreach($_REQUEST as $key => $value){
				switch($key){
					default:
						$replace_array[] = $key."='".$value."'";
					break;
					case "attack":
					case "mana":
					case "hp":
					case "gold_get_class":
					case "artist":
					case "story":
						$value = ($value == "NULL")?'NULL':"'".$value."'";
						$replace_array[] = $key.'='.$value;
					break;
				}
			}
			
			$db->replace("card",implode(",",$replace_array));
			
			if(!empty($_REQUEST["id"])){
				$goto = $main_cfg->ad_root."card.php?func=card_show&id=".$_REQUEST["id"];
			}else{
				$goto = $main_cfg->ad_root."card.php";
			}
			
			header("location: ".$goto);
		}
		
		// 刪除
		function card_del(){
			global $db,$tpl,$main_cfg,$card_cfg,$main;
			
			$del_str = "id = '".$_REQUEST["id"]."'";
			$db->delete("card",$del_str);
			
			header("location: ".$main_cfg->ad_root.'card.php?page='.$_SESSION[$main_cfg->sess]["page"]);
		}
		
		// 列表搜尋
		function card_search(){
			global $db,$card_cfg,$main_cfg,$tpl;
			
			$search_array = array($card_cfg->type,$card_cfg->class,$card_cfg->quality,$card_cfg->race);
			
			foreach($search_array as $key => $card_array){
				$key = $key + 1;
				
				foreach($card_array as $id_key => $str_value){
					$tpl->newBlock("TAG_SEARCH_LIST_".$key);
					$tpl->assign(array(
						"VALUE_SEARCH_ID" => $id_key,
						"VALUE_SEARCH_STR" => $str_value,
						//"VALUE_SEARCH_CURRENT" => 'selected':'',
					));
					
					if($_REQUEST["sk"][$key] == $id_key && !empty($_REQUEST["sk"]["callback"]) && $_REQUEST["sk"][$key] != "NULL"){
						$tpl->assign("VALUE_SEARCH_CURRENT",'selected');
						
						switch($key){
							case "1":
								$sk_array[$key] = "type = '".$id_key."'";
							break;
							case "2":
								$sk_array[$key] = "class = '".$id_key."'";
							break;
							case "3":
								$sk_array[$key] = "quality = '".$id_key."'";
							break;
							case "4":
								$sk_array[$key] = "race = '".$id_key."'";
							break;
						}
					}
					
					if(!empty($_SESSION[$main_cfg->sess]["sk"]) && empty($_REQUEST["sk"]["callback"])){
						$option_array = explode(" and ",$_SESSION[$main_cfg->sess]["sk"]);
						foreach($option_array as $option_key => $option_str){
							$value_array = explode(" = ",$option_str);
							$sk_value = str_replace("'", '', $value_array[1]);
							$sk_key = $value_array[0];
							
							switch($value_array[0]){
								case "type":
									$key_selected = 1;
								break;
								case "class":
									$key_selected = 2;
								break;
								case "quality":
									$key_selected = 3;
								break;
								case "race":
									$key_selected = 4;
								break;
							}
							
							if($key == $key_selected && $id_key == $sk_value){
								$tpl->assign("VALUE_SEARCH_CURRENT",'selected');
							}
						}
					}
				}
			}
			
			if(is_array($sk_array)){
				$sk_str = implode(' and ',$sk_array);
				return $_SESSION[$main_cfg->sess]["sk"] = $sk_str;
			}else{
				if(!empty($_REQUEST["sk"]["callback"])){
					unset($_SESSION[$main_cfg->sess]["sk"]);
				}else{
					return $_SESSION[$main_cfg->sess]["sk"];
				}
			}
		}
		
		// 更換數值為數字紀錄
		function card_reval(){
			global $db,$main_cfg,$card_cfg,$main;
			
			$sql = $db->select(array(
				'table' => "card",
				'fields' => "*",
				//'condition' => ""
				//rder' => "",
				//'limit' => 0
			));
			
			$rsnum = $db->num($sql);
			
			if(!empty($rsnum)){
				while($row = $db->field($sql)){
					
					switch($row["type"]){
						default:
						break;
						case "minion":
							$type = 0;
						break;
						case "spell":
							$type = 1;
						break;
						case "weapon":
							$type = 2;
						break;
					}
					
					switch($row["quality"]){
						default:
						break;
						case "basic":
							$quality = 0;
						break;
						case "common":
							$quality = 1;
						break;
						case "rare":
							$quality = 2;
						break;
						case "epic":
							$quality = 3;
						break;
						case "legendary":
							$quality = 4;
						break;
					}
					
					switch($row["race"]){
						default:
							$race = 0;
						break;
						case "Beast":
							$race = 1;
						break;
						case "Murloc":
							$race = 2;
						break;
						case "Pirate":
							$race = 3;
						break;
						case "Demon":
							$race = 4;
						break;
						case "Dragon":
							$race = 5;
						break;
					}
					
					switch($row["class"]){
						default:
						break;
						case "Neutral":
							$class = 0;
						break;
						case "Warrior":
							$class = 1;
						break;
						case "Shaman":
							$class = 2;
						break;
						case "Rogue":
							$class = 3;
						break;
						case "Paladin":
							$class = 4;
						break;
						case "Hunter":
							$class = 5;
						break;
						case "Druid":
							$class = 6;
						break;
						case "Warlock":
							$class = 7;
						break;
						case "Mage":
							$class = 8;
						break;
						case "Priest":
							$class = 9;
						break;
					}

					$update_str = "class = '".$class."',race = '".$race."',quality = '".$quality."',type = '".$type."'";
					$update_where = "id = '".$row["id"]."'";
					$db->update("card",$update_str,$update_where);
				}

				header("location: card.php");
			}
		}
	}
	
?>