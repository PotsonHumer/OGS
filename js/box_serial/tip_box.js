// JavaScript Document

/* 使用方法

$(document or ".class or #id").tip_box({
	BG : "#FFF", //背景顏色
	BC : "#CCC", //邊框顏色
	COLOR : "#000", //文字顏色
	SHADOW : "#333", //陰影顏色
	DIR : "1", //出現位置 : 1 => 上方, 2 => 右方 , 3 => 下方, 4 => 左方
	VAL_FROM : "1", //內容取得位置 : 1 => rel參數, 2 => href參數 , 3 => src參數, 4 => value參數
	ACT_ID : "", //指定啟動元素 ID or Class (css 選取方法)
	VAL_ID : "", //指定內容元素 ID
	AFTER :  function() {  }, // 動作後執行擴充
},function(VAL){
	//callback
});

*/

(function($){
	$.fn.tip_box = function(OPTION,CALLBACK){
		var TIP = jQuery.extend({
			BG : "#FFF", //背景顏色
			BC : "#CCC", //邊框顏色
			COLOR : "#000", //文字顏色
			SHADOW : "#333", //陰影顏色
			DIR : "1", //出現位置 : 1 => 上方, 2 => 右方 , 3 => 下方, 4 => 左方
			VAL_FROM : "1", //內容取得位置 : 1 => rel參數, 2 => href參數 , 3 => src參數, 4 => value參數
			ACT_ID : "", //指定啟動元素 ID or Class (css 選取方法)
			VAL_ID : "", //指定內容元素 ID
			AFTER :  function() {  }, // 動作後執行擴充			
		}, OPTION);
		

		//----APPEND----//
		this.on("mouseenter",TIP.ACT_ID,function(){
			var TIP_NUM = $("div#tip_box").length;
			
			if(TIP_NUM < 1){
				$("body").append('<div id="tip_box"><p></p><span id="point_1"></span><span id="point_2"></span></div>');
			}
			$("div#tip_box").css({
				"display":"none",
				"position":"absolute",
				"white-space":"nowrap",
				"z-index":"99",
				"background":TIP.BG,
				"letter-spacing":"1px",
				"padding":"0 10px",
				"border":"2px solid "+ TIP.BC,
				"text-align":"center",
				"line-height":"15px",
				"border-radius":"5px",
				"color":TIP.COLOR,
				"box-shadow":"4px 2px 10px "+ TIP.SHADOW
			});
			$("div#tip_box > p").css({
				"font-size":"12px",
				"font-family":"Arial, Helvetica, sans-serif",
			});
			$("div#tip_box span#point_1,div#tip_box span#point_2").css({
				"width":"0px",
				"height":"0px",
				"border-width":"10px",
				"position":"absolute",
				"color":"#FFF",
			});
			
			switch(TIP.DIR){
				case "1":
					$("div#tip_box span#point_1,div#tip_box span#point_2").css({
						"border-style":"solid dashed dashed dashed",
						"left":"50%",
						"margin-left":"-10px",
					});
					
					$("div#tip_box span#point_1").css({
						"border-color":TIP.BG +" transparent transparent transparent",
						"bottom":"-20px",
					});
					$("div#tip_box span#point_2").css({
						"border-color":TIP.BC +" transparent transparent transparent",
						"margin-left":"-13px",
						"bottom":"-26px",
					});
				break;
				case "2":
					$("div#tip_box span#point_1,div#tip_box span#point_2").css({
						"border-style":"dashed solid dashed dashed",
						"top":"50%",
						"margin-top":"-10px",
					});
					
					$("div#tip_box span#point_1").css({
						"border-color":"transparent "+ TIP.BG +" transparent transparent",
						"left":"-20px",
					});
					$("div#tip_box span#point_2").css({
						"border-color":"transparent "+ TIP.BC +" transparent transparent",
						"margin-top":"-13px",
						"left":"-26px",
					});
				break;
				case "3":
					$("div#tip_box span#point_1,div#tip_box span#point_2").css({
						"border-style":"dashed dashed solid dashed",
						"left":"50%",
						"margin-left":"-10px",
					});
					
					$("div#tip_box span#point_1").css({
						"border-color":"transparent transparent "+ TIP.BG +" transparent",
						"top":"-20px",
					});
					$("div#tip_box span#point_2").css({
						"border-color":"transparent transparent "+ TIP.BC +" transparent",
						"margin-left":"-13px",
						"top":"-26px",
					});
				break;
				case "4":
					$("div#tip_box span#point_1,div#tip_box span#point_2").css({
						"border-style":"dashed dashed dashed solid",
						"top":"50%",
						"margin-top":"-10px",
					});
					
					$("div#tip_box span#point_1").css({
						"border-color":"transparent transparent transparent "+ TIP.BG,
						"right":"-20px",
					});
					$("div#tip_box span#point_2").css({
						"border-color":"transparent transparent transparent "+ TIP.BC,
						"margin-top":"-13px",
						"right":"-26px",
					});
				break;
			}
			
			$("div#tip_box span#point_1").css({
				"z-index":"99",
			});
			$("div#tip_box span#point_2").css({
				"border-width":"13px",
				"z-index":"98",
			});
			
			//----hover----//
			if(TIP.VAL_ID == ""){
				switch(TIP.VAL_FROM){
					case "1":
						var TIP_VAL = $(this).attr("rel");
					break;
					case "2":
						var TIP_VAL = $(this).attr("href");
					break;
					case "3":
						var TIP_VAL = $(this).attr("src");
					break;
					case "4":
						var TIP_VAL = $(this).val();
					break;
				}
			}else{
				var TIP_VAL = $("#"+ TIP.VAL_ID).html();
			}
			
			//CALLBACK(TIP_VAL);
	
			if(TIP_VAL != "" && typeof(TIP_VAL) != "undefined" && typeof(TIP_VAL) != "null" && TIP_VAL != "lightbox"){
				//----OJ----//
				var OJ_W = $(this).outerWidth();
				var OJ_H = $(this).outerHeight();
				var OJ_TOP = $(this).offset().top;
				var OJ_LEFT = $(this).offset().left
				
				$("div#tip_box p").html(TIP_VAL);
				
				switch(TIP.DIR){
					case "1":
						//----TIP_TOP----//
						var TIP_LEFT = OJ_LEFT + (OJ_W / 2);
						var TIP_W_HELF = $("div#tip_box").outerWidth() / 2;
						var TIP_H_HELF = 0;
						var TIP_H = $("div#tip_box").outerHeight();
						
						var TIP_TOP = OJ_TOP - (TIP_H + 15);
					break;
					case "2":
						//----TIP_RIGHT----//
						var TIP_LEFT = OJ_LEFT + (OJ_W + 15);
						var TIP_W_HELF = 0;
						var TIP_H_HELF = $("div#tip_box").outerHeight() / 2;
						var TIP_H = $("div#tip_box").outerHeight();
						
						var TIP_TOP = OJ_TOP + (OJ_H / 2);
					break;
					case "3":
						//----TIP_BOTTOM----//
						var TIP_LEFT = OJ_LEFT + (OJ_W / 2);
						var TIP_W_HELF = $("div#tip_box").outerWidth() / 2;
						var TIP_H_HELF = 0;
						var TIP_H = $("div#tip_box").outerHeight();
						
						var TIP_TOP = OJ_TOP + OJ_H + 15;
					break;
					case "4":
						//----TIP_LEFT----//
						var TIP_LEFT = OJ_LEFT - ($("div#tip_box").outerWidth() + 15);
						var TIP_W_HELF = 0;
						var TIP_H_HELF = $("div#tip_box").outerHeight() / 2;
						var TIP_H = $("div#tip_box").outerHeight();
						
						var TIP_TOP = OJ_TOP + (OJ_H / 2);
					break;
				}
							
				//----ACITVE----//			
				$("div#tip_box").css({
					"top":TIP_TOP +"px",
					"left":TIP_LEFT +"px",
					"margin-top":"-"+ TIP_H_HELF +"px",
					"margin-left":"-"+ TIP_W_HELF +"px"
				});
				
				$("div#tip_box").fadeIn(100);
			}
			
			TIP.AFTER();
			
		});
		
		//----DEACTIVE----//
		this.on("mouseleave",TIP.ACT_ID,function(){
			$("div#tip_box").hide().remove();
		});
	};
})(jQuery);
