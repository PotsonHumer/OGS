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
	
	// 直接啟動項目
	$(function(){
		pixels_size();
		goto();
	});
