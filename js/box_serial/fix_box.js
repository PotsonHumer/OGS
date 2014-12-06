// JavaScript Document

/* 使用方法

$(".class or #id").fix_box();

*/

(function($){
	$.fn.fix_box = function(OPTION){
		/*
		var FIX = jQuery.extend({
		}, OPTION);
		*/
		
		var THIS = this;
		
		function log(OUTPUT){
			try{
				console.log(OUTPUT);
			}catch(e){
				alert(OUTPUT);
			}
		}
		
		// 高度置中
		function VRL_MID(OBJ,IMG_H){
			OBJ.find("img").css({
				"position":"absolute",
				"top":"50%",
				"left":"0",
				"margin-top":"-"+ (IMG_H / 2) +"px",
				"margin-left":"0",
			});
		}
		
		// 寬度置中
		function VRL_CENTER(OBJ,IMG_W){
			OBJ.find("img").css({
				"position":"absolute",
				"top":"0",
				"left":"50%",
				"margin-top":"0",
				"margin-left":"-"+ (IMG_W / 2) +"px",
			});
		}
		
		// 置中清除
		function VRL_RESET(OBJ){
			OBJ.find("img").css({
				"position":"absolute",
				"top":"0",
				"left":"0",
				"margin":"0",
			});
		}
		
		// 執行修正
		function NOW_FIX(OBJ,IMG,THIS_W,THIS_H){
			var IMG_W = IMG.width;
			var IMG_H = IMG.height;
			
			var RATIO_W = IMG_W / THIS_W;
			var RATIO_H = IMG_H / THIS_H;
			var TARGET_W = Math.round(IMG_W / RATIO_H);
			var TARGET_H = Math.round(IMG_H / RATIO_W);
			
			if(TARGET_H == THIS_H && TARGET_W == THIS_W){
				OBJ.find("img").css({
					"width":TARGET_H +"px",
					"height":TARGET_W +"px",
					"display":"block",
					"margin":"auto",
				});
				
				VRL_RESET(OBJ);
			}
			
			if(TARGET_H > THIS_H){
				OBJ.find("img").css({
					"width":"auto",
					"height":THIS_H +"px",
					"display":"block",
					"margin":"auto",
				});
				
				VRL_CENTER(OBJ,TARGET_W);
			}else{
				OBJ.find("img").css({
					"width":THIS_W +"px",
					"height":"auto",
					"display":"block",	
				});
				
				VRL_MID(OBJ,TARGET_H);
			}
		}
		
		// 重新讀取圖片
		function LOAD(OBJ,IMG,THIS_W,THIS_H){
			$(IMG).load(function(){
				NOW_FIX(OBJ,IMG,THIS_W,THIS_H);
			});
		}
		
		// 啟動
		function ACT(KEY){
			var OBJ = THIS.eq(KEY);
			var THIS_W = OBJ.width();
			var THIS_H = OBJ.height();
			var THIS_POSITION = OBJ.css("position");
			var IMG_SRC = OBJ.find("img").attr("src");
			
			switch(THIS_POSITION){
				default:
					OBJ.css({ "position":"relative" });
				break;
				case "absolute":
				break;
			}
			
			OBJ.find("img").hide();

			var IMG = new Image();
			IMG.src = IMG_SRC;
			
			if(IMG.complete == false){
				LOAD(OBJ,IMG,THIS_W,THIS_H);
			}else{
				NOW_FIX(OBJ,IMG,THIS_W,THIS_H);
			}
		}
		
		function _EACH(){
			THIS.each(function(KEY){
				ACT(KEY);
			});
		}
		
		_EACH();
		
		$(window).resize(function(){
			_EACH();
		});
	};
})(jQuery);