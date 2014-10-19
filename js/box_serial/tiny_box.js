// JavaScript Document

/* 使用方法

$(document).tiny_box({
	SWITCH : 0, // 0 => 全部 , 1 => 圖片 , 2 => 檔案
	ID : ".tiny", // 綁定元素 (同css選取器)，可設定多個元素，使用 , 分隔
	ROOT : "" // 根目錄位置
});

*/

(function($){
	$.fn.tiny_box = function(OPTION){
		var TINY = jQuery.extend({
			SWITCH : 0, // 0 => 全部 , 1 => 圖片 , 2 => 檔案
			ID : ".tiny", // 綁定元素 (同css選取器)，可設定多個元素，使用 , 分隔
			ROOT : "" // 根目錄位置
			//----
		}, OPTION);
		
		//var THIS = this;
		var THIS_ARRAY = new Array();
		THIS_ARRAY = TINY.ID.split(",");
		
		for(var I=0;I<THIS_ARRAY.length;I++){
			$(document).on("click",THIS_ARRAY[I],function(E){
				E.preventDefault();
				
				//利用 rel 指定對象 ID
				var INPUT_CH = $(this).attr("rel");
				var INPUT_ID = $(this).prev("input").attr("id");
				
				//判斷有無 ID 沒有給個暫代值
				if((!INPUT_ID || INPUT_ID == "temp_select") && !INPUT_CH){
					$("#temp_select").attr("id","");
					$(this).prev("input").attr("id","temp_select");
					INPUT_ID = "temp_select";
				}

				//判斷有無 ID 如果有直接給值
				if(INPUT_CH){
					INPUT_ID = INPUT_CH;
				}
				
				open_popup(TINY.ROOT +'filemanager/dialog.php?type='+ TINY.SWITCH +'&fldr=&popup=1&field_id='+ INPUT_ID +'&lang=zh_TW')
			});
		}
	};
})(jQuery);