// JavaScript Document

/* 使用方法

	$(document).scroll_box({
		ACT_TIMER : 1000, //動作花費時間
	}, function(DATA){
		//callback
	});
	
	
	**********************************************************************
	INFO :
	連結設為 #document 即可實現卷軸置頂功能
*/

(function($){
	$.fn.scroll_box = function(OPTION,CALLBACK){
		var SCROLL = jQuery.extend({
			ACT_TIMER : 1000, //動作花費時間
			
			//----
			//AFTER_TIMER : 0,
		}, OPTION);
				
		var $BODY = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
		
		$(document).on("click","a",function(E){
			try{
				var LOCATION = $(this).attr("href");
				var LOCATION_TOP = (LOCATION == "#document")?0:$("*[name="+ LOCATION.replace("#","") +"]").offset().top;
			}
			catch(e){ return true; }
			
			E.preventDefault();
			$BODY.animate({ scrollTop: LOCATION_TOP }, SCROLL.ACT_TIMER,function(){
				CALLBACK(LOCATION);
			});
		});
	};
})(jQuery);
