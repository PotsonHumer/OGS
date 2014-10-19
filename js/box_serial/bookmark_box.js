// JavaScript Document

/* 使用方法

	$("#id").bookmark_box({
		GAP : 10, // 書籤與內容間距寬度 (px)
		AUTO : true, // true => 自動動作 , false => 手動動作
		HOVER : false, // 滑鼠hover停止動作 , true => 停止 , false => 不停止
		DELAY : 1000, // 動作間隔時間
	});

	**********************************************************************
	INFO :
	
*/

(function($){
	$.fn.bookmark_box = function(OPTION){
		var BM = jQuery.extend({
			GAP : 10, // 書籤與內容間距寬度 (px)
			AUTO : true, // true => 自動動作 , false => 手動動作
			HOVER : false, // 滑鼠hover停止動作 , true => 停止 , false => 不停止
			DELAY : 1000, // 動作間隔時間
			
			//----
			TIME : "",
			MOVE_W : "",
			CLICK : 1,
			NOD : new Array(),
		}, OPTION);
		
		var THIS = $(this);
		
		function log(OUTPUT){
			try{
				console.log(OUTPUT);
			}catch(e){
				alert(OUTPUT);
			}
		}
		
		// 自動循環
		function DELAY(){
			BM.TIME = setTimeout(MOVE,BM.DELAY);
		}
		
		// 移動
		function MOVE(){
			THIS.find(".bm_block").each(function(KEY){
				if(KEY <= BM.CLICK){
					// 移動
					$(this).stop().animate({ "left":BM.NOD[KEY] - (BM.MOVE_W - -BM.GAP) +"px" });
				}else{
					// 回到 NOD 位置
					$(this).stop().animate({ "left":BM.NOD[KEY] +"px" });
				}
			});
			
			MARK_HOVER();
			
			if(BM.AUTO){
				if(BM.CLICK < THIS.find(".bm_block").length - 1){
					BM.CLICK++;
				}else{
					BM.CLICK = 0;
				}
				
				DELAY();
			}
		}
		
		// 更改書籤狀態
		function MARK_HOVER(){
			THIS.find(".bm_mark").show().removeClass("current");
			THIS.find(".bm_mark:eq("+ BM.CLICK +")").hide().addClass("current");
		}
		
		// 啟動
		function ACT(){
			// 書籤 HOVER 設定
			THIS.find(".bm_mark").mouseenter(function(){
				$(this).hide();
			});
			THIS.find(".bm_hover").mouseleave(function(){
				$(this).prev(".bm_mark").not(".current").show();
			});
			
			// 書籤點擊動作
			THIS.find(".bm_hover").click(function(){
				clearTimeout(BM.TIME);
				BM.CLICK = THIS.find(".bm_hover").index(this);
				MOVE();
			});
			
			// HOVER 停止自動循環
			if(BM.AUTO && BM.HOVER){
				THIS.mouseover(function(){
					clearTimeout(BM.TIME);
				});
				
				THIS.mouseleave(function(){
					DELAY();
				});
			}
			
			// 自動循環啟動
			if(BM.AUTO){
				DELAY();
			}
		}
		
		// 初始化
		function INIT(){
			var THIS_W = THIS.width();
			var THIS_H = THIS.height();
			var MARK_W = THIS.find(".bm_mark").width();
			var MARK_NUM = THIS.find(".bm_block").length;
			var MARK_GAP = MARK_W - -BM.GAP;
			BM.MOVE_W = THIS_W - ((MARK_W * MARK_NUM) - -(MARK_NUM * BM.GAP));
			
			THIS.find(".bm_mark:eq(0)").hide().addClass("current");
			
			THIS.css({
				"position":"relative",
				"overflow":"hidden",
			});
			
			THIS.find(".bm_block").css({
				"position":"absolute",
				"width":BM.MOVE_W - -MARK_GAP +"px",
				"height":THIS_H +"px",
				"top":"0",
				"background":"#FFF",
			});
			
			THIS.find(".bm_mark,.bm_hover").css({
				"position":"absolute",
				"top":"0",
				"left":"0",
				"width":MARK_W +"px",
				"height":"100%",
				"cursor":"pointer",
				"z-index":"1",
			});
			
			THIS.find(".bm_hover").css({
				"cursor":"pointer",
				"z-index":"0",
			});
			
			THIS.find(".bm_content").css({
				"position":"absolute",
				"top":"0",
				"left":MARK_GAP +"px",
				"width":BM.MOVE_W +"px",
				"height":"100%",
			});
			
			THIS.find(".bm_block").each(function(KEY){
				if(KEY != 0){
					if(KEY == 1){
						BM.NOD[1] = MARK_GAP - -BM.MOVE_W - -BM.GAP;
						$(this).css({ "left":BM.NOD[1] +"px" });
					}else{
						BM.NOD[KEY] = BM.NOD[KEY - 1] - -MARK_GAP;
						$(this).css({ "left":BM.NOD[KEY] +"px" });
					}
				}
				
				$(this).css({ "z-index":KEY });
			});
			
			ACT();
		}
		
		INIT();
	};
})(jQuery);