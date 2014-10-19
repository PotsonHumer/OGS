// JavaScript Document

/* 使用方法

$(".class or #id").move_box({
	HOR_BOR : 0, // 水平方向留白
	VEL_BOR : 0, // 垂直方向留白
	HORIZON : true, // 水平移動  , true => 啟用 , false => 停用
	VERTICAL : true, // 垂直移動  , true => 啟用 , false => 停用
});

*/

(function($){
	$.fn.move_box = function(OPTION){
		var MOV = jQuery.extend({
			HOR_BOR : 0, // 水平方向留白
			VEL_BOR : 0, // 垂直方向留白
			HORIZON : true, // 水平移動  , true => 啟用 , false => 停用
			VERTICAL : true, // 垂直移動  , true => 啟用 , false => 停用
		}, OPTION);
		
		var THIS = this;
						
		return this.each(function(KEY){
	        $(this).mousemove(function(E){
	        	var INNER_W = $(this).find(".inner").outerWidth() + MOV.HOR_BOR;
	        	var INNER_H = $(this).find(".inner").outerHeight() + MOV.VEL_BOR;
	        	var BLOCK_W = $(this).outerWidth();
	        	var BLOCK_H = $(this).outerHeight();
	        	
	        	if(INNER_W > BLOCK_W && MOV.HORIZON){
		        	var PAGE_L = $(this).offset().left;
		        	var PAGE_X = E.pageX;
		        	
		        	var BLOCK_X = PAGE_X - PAGE_L;
		        	
					var BLOCK_RATIO_X = BLOCK_W / 100;
					var INNER_PER_X = BLOCK_X / BLOCK_RATIO_X;
					var MOVE_W = INNER_W - BLOCK_W;
					var MOVE_RATIO_X = MOVE_W / 100;
					var MOVE_NOW_X = 0 - (INNER_PER_X * MOVE_RATIO_X);
					
					$(this).find(".inner").css({ "left":MOVE_NOW_X +"px" });
					$(this).find(".reinner").css({ "right":MOVE_NOW_X +"px" });
				}
				
				if(INNER_H > BLOCK_H  && MOV.VERTICAL){
		        	var PAGE_T = $(this).offset().top;
		        	var PAGE_Y = E.pageY;
		        	
		        	var BLOCK_Y = PAGE_Y - PAGE_T;
		        	
					var BLOCK_RATIO_Y = BLOCK_H / 100;
					var INNER_PER_Y = BLOCK_Y / BLOCK_RATIO_Y;
					var MOVE_H = INNER_H - BLOCK_H;
					var MOVE_RATIO_Y = MOVE_H / 100;
					var MOVE_NOW_Y = 0 - (INNER_PER_Y * MOVE_RATIO_Y);
					
					$(this).find(".inner").css({ "top":MOVE_NOW_Y +"px" });
					$(this).find(".reinner").css({ "bottom":MOVE_NOW_Y +"px" });
				}
	        });
		});
	};
})(jQuery);