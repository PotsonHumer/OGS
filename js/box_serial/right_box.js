// JavaScript Document

/* 使用方法

	$(document or "#id or .class").right_box({
		ACTIVE : true, //啟動此功能 , true => 啟動 , false => 不啟動
		MENU : "", //自訂選單 ID
		FOLLOW : true, //選單跟隨滑鼠
	},function(CLICK,OBJ){
		// callback
	});
	
	
	**********************************************************************
	INFO :
	不填寫 MENU 參數，可以當鎖右鍵功能使用
	配合 callback 可以做為判斷左右鍵或其他特效功能
*/


(function($){
	$.fn.right_box = function(OPTION,CALLBACK){
		var RIGHT = jQuery.extend({
			ACT : true, //啟動此功能 , true => 啟動 , false => 不啟動
			MENU : "", //自訂選單 ID
			FOLLOW : true, //選單跟隨滑鼠
			
			//----
			CLICK : false,
			ENTER : false,
		}, OPTION);
		
		THIS = this;
		
		function log(INPUT){
			console.log(INPUT);
		}
		
		//取消預設事件
		function unbind_act(E){
			//IE
			if(window.event){
				E = window.event;
				E.returnValue = false; //取消IE預設事件
			}else{
		    	E.preventDefault(); //取消DOM預設事件
			}
		}
		
		//解除右鍵事件
		function unbind_event(E){
			if(typeof(THIS.prop("tagName")) != 'undefined'){
				if(RIGHT.ENTER){
					unbind_act(E);
					ACT(E);
				}else{
					RIGHT.CLICK = true;
					ACT();
				}
			}else{
				unbind_act(E);
				ACT(E);
			}
		}
		
		// 啟動
		function ACT(E){
			var MENU_OBJ = $("#"+ RIGHT.MENU);
			CALLBACK(RIGHT.CLICK,MENU_OBJ);
			
			switch(RIGHT.CLICK){
				case true:
					(RIGHT.MENU)?$("#"+ RIGHT.MENU).fadeOut(100):"";
					RIGHT.CLICK = false;
				break;
				case false:
					(RIGHT.MENU)?$("#"+ RIGHT.MENU).fadeIn(100):"";
					//RIGHT.CLICK = true;
					
			    	if(RIGHT.FOLLOW){
			    		MENU_FOLLOW(E);
			    	}
				break;
			}
		}
		
		// 跟隨滑鼠
		function MENU_FOLLOW(E){
			var WIN_H = $(window).height();
			var WIN_TOP = parseInt(document.body.scrollTop, 10) ||parseInt(document.documentElement.scrollTop, 10);
			var WIN_HELF_H = WIN_TOP - -(WIN_H / 2);
			
			var BODY_LEFT = E.pageX;
			var BODY_TOP = E.pageY;
			
			if(typeof(BODY_LEFT) == "undefined" && typeof(BODY_TOP) == "undefined"){
				var BODY_LEFT = E.clientX + document.documentElement.scrollLeft; 			
				var BODY_TOP = E.clientY + document.documentElement.scrollTop; 	
			}
			
			var BODY_W = $("body").outerWidth();
			var BODY_H = $("body").outerHeight();
			
			var MENU_W = $("#"+ RIGHT.MENU).outerWidth();
			var MENU_H = $("#"+ RIGHT.MENU).outerHeight();
			
			if(BODY_LEFT - -MENU_W > BODY_W){
				var LEFT_SET = BODY_LEFT - MENU_W;
			}else{
				var LEFT_SET = BODY_LEFT;
			}
			
			if(BODY_TOP > WIN_HELF_H){
				var TOP_SET = BODY_TOP - MENU_H;
			}else{
				var TOP_SET = BODY_TOP;
			}
			
			$("#"+ RIGHT.MENU).css({ "position":"absolute","left":LEFT_SET +"px","top":TOP_SET +"px","z-index":"99" });
		}
		
		// 左鍵關閉
		$(document).click(function(){
			RIGHT.CLICK = true;
			ACT();
		});
		
		THIS.mouseenter(function(){
			RIGHT.ENTER = true;
		});
		
		THIS.mouseleave(function(){
			RIGHT.ENTER = false;
		});
		
		if(RIGHT.ACTIVE){
			document.oncontextmenu = unbind_event;
		}		
	};
})(jQuery);
