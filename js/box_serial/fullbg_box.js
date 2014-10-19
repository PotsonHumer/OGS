// JavaScript Document

/* 使用方法

$("#id").fullbg_box({
	IMG : "", // 背景圖案 (使用 , 分隔不同圖檔路徑)
	ACT_SPEED : 1000, //動作花費時間
	ACT_TIMER : 3000, //動作間隔時間
},function(){
	//callback
});

*/

(function($){
	$.fn.fullbg_box = function(OPTION,CALLBACK){
		var BG = jQuery.extend({
			IMG : "", // 背景圖案 (使用 , 分隔不同圖檔路徑)
			ACT_SPEED : 1000, //動作花費時間
			ACT_TIMER : 3000, //動作間隔時間
			
			//----
			TIMER : ""
		}, OPTION);
		
		var THIS = this;
		var IMG_ARRAY = new Array();
		IMG_ARRAY = BG.IMG.split(",");
		
		var THIS_POSITION =  THIS.css("position");
		
		if(THIS_POSITION != "relative" &&  THIS_POSITION != "absolute"){
			THIS.css({
				"position":"relative",
			});
		}
		
		function log(OUTPUT){
			try{
				console.log(OUTPUT);
			}catch(e){}
		}
		
		function INITIALIZE(REPEAT){
			switch(REPEAT){
				default:
					var ALL_IMG_STR = "";
					
					$.each(IMG_ARRAY,function(KEY,VALUE){
						if(KEY == 0){
							ALL_IMG_STR = ALL_IMG_STR + '<img class="fullbg_img current" src="'+ VALUE +'">';
						}else{
							ALL_IMG_STR = ALL_IMG_STR + '<img class="fullbg_img" src="'+ VALUE +'">';
						}
					});
					
					THIS.append(
						'<div class="fullbg_box">'
							+ ALL_IMG_STR +
						'</div>'
					);
						
					$(THIS).find(".fullbg_box").css({
						"position":"absolute",
						"width":"100%",
						"height":"100%",
						"top":"0",
						"left":"0",
						"overflow":"hidden",
						"z-index":"-1"
					});
					
					$(THIS).find(".fullbg_img").css({
						"position":"absolute",
						"width":"100%",
						"height":"auto",
						"top":"0",
						"left":"0",
					});
				
					$(THIS).find(".fullbg_img").not(".fullbg_img.current").css({
						//"display":"none",
						"opacity":"0",
					});
					
					IMG_RESIZE();
				break;
				case 1:
					
				break;
				case 2:
					$(THIS).find(".fullbg_img").css({
						"position":"absolute",
						"width":"100%",
						"height":"auto",
						"top":"0",
						"left":"0",
					});
				
					$(THIS).find(".fullbg_img").not(".fullbg_img.current").css({
						"opacity":"0",
					});
				break;
			}
			
			//?
		}
		
		function FADE_DELAY(){
			BG.TIMER = setTimeout(FADE_ROW,BG.ACT_TIMER);
		}
		
		function FADE_ROW(){
			var ALL_IMG_NUM = $(THIS).find(".fullbg_img").length;
			
			if(ALL_IMG_NUM <= 1){
				clearTimeout(BG.TIMER);
				return false;
			}
			
			var IMG_INDEX = $(THIS).find(".fullbg_img").index($(THIS).find(".fullbg_img.current"));
			var LAST_INDEX = ALL_IMG_NUM - 1;
			
			$(THIS).find(".fullbg_img.current").removeClass("current").animate({ "opacity":"0" },BG.ACT_SPEED);
			
			if(IMG_INDEX < LAST_INDEX){
				$(THIS).find(".fullbg_img:eq("+ (IMG_INDEX - - 1) +")").addClass("current").animate({ "opacity":"1" },BG.ACT_SPEED);
				var NOW_INDEX = IMG_INDEX - - 1;
			}else{
				$(THIS).find(".fullbg_img:eq(0)").addClass("current").animate({ "opacity":"1" },BG.ACT_SPEED);
				var NOW_INDEX = 0;
			}
			
			INITIALIZE(1);
			POSITION();
			IMG_RESIZE();
			
			CALLBACK(NOW_INDEX);
			
			FADE_DELAY();
		}
		
		function IMG_RESIZE(){
			var THIS_W = THIS.outerWidth();
			var THIS_H = THIS.outerHeight();
			
			$(THIS).find(".fullbg_img").each(function(KEY){
				var BG_PATH = $(this).attr("src");
				
				var IMG = new Image();
				IMG.src = BG_PATH;
				
				if(IMG.complete == false){
					$(this).load(function(){
						BG_W = this.width;
						BG_H = this.height;
						
						IMG_RESIZE_COUNT(THIS_W,THIS_H,BG_W,BG_H,KEY);
					});
				}else{
					var BG_W = $(this).width();
					var BG_H = $(this).height();
					
					IMG_RESIZE_COUNT(THIS_W,THIS_H,BG_W,BG_H,KEY);
				}
			});
		}
		
		function IMG_RESIZE_COUNT(THIS_W,THIS_H,BG_W,BG_H,KEY){
			
			// 框大於圖
			if(THIS_H > BG_H){
				var RATIO = THIS_H / BG_H;
				var FINAL_W = Math.round(BG_W * RATIO);
				
				$(THIS).find(".fullbg_img:eq("+ KEY +")").css({
					"width":FINAL_W + "px",
					"height":THIS_H +"px",
					"top":"50%",
					"left":"50%",
					"margin-top":"-"+ (THIS_H / 2) +"px",
					"margin-left":"-"+ (FINAL_W / 2) +"px",
				});
			}
			
			// 圖大於框
			if(BG_H > THIS_H){
				$(THIS).find(".fullbg_img:eq("+ KEY +")").css({
					"top":"50%",
					"margin-top":"-"+ (BG_H / 2) +"px",
					"margin-left":"0"
				});
			}
		}
		
		function POSITION(){
			var THIS_TAG = $(THIS)[0].tagName;
			
			if(THIS_TAG == "BODY"){
				$("body > .fullbg_box").css({
					"position":"fixed",
				});
			}else{
				var NOW_TOP = THIS.scrollTop();
				$(THIS).find(".fullbg_box").css({
					"top":NOW_TOP +"px"
				});
				
				THIS.scroll(function(){
					NOW_TOP = THIS.scrollTop();
					$(THIS).find(".fullbg_box").css({
						"top":NOW_TOP +"px"
					});
					
				});
			}
		}
		
		return this.each(function(){
			INITIALIZE();
			POSITION();
			FADE_DELAY();
			
			$(window).resize(function(){
				INITIALIZE(2);
				POSITION();
				
				IMG_RESIZE();
			});
		});
	};
})(jQuery);
