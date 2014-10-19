// JavaScript Document

/* 使用方法

	$("#id or .class").float_box({
		IMG : "", // 背景圖案 (使用 , 分隔不同圖檔路徑)
		ACT_TIMER : 3000, //動作間隔時間
	});

	**********************************************************************
	INFO :
	
	此特效利用 CSS3 效果完成，必須要最新的瀏覽器才可支援
	此特效包含 float_box.css 檔案，需一起載入使用
*/

(function($){
	$.fn.float_box = function(OPTION){
		var FLOAT = jQuery.extend({
			IMG : "", // 背景圖案 (使用 , 分隔不同圖檔路徑)
			ACT_TIMER : 3000, //動作間隔時間
			
			//----
			ARRAY : new Array(),
			SIZE : new Array(),
			TIMER : "",
			KEY : 0,
			NUM : 0,
		}, OPTION);
		
		var THIS = $(this);
		
		//-------------------------------------------------
		
		function log(OUTPUT){
			try{
				console.log(OUTPUT);
			}catch(e){
				alert(OUTPUT);
			}
		}
		
		// 小數點位數保留
		function round(num, pos){
			var size = Math.pow(10, pos);
			return Math.round(num * size) / size;
		}
		
		//-------------------------------------------------
		
		// 時間控制
		function DELAY(){
			FLOAT.TIMER = setTimeout(ROW,FLOAT.ACT_TIMER);
		}
		
		// 換頁
		function ROW(){
			clearTimeout(FLOAT.SIZE_TIMER);
			
			if(FLOAT.KEY < (FLOAT.NUM - 1)){
				FLOAT.KEY++;
			}else{
				FLOAT.KEY = 0;
			}
			
			var PREV_KEY = (FLOAT.KEY - 1 < 0)?FLOAT.NUM - 1:FLOAT.KEY - 1;
			var FADE_SPEED = round(FLOAT.ACT_TIMER / 3,0);
			
			THIS.find(".float_box:eq("+ PREV_KEY +")").fadeOut(FADE_SPEED,function(){
				RESET(PREV_KEY);
			});
			THIS.find(".float_box:eq("+ FLOAT.KEY +")").fadeIn(FADE_SPEED,function(){
				SIZEUP();
				DELAY();
			});
		}
		
		// 恢復尺寸
		function RESET(KEY){
			THIS.find(".float_img:eq("+ KEY +")").removeClass("float_act");
		}
		
		// 放大特效
		function SIZEUP(){
			THIS.find(".float_img:eq("+ FLOAT.KEY +")").addClass("float_act");
		}
		
		// 初始化
		function INIT(){
			FLOAT.ARRAY = FLOAT.IMG.split(',');
			FLOAT.NUM = FLOAT.ARRAY.length;
			
			FLOAT.SIZE[0] = THIS.outerWidth();
			FLOAT.SIZE[1] = THIS.outerHeight();
			var FADE_SPEED = round(FLOAT.ACT_TIMER / 3,0);
			
			$.each(FLOAT.ARRAY,function(KEY,PATH){
				if(KEY != 0){
					var INSERT_DISPLAY = 'style="display: none;"';
				}
				THIS.append('<div class="float_box" '+ INSERT_DISPLAY +'><div class="float_img" style="background: url('+ PATH +') no-repeat center center;"></div><div>');	
			});
			
			THIS.css({
				"position":"relative",
				"overflow":"hidden",
			});
			
			THIS.find(".float_box").css({
				"position":"absolute",
				"width":FLOAT.SIZE[0] +"px",
				"height":FLOAT.SIZE[1] +"px",
				"top":"0",
				"left":"0",
				"z-index":"-1",
			});
			
			THIS.find(".float_img").css({
				"position":"relative",
				"width":FLOAT.SIZE[0] +"px",
				"height":FLOAT.SIZE[1] +"px",
				"top":"0",
				"left":"0",
				"background-size":"cover",
			});
			
			THIS.find(".float_box:eq(0)").show();
			
			SIZEUP();
			DELAY();
		}
		
		INIT();
	};
})(jQuery);