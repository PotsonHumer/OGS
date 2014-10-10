<?php
	include_once($_SERVER['DOCUMENT_ROOT']."/core.php");
	
	$member = new MEMBER;
	
	class MEMBER{
		function __construct(){
			global $main,$card_cfg,$main_cfg,$main,$red;
			
			$main->sess_loader();
			$this->parameter_handle();
			//$main_cfg->lang;
			
			if($tpl_load){
				$tpl->printToScreen();
			}
			
			// 會員詳細必做事項
			# 站內信
			# 等級
			# 提醒功能
		}
		
		// 參數處理
		function parameter_handle(){
			global $db,$main_cfg,$tpl,$main;
			
			if(is_array($main_cfg->parameter)){
				$func = $main_cfg->parameter[0];
			}else{
				$func = ''; // 預設功能
			}
			
			switch($func){
				default:
					if(empty($main->sess["m_id"])){
						header("location: ".$main_cfg->root.'member/login/');
						exit;
					}else{
						$this->tpl_load('temp/member_main_tpl.html');
						$this->member_main();
						$tpl->printToScreen();
					}
				break;
				case "login":
					$this->tpl_load('temp/member_login_tpl.html');
					$this->member_login();
					$tpl->printToScreen();
				break;
				case "register":
					$this->tpl_load('temp/member_form_tpl.html');
					$this->member_register();
					$tpl->printToScreen();
				break;
				case "mod":
					$this->tpl_load('temp/member_form_tpl.html');
					$this->member_mod();
					$tpl->printToScreen();
				break;
				case "mailbox":
					$this->member_mailbox();
				break;
				/*
				case "new_comment":
					$this->new_comment();
				*/
				break;
				case "logout":
					$this->member_logout();
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
			
			return true;
	    }
		
		//-----------------------------------
		
		// 主頁資訊
		function member_main(){
			
		}
		
		// 會員登入
		function member_login(){
			global $db,$main_cfg,$tpl,$main,$error,$LANG;
			
			if(!empty($_POST["m_callback"]) && $this->parameter_ckeck(true)){
				$sql_str = "m_account='".$_POST["m_account"]."' and m_password='".md5($_POST["m_password"])."'";
				$sql = $db->select(array(
					'table' => "member",
					'fields' => "*",
					'condition' => $sql_str,
					//'order' => "",
					//'limit' => 0
				));
				
				$rsnum = $db->num($sql);
				
				if($rsnum == 1){
					$row = $db->field($sql);
					
					foreach($row as $field_name => $value){
						$_SESSION[$main_cfg->sess][$field_name] = $value;
					}
					
					$main->js_notice($LANG["LOGIN_SUCC"],$main_cfg->root.'member/');
				}else{
					$error->error_handle('goto',$LANG["ER_LOGIN"],$main_cfg->root);
				}
			}
		}
		
		// 會員註冊
		function member_register(){
			global $db,$main_cfg,$tpl,$error,$main,$LANG;
			
			$tpl->newBlock("TAG_REGISTER_PASSWORD");
			$tpl->assignGlobal("TAG_M_FUNC",'register');

			if(!empty($_POST["m_callback"]) && $this->parameter_ckeck()){
				$value_combin = array(
					'm_account' => $_POST["m_account"],
					'm_password' => md5($_POST["m_password"]),
					'm_name' => $_POST["m_name"],
					'm_ip' => $main->get_ip(),
					'm_date' => @date("Y-m-d H:i:s"),
					'm_tag' => $_POST["m_tag"],
				);
				
				$db->insert('member',$value_combin);
				$rs_msg  = $db->error();
				
				if(empty($rs_msg)){
					$main->js_notice($LANG["REG_SUCC"],$main_cfg->root.'member/login/');
				}else{
					$error->error_handle('goto',$rs_msg,$main_cfg->root.'member/register/');
				}
			}
		}
		
		// 會員修改資料
		function member_mod(){
			global $db,$main_cfg,$tpl,$main,$LANG,$error;

			if(!empty($_POST["m_callback"])){
				$_POST["m_account"] = $main->sess["m_account"];
				
				if((empty($_POST["m_passchange"]) || md5($_POST["m_password_origin"]) == $main->sess["m_password"] && preg_match('/^[A-Za-z0-9]{4,20}/',$_POST["m_password"]) && $_POST["m_password"] === $_POST["m_password_vail"] && !empty($_POST["m_password"])) && preg_match('/^[x00-xff_a-zA-Z0-9]+$/',$_POST["m_name"])){
					
					if(empty($_POST["m_passchange"])){
						$update_str = "m_name = '".$_POST["m_name"]."',m_tag = '".$_POST["m_tag"]."'";
					}else{
						$update_str = "m_name = '".$_POST["m_name"]."',m_password = '".md5($_POST["m_password"])."',m_tag = '".$_POST["m_tag"]."'";
					}
					
					$update_where = "m_id = '".$main->sess["m_id"]."'";
					$db->update("member",$update_str,$update_where);
					$rs_msg  = $db->error();
					
					if(empty($rs_msg)){
						$main->js_notice($LANG["MOD_SUCC"],$main_cfg->root.'member/');
					}else{
						$error->error_handle('goto',$rs_msg,$main_cfg->root.'member/mod/');
					}
				}else{
					$error->error_handle('goto',$LANG["MOD_FAIL"],$main_cfg->root.'member/mod/');
				}
				
				exit;
			}
			
			$tpl->newBlock("TAG_MOD_PASSWORD");
			
			$sql_str = "m_id='".$main->sess["m_id"]."'";
			$sql = $db->select(array(
				'table' => "member",
				'fields' => "*",
				'condition' => $sql_str,
				//'order' => "",
				//'limit' => 0
			));
			
			$rsnum = $db->num($sql);
			
			if($rsnum == 1){
				$row = $db->field($sql);
				
				$tpl->assignGlobal(array(
					"TAG_M_FUNC" => 'mod',
					"VALUE_M_ACCOUNT" => $row["m_account"],
					"VALUE_M_NAME" => $row["m_name"],
					"VALUE_M_TAG" => $row["m_tag"],
				));
			}else{
				$error->error_handle('goto',$LANG["LOGIN_FAIL"],$main_cfg->root.'member/logout/');
				exit;
			}
		}
		
		// 參數檢查
		function parameter_ckeck($func_switch=false){
			global $main_cfg,$LANG,$tpl;
			
			$ck_rs[1] = preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/', $_POST["m_account"]); // m_account
			$ck_rs[2] = (preg_match('/^[A-Za-z0-9]{4,20}/',$_POST["m_password"]) && ($_POST["m_password"] === $_POST["m_password_vail"] || $func_switch))?true:false; // m_password
			$ck_rs[3] = (preg_match('/^[x00-xff_a-zA-Z0-9]+$/',$_POST["m_name"]) || $func_switch)?true:false; // m_name
			
			foreach($ck_rs as $key => $rs){
				if(!$rs){
					$error_option = $key;
				}
			}
			
			switch($error_option){
				case 1:
					$error_str = $LANG['ER_ACC'];
				break;
				case 2:
					$error_str = $LANG['ER_PSW'];
				break;
				case 3:
					$error_str = $LANG['ER_NAME'];
				break;
				default:
					return true;
				break;
			}
			
			$tpl->assignGlobal(array(
				"VALUE_ERROR_MSG" => $error_str,
				"VALUE_M_ACCOUNT" => $_POST["m_account"],
				"VALUE_M_PASSWORD" => $_POST["m_password"],
				"VALUE_M_NAME" => $_POST["m_name"],
			));
			
			return false;
		}

		// 會員站內信箱
		function member_mailbox(){
			global $db,$main,$main_cfg,$tpl;
			
			
		}
		
		// 新評論
		/*
		function new_comment(){
			global $db,$main_cfg,$main,$LANG;
			
			$args["m_id"] = $main->sess["m_id"];
			$args["cm_title"] = $_POST["cm_title"];
			$args["cm_content"] = $_POST["cm_content"];
			$args["cm_date"] = @date("Y-m-d H:i:s");
			
			$db->insert('comment',$args);
			$rs_msg  = $db->error();
			
			if(empty($rs_msg)){
				$main->js_notice($LANG["REG_SUCC"],$main_cfg->root.'member/');
			}else{
				$main->js_notice($rs_msg,$main_cfg->root.'member/');
			}
		}
		*/
		
		// 會員登出
		function member_logout(){
			global $main,$main_cfg;
			
			session_destroy();
			
			//$main->sess_loader();
			unset($main->sess);
			
			header("location: ".$main_cfg->root);
			exit;
		}
		
		// 檔案上傳
		function file_upload(){
	        global $db,$main_cfg;
			if($_FILES["cu_file"]){
				$file = $_FILES["cu_file"];
				foreach($file as $key => $V){
					$text = "file_".$key;
					$$text = $V;
				}
				
				$num = 1;
				for($i=0;$i<count($file_name);$i++){
					$file_size_mega = ($file_size[$i] / 1024 / 1024);
					$file_name_array = explode(".",$file_name[$i]);
					$sub_name = $file_name_array[count($file_name_array) - 1];
					
					switch($file_type[$i]){
						default:
							$file_type_ck = false;
						break;
						case "image/jpeg":
						case "image/png":
						case "image/gif":
							$file_type_ck = true;
						break;
					}
					
					if($file_error[$i] == 0 && $file_name[$i] != "" && $sub_name != "exe" && $file_size_mega < 3 && $file_type_ck){
						$date_name[$i] = date("Y-m-d-H-i-s")."-file".$num.".".$sub_name;
						$route = "avatar/".$date_name[$i];
						move_uploaded_file($file_tmp_name[$i],$route);
						$num++;
					}
				}
				
				if(!empty($date_name)){
					foreach($date_name as $key => $V){
						$file_str[] = "<a href=\"".$main_cfg->url."/avatar/".$V."\" target=\"_blank\">".$V."</a>";
					}
					$this->cu_file = implode(" , ",$file_str);
					return implode("|",$date_name);
				}
			}
		}
	}
?>
