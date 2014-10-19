	// OGS default js function
	
	function js_notice(MSG,HEADING){
		
		if(isset(MSG)){
			alert(MSG);
		}
		
		if(isset(HEADING)){
			location.href = HEADING;
		}
	}
	
	// 檢查變數是否存在
	function isset(ARGS){
		if(typeof(ARGS) != "undefined" && ARGS != '' && ARGS != "false"){
			return true;
		}else{
			return false;
		}
	}
