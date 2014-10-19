// JavaScript Document

/* 使用方法

$("#id").marquee_box({
	ACT_TIMER : 40, //每像素移動時間
	HOVER : false, // 滑鼠hover停止動作 , true => 停止 , false => 不停止
	AFTER :  function() {  }, // 動作後執行擴充
});

*/

(function($){
	$.fn.marquee_box = function(OPTION){
		var MARQUEE = jQuery.extend({
			ACT_TIMER : 40, //每像素移動時間
			HOVER : false, // 滑鼠hover停止動作 , true => 停止 , false => 不停止
			AFTER :  function() {  }, // 動作後執行擴充
			
			//----
			THIS_W : 0,
			THIS_H : 0,
			TIMER : 0,
		}, OPTION);
		
		var THIS = this;
		
		var THIS_POSITION =  THIS.css("position");
		
		if(THIS_POSITION != "relative" &&  THIS_POSITION != "absolute"){
			THIS.css({
				"position":"relative",
			});
		}
		
		function MARQUEE_DELAY(){
			MARQUEE.TIMER = setTimeout(ACTIVE,MARQUEE.ACT_TIMER);
		}
		
		function ACTIVE(){
			var MARQUEE_LEFT = THIS.find("p").css("left");
			var MARQUEE_W = THIS.find("p").outerWidth();
			MARQUEE_LEFT = MARQUEE_LEFT.replace("px","");
			var MARQUEE_NEXT = MARQUEE_LEFT - 1;
			var LIMIT_LEFT = 0 - MARQUEE_W;
			
			if(MARQUEE_LEFT > LIMIT_LEFT){
				THIS.find("p").css({ "left":MARQUEE_NEXT +"px" });
			}else{
				THIS.find("p").css({ "left":"100%" });
				
				MARQUEE.AFTER();
			}
			
			MARQUEE_DELAY();
		}			
		
		return this.each(function(){
			var MAQ_CONTENT = THIS.html();
			MARQUEE.THIS_W = THIS.width();
			MARQUEE.THIS_H = THIS.height();
			
			THIS.html("").append('<p>'+ MAQ_CONTENT +'</p>');
			THIS.find("p").css({ 
				"height":MARQUEE.THIS_H +"px",
				"line-height":MARQUEE.THIS_H +"px",
				"margin":"0",
				"padding":"0",
				"position":"absolute",
				"top":"0",
				"left":MARQUEE.THIS_W +"px",
				"white-space":"nowrap",
			});
			
			MARQUEE_DELAY();
			
			if(MARQUEE.HOVER == true){
				THIS.find("p").hover(function(){
					clearTimeout(MARQUEE.TIMER);
				},function(){
					MARQUEE_DELAY();
				});
			}
		});
	};
})(jQuery);
