// JavaScript Document

// 主要功能
function alert_msg(E,MSG){
	if(!confirm(MSG)){
		E.preventDefault();
	}
}

function back_to_list(OBJ,PATH){
	$(OBJ).click(function(E){
		E.preventDefault();
		location.href = PATH;
	});
}


$(function(){
	// 刪除警示
	$(".del").click(function(E){
		var MSG = '確定刪除此項目?';
		alert_msg(E,MSG);
	});

	// 表格分色
	$("table").each(function(){
		$(this).find("tr").each(function(KEY){
			if(KEY % 2 == 0){
				$(this).css({ "background":"#ECECEC" });
			}
		});
	});
});
