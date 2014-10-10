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
		
		//高度置中
		function VRL_MID(OBJ,IMG_H){
			OBJ.find("img").css({
				"position":"absolute",
				"top":"50%",
				"margin-top":"-"+ (IMG_H / 2) +"px",
			});
		}
		
		//重新讀取圖片
		function LOAD(OBJ,IMG,THIS_W,THIS_H){
			$(IMG).load(function(){
				var IMG_W = IMG.width;
				var IMG_H = IMG.height;
				
				var RATIO_W = IMG_W / THIS_W;
				var TARGET_H = Math.round(IMG_H / RATIO_W);
				
				if(TARGET_H > THIS_H){
					OBJ.find("img").css({
						"width":"auto",
						"height":THIS_H +"px",
						"display":"block",
						"margin":"auto",
					});
				}else{
					OBJ.find("img").css({
						"width":THIS_W +"px",
						"height":"auto",
						"display":"block",	
					});
					
					VRL_MID(OBJ,TARGET_H);
				}
			});
		}
		
		//啟動
		function ACT(KEY){
			var OBJ = THIS.eq(KEY);
			var THIS_W = THIS.width();
			var THIS_H = THIS.height();
			var IMG_SRC = OBJ.find("img").attr("src");
			
			THIS.css({ "position":"relative" });
			OBJ.find("img").hide();
			
			var IMG = new Image();
			IMG.src = IMG_SRC;
			
			if(IMG.complete == false){
				LOAD(OBJ,IMG,THIS_W,THIS_H);
			}else{
				var IMG_W = IMG.width;
				var IMG_H = IMG.height;
				
				var RATIO_W = IMG_W / THIS_W;
				var TARGET_H = Math.round(IMG_H / RATIO_W);
				
				if(TARGET_H > THIS_H){
					OBJ.find("img").css({
						"width":"auto",
						"height":THIS_H +"px",
						"display":"block",
						"margin":"auto",
					});
				}else{
					OBJ.find("img").css({
						"width":THIS_W +"px",
						"height":"auto",
						"display":"block",	
					});
					
					VRL_MID(OBJ,TARGET_H);
				}
			}
		}
		
		return this.each(function(KEY){
			ACT(KEY);
			
		});
	};
})(jQuery);