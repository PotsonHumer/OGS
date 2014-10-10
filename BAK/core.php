<?php
	include_once($_SERVER['DOCUMENT_ROOT']."/libs/system.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/error.php");
	
	$error = new error;
	$main = new MAIN;
	
	class MAIN{
		function __construct(){}
		
		// 共享功能設定
		function init_load(){
			global $db,$main_cfg,$card_cfg,$error,$tpl,$login;
			
			// 初始樣版設定
			$tpl->assignGlobal(array(
				"TAG_ROOT_PATH" => $main_cfg->root,
				"TAG_ADMIN_PATH" => $main_cfg->ad_root,
				"TAG_IMG_PATH" => $main_cfg->img,
				"TAG_CSS_PATH" => $main_cfg->css,
				"TAG_JS_PATH" => $main_cfg->js,
				"TAG_URL_PATH" => $main_cfg->url.$main_cfg->root,
			));
		}
		
		// 分頁
		function init_page($sql_str=0,$all_num=0,$page_num=0,$page_link=0,$switch_tpl=false){
			global $db,$tpl,$main_cfg;
			
			if(!empty($sql_str) && !empty($all_num) && !empty($page_num)){
				$all_page = ceil($all_num / $page_num);

				if(!empty($_REQUEST["page"])){
					$start_num = ($page_num * $_REQUEST["page"]) - $page_num;
					$end_num = $page_num;
					$_SESSION[$main_cfg->sess]["page"] = $_REQUEST["page"];
				}else{
					$start_num = 0;
					$end_num = $page_num;
					unset($_SESSION[$main_cfg->sess]["page"]);
				}
				
				for($p=1;$p<=$all_page;$p++){
					$page_link_str = (!empty($page_link))?$page_link.'page-'.$p.'/':'page-'.$p.'/';
					
					if(!$switch_tpl){
						$tpl->newBlock("TAG_PAGE_LIST");
						$tpl->assign(array(
							"VALUE_P_NUM" => $p,
							"VALUE_P_LINK" => $page_link_str,
						));
					}else{
						$this->page_output_array[] = '<a href="'.$page_link_str.'">'.$p.'</a>';
						
						if($p == $all_page){
							$this->page_output = implode('',$this->page_output_array);
						}
					}
				}
				
				$sql_str = $sql_str.'limit '.$start_num.','.$end_num;
				return $db->execute($sql_str);
			}
		}
		
		// 麵包屑
		function init_layer(){
			global $db,$tpl,$main_cfg;
			
			//?
		}
		
		// AJAX 表單輸入
		function ajax_form(){
			if(is_array($_REQUEST["val"])){
				foreach($_REQUEST["val"] as $key => $array){
					if(!empty($array["value"]) || $array["value"] == "0"){
						if(empty($form[$array["name"]])){
							$form[$array["name"]] = $array["value"];
						}else{
							if(!is_array($form[$array["name"]])){
								$sub_array = $form[$array["name"]];
								unset($form[$array["name"]]);
								
								$form[$array["name"]][] = $sub_array;
								$form[$array["name"]][] = $array["value"];
							}else{
								$form[$array["name"]][] = $array["value"];
							}
						}
					}
				}
				
				return $form;
			}else{
				return false;
			}
		}
		
		// session 物件化
		function sess_loader(){
			global $main_cfg;
			
			if(is_array($_SESSION[$main_cfg->sess])){
				foreach($_SESSION[$main_cfg->sess] as $key => $value){
					$this->sess[$key] = $value;
				}
			}
			
			return false;
		}
		
		// session
		/* 
		function sess_builder($key,$value){
			global $main_cfg;
			
			$_SESSION[$main_cfg->sess][$key]= $value;
			
			// session reloader
			$this->sess_loader();
			return false;
		}
		*/
		
		// jQuery 載入模組
		function jQuery_init(){
			global $tpl,$main_cfg;

			$file_title = func_get_args();
			
			if(count($file_title)){
				foreach($file_title as $key => $value){
					$js_insert .= "<script src=\"".$main_cfg->js.$value."_box.js\" type=\"text/javascript\"></script>\n";
				}
				
				$tpl->assignGlobal("TAG_JS_BOX",$js_insert);
			}		
		}
		
		// 取得 IP
		function get_ip(){
			if(!empty($_SERVER['HTTP_CLIENT_IP'])){
				return $_SERVER['HTTP_CLIENT_IP'];
			}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}else{
				return $_SERVER['REMOTE_ADDR'];
			}
		}
		
		// JS 警告顯示
		function js_notice($msg,$goto_url){
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
			echo "<script language=javascript>";
			echo "Javascript:alert(\"".$msg."\")";
			echo "</script>";
			echo "<script language=javascript>";
			echo "document.location='".$goto_url."'";
			echo "</script>";
		}
		
		// 讀取卡牌
		function card_load($id,$order=''){
			global $db,$main_cfg,$card_cfg;
			
			if(is_array($id)){
				$sql_str = "id in ('".implode("','",$id)."')";
			}else{
				$sql_str = "id='".$id."'";
			}
			
			if(empty($order)){
				$order = "mana asc,quality asc";
			}
			
			$sql = $db->select(array(
				'table' => "card",
				'fields' => "*",
				'condition' => $sql_str,
				'order' => $order,
				//'limit' => 0
			));
			
			$rsnum = $db->num($sql);
			
			if(!empty($rsnum)){
				while($row = $db->field($sql)){
					$all_row[] = $row;
				}
				
				return $all_row;
			}else{
				return false;
			}
		}
	}
?>