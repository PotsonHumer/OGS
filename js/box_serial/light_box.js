// JavaScript Document

/* 使用方法
	$(".class or #id").light_box();
	
	or 
	
	$("*[rel=light_box]").light_box();
	
	!!!注意!!!
	* 需與 slide_box , fix_box 功能一同使用 *
*/


(function($){
	$.fn.light_box = function(OPTION){
		var LIGHT = jQuery.extend({
			//AFTER :  function() {  }, // 動作後執行擴充
			
			//----
			WIN_W : "",
			IMG : new Array(),
			ELM : "",
			NUM : "",
			INDEX : "",
		}, OPTION);
		
		var THIS = this;
		
		//載入大圖
		function main_act(){
			var NOW_IMG_INDEX = $("#light_box .slide_pic.current").index("#light_box .slide_pic");
			var NOW_IMG_SRC = $("#light_box .slide_pic.current > div > img").attr("src");
			
			$("#light_box > #main").html("").append(
				'<img src="'+ NOW_IMG_SRC +'" />'
			);
			
			$("#light_box > #main > img").load(function(){
				var BIG_W = this.width;
				var MAIN_H = $("#light_box > #main").height();
				
				if(BIG_W > LIGHT.WIN_W){
					$(this).css({
						"width":LIGHT.WIN_W +"px",
						"margin-left":"-"+ Math.round(LIGHT.WIN_W / 2) +"px",
					});
				}else{
					$(this).css({
						"margin-left":"-"+ Math.round(BIG_W / 2) +"px",
					});
				}
				var BIG_H = this.height - -200;
								
				if(BIG_H > MAIN_H){
					$("#light_box > #main > img").css({ "padding":"100px 0" });
					$("#light_box > #main").mousemove(function(VALUE){
						var MOS_Y = VALUE.clientY;
						var MOV_H = BIG_H - MAIN_H
						var MAIN_RATIO = MAIN_H / 100;
						var MOV_RAITO = MOV_H / 100;
						var NOW_RATIO = MOS_Y / MAIN_RATIO;
						var MOV_TARGET = NOW_RATIO * MOV_RAITO;
						
						
						//$("#light_box > #main").html(MOV_TARGET);
						
						$("#light_box > #main > img").css({ "top":"-"+ MOV_TARGET +"px" });
					});
				}else{
					$("#light_box > #main").unbind();
				}
			});
		}

		//觸發
		THIS.click(function(E){
			E.preventDefault();
			
			//初始化數值
			LIGHT.IMG = new Array();
			LIGHT.ELM = "";
			LIGHT.NUM = "";
			LIGHT.WIN_W = $(window).width();
			LIGHT.INDEX = $(THIS).index(this);
			
			var SLIDE_H = Math.round($(window).height() / 100 * 20);
			var IMG_W = Math.round(LIGHT.WIN_W / 6);
			
			//取得所有圖檔路徑
			THIS.each(function(KEY){
				LIGHT.IMG[KEY] = $(this).attr("href");
			});
			
			$.each(LIGHT.IMG,function(KEY,VALUE){
				LIGHT.ELM = LIGHT.ELM + '<li class="slide_pic key" rel="'+ (KEY - -1) +'"><div><img src="'+ VALUE +'" /></div></li>';
				LIGHT.NUM = KEY - -1;
			});
			
			//載入元素
			$("body").append(
				'<div id="light_box">'+
					'<div id="main"></div>'+
					'<div class="arrow" id="arrow_1"> ＜ </div>'+
					'<div class="arrow" id="arrow_2"> ＞ </div>'+
					'<div id="slide">'+
						'<ul class="slide_move">'
							+ LIGHT.ELM +
						'</ul>'+
					'</div>'+
					'<div id="close">Close</div>'+
					'<div id="bg">&nbsp;</div>'+
				'</div>'
			);
			
			//初始化 slide
			$("#light_box > #slide > .slide_move > .slide_pic").css({
				"width":IMG_W +"px",
				"height":SLIDE_H +"px",
			});
			
			$("#light_box > #slide > .slide_move > .slide_pic > div").css({
				"width":IMG_W - 30 +"px",
				"height":SLIDE_H - 30 +"px",
			});
			
			/*
			$("#light_box > #slide > .slide_move > .slide_pic > img").css({
				"width":IMG_W +"px",
				"position":"absolute",
				"top":"0",
				"left":"0",
			});
			*/
			
			if(LIGHT.NUM < 6){
				$("#light_box > #slide").css({
					"width": IMG_W * LIGHT.NUM +"px",
					"left":"50%",
					"margin-left":"-"+ Math.round(IMG_W * LIGHT.NUM / 2) +"px",
				});
			}
			
			if(LIGHT.NUM <= 1){
				$("#light_box > #slide").hide();
				$("#light_box > #main").css({ "height":"100%" });
			}else{
				$("#light_box > #slide").show();
			}
			
			//崁入 slide
			$("#light_box").slide_box({
				SHOW_NUM : 6, //一次顯示數量
				OUTER_WIDTH : 0, //額外間距
				ACT_TIMER : 3000, //動作間隔時間
				POSITION : LIGHT.INDEX, //起始位置
				AUTO : false, // true => 自動動作 , false => 手動動作
				WIDTH : IMG_W, //圖片大小
				HOVER : false, // 滑鼠hover停止動作 , true => 停止 , false => 不停止
				CYCLE : true, // 循環 / 回放切換 , true => 循環 , false => 回放
				VERTICAL : false, //移動方向 , true => 垂直 , false => 水平
				AFTER : function() { main_act() } // 動作後執行擴充
			});
			
			//修正 slide img
			/*
			$("#light_box > #slide > .slide_move > .slide_pic > img").each(function(){
				$(this).load(function(){
					var SLIDE_IMG_H = this.height;
					
					if(SLIDE_IMG_H > SLIDE_H){
						$(this).css({ "top":"50%","margin-top":"-"+ Math.round(SLIDE_IMG_H / 2) +"px" })
					}else{
						$(this).css({ "width":"auto","height":"100%" })
					}
				});
			});
			*/
			
			$("#light_box > #slide > .slide_move > .slide_pic > div").fix_box();
			
			//啟動
			main_act();
			$("#light_box").fadeIn("slow");
			
			//關閉
			$("#light_box > #close").click(function(){
				$("#light_box").fadeOut("slow",function(){
					$(this).remove();
				});
			});
		});
	};
})(jQuery);
