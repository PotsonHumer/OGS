	// OGS default js function
	
	function log(OUTPUT){
		try{
			console.log(OUTPUT);
		}catch(e){
			alert(OUTPUT);
		}
	}
	
	// js 提示功能
	function js_notice(MSG,HEADING){
		
		if(isset(MSG)){
			alert(MSG);
		}
		
		if(isset(HEADING)){
			location.href = HEADING;
		}
	}
	
	// js 連結
	function goto(){
		$(document).on("click",".goto",function(){
			var HEADING = $(this).attr("rel");
			
			if(isset(HEADING)){
				js_notice(false,HEADING);
			}
		});
	}
	
	// 檢查變數是否存在
	function isset(ARGS){
		if(typeof(ARGS) != "undefined" && ARGS != '' && ARGS != "false" && ARGS != 0){
			return true;
		}else{
			return false;
		}
	}
	
	// 更改 size 定義成像素寬度
	function pixels_size(){
		$("input,select,textarea").each(function(){
			var INPUT_SIZE = $(this).attr("size");
			var MULTI = $(this).attr("multiple");
			
			if(isset(INPUT_SIZE) && !isset(MULTI)){
				$(this).css({ "width":INPUT_SIZE +"px" });
			}
		});
	}
	
	// 功能處理
	function func_handle(){
		$(".func").click(function(E){
			E.preventDefault();
			if(!confirm('確定執行?')){
				return false;
			}
			
			var HANDLE_PATH = $(this).attr("rel");
			
			if(isset(HANDLE_PATH)){
				$("form[name=func_form]").attr("action",HANDLE_PATH);
				document.func_form.submit();
			}
		});
	}
	
	// 連結警告
	function link_alert(){
		$(document).on("click",".alert",function(E){
			var ALERT_MSG = $(this).attr("rel");
			
			if(!confirm(ALERT_MSG)){
				E.preventDefault();
			}
		});
	}
	
	// 全選功能
	function all_select(){
		$(document).on("click",".all",function(){
			var CK = $("input[type=checkbox].id").attr("checked");
			
			if(isset(CK) && CK == "checked"){
				$(".id").removeAttr("checked");
			}else{
				$(".id").attr("checked","checked");
			}
		});
	}
	
	// 圖片選取框
	var IMG_ARRAY = new Array();
	function img_block(){
		
		$(".img_block").each(function(KEY){
			var THIS = $(this);
			var IMG = $(this).find("img");
			var TXT = $(this).find("p");
			var IMG_PATH = IMG.attr("src");
			if(!isset(IMG_PATH)){
				IMG.hide();
				TXT.show();
			}else{
				IMG.show();
				TXT.hide();
			}
			
			var NOW_IMG_PATH = $(this).find("input.img").val();
			if(IMG_ARRAY[KEY] != NOW_IMG_PATH && isset(NOW_IMG_PATH)){
				var ROOT_PATH_EXIST = NOW_IMG_PATH.search(TAG_FILE_PATH);
				IMG_ARRAY[KEY] = (ROOT_PATH_EXIST >= 0)?NOW_IMG_PATH:TAG_ROOT_PATH + NOW_IMG_PATH;
				IMG.attr("src",IMG_ARRAY[KEY]);
				$(this).fix_box();
				
				// 取得圖片原始寬高
				img_load(IMG_ARRAY[KEY],function(IMG_W,IMG_H){
					var OBJ_W = THIS.find("input.width");
					var OBJ_H = THIS.find("input.height");
					var NOW_WIDTH = OBJ_W.val();
					var NOW_HEIGHT = OBJ_H.val();
					
					/*
					if(!isset(NOW_WIDTH)){
						OBJ_W.val(IMG_W);
					}
					
					if(!isset(NOW_HEIGHT)){
						OBJ_H.val(IMG_H);
					}
					*/
					
					THIS.find(".img_input[rel=width]").attr("placeholder",'原始寬度 : '+ IMG_W);
					THIS.find(".img_input[rel=height]").attr("placeholder",'原始高度 : '+ IMG_H);
				});
			}
		});
		
		setTimeout(img_block,50);
	}
	
	// 圖片選取框 - 刪除圖片
	function img_block_del(){
		$(document).on("click",".img_del",function(){
			var OBJ = $(this).parents(".img_block");			
			var IMG = OBJ.find("img");
			var TXT = OBJ.find("p");
			OBJ.find("input[type=text]").val("");
			IMG.removeAttr("src").hide();
			TXT.show();
		});
	}
	
	// 圖片選取框 - 圖片設定
	function img_block_setting(){
		
		// 開啟設定框
		$(document).on("click",".img_config",function(){
			pixels_size();
			
			var OBJ = $(this).parents(".img_block").find(".img_conf_block");
			var OBJ_BG = $(this).parents(".img_block").find(".img_conf_block_bg");
			
			if(isset(OBJ.length) && isset(OBJ_BG.length)){
				OBJ.fadeIn('fast');
				OBJ_BG.fadeIn('fast');
			}
		});
		
		// 關閉設定框
		$(document).on("click",".img_conf_cancel",function(){
			var OBJ = $(this).parents(".img_conf_block");
			OBJ.fadeOut('fast');
			OBJ.next(".img_conf_block_bg").fadeOut('fast');
		});
		
		// 輸入設定
		$(document).on("click",".img_conf_input",function(){
			var OBJ = $(this).parents(".img_conf_block");
			var OBJ_PARENT = $(this).parents(".img_block");
			
			OBJ.find(".img_input").each(function(){
				var MATCH_CLASS = $(this).attr("rel");
				var VAL = $(this).val();
				OBJ_PARENT.find("."+ MATCH_CLASS).val(VAL);
			});
			
			OBJ.find(".img_conf_cancel").trigger('click');
		});
	}
	
	// 圖片選取框 - 讀取尺寸
	function img_load(IMG_PATH,CALLBACK){
		
		if(isset(IMG_PATH)){
			var IMG = new Image;
			IMG.src = IMG_PATH;
			
			$(IMG).load(function(){
				CALLBACK(IMG.width,IMG.height);
			});
		}
	}
	
	// 快捷鍵
	function hotkey(){
		$(document).keyup(function(E){
			if(E.altKey){
				log(E.keyCode);
				
				switch(E.keyCode){
					case 81:
						var OBJ = $(".back");
					break;
					case 83:
						var OBJ = $(".save");
					break;
					case 49:
						//var OBJ = $("#nav_close");
					break;
				}
				
				if(isset(OBJ)){
					OBJ.trigger("click");
				}
			}
		});
	}
	
	// 直接啟動項目
	$(function(){
		pixels_size();
		goto();
		func_handle();
		link_alert();
		all_select();
		img_block();
		img_block_del();
		img_block_setting();
		hotkey();
	});
