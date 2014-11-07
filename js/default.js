	// OGS default js function
	
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
		if(typeof(ARGS) != "undefined" && ARGS != '' && ARGS != "false"){
			return true;
		}else{
			return false;
		}
	}
	
	// 更改 size 定義成像素寬度
	function pixels_size(){
		$("input,select,textarea").each(function(){
			var INPUT_SIZE = $(this).attr("size");
			
			if(isset(INPUT_SIZE)){
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
	
	// 直接啟動項目
	$(function(){
		pixels_size();
		goto();
		func_handle();
		link_alert();
	});
