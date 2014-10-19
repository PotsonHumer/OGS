// JavaScript Document

/* 使用方法

$(".class or #id").default_box({
	ACTIVE : "#666", // 註解文字顏色
	DEACTIVE : "#000", //輸入文字顏色
});

*/

(function($){
	$.fn.default_box = function(OPTION){
		var DEF= jQuery.extend({
			ACTIVE : "#666", // 註解文字顏色
			DEACTIVE : "#000", //輸入文字顏色
		}, OPTION);
		
		var THIS = this;
						
		return this.each(function(KEY){
			var INPUT_VAL = $(this).val();
			
			if(INPUT_VAL != ""){
				$(this).css({ "color":DEF.ACTIVE });
			}
			
			$(this).focus(function(){
				var NOW_VAL = $(this).val();
				
				if(INPUT_VAL == NOW_VAL){
					$(this).val("");
					$(this).css({ "color":DEF.DEACTIVE });
				}
			});
			
			$(this).blur(function(){
				var NOW_VAL = $(this).val();
				
				if(NOW_VAL == ""){
					$(this).val(INPUT_VAL);
					$(this).css({ "color":DEF.ACTIVE });
				}
			});
			
			//for chrome
			$(this).css({ "outline":"none" });
		});
	};
})(jQuery);