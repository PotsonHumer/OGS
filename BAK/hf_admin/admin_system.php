<?php
	include_once($_SERVER['DOCUMENT_ROOT']."/libs/system.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/hf_admin/admin_login.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/hf_admin/admin_error.php");
	
	$login = new login;
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
			
			// 登出
			if(!empty($_REQUEST["logout"])){
				$login->logout();
			}
		}
		
		function init_page($sql_str=0,$all_num=0,$page_num=0,$page_link=0){
			global $db,$tpl,$main_cfg;
			
			if(!empty($sql_str) && !empty($all_num) && !empty($page_num)){
				$all_page = ceil($all_num / $page_num);

				if(!empty($_REQUEST["page"])){
					$start_num = ($main_cfg->page_num * $_REQUEST["page"]) - $main_cfg->page_num;
					$end_num = $main_cfg->page_num;
					$_SESSION[$main_cfg->sess]["page"] = $_REQUEST["page"];
				}else{
					$start_num = 0;
					$end_num = $main_cfg->page_num;
					unset($_SESSION[$main_cfg->sess]["page"]);
				}
				
				for($p=1;$p<=$all_page;$p++){
					$tpl->newBlock("TAG_PAGE_LIST");
					$tpl->assign(array(
						"VALUE_P_NUM" => $p,
						"VALUE_P_LINK" => (strpos($page_link, '?'))?$page_link.'&page='.$p:'?page='.$p,
					));
				}
				
				$sql_str = $sql_str.'limit '.$start_num.','.$end_num;
				return $db->execute($sql_str);
			}
		}
	}

?>