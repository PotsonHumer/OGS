// JavaScript Document

/* 使用方法

$("#id").top_box({
	ACT_TIMER : 1000, //動作花費時間
	AFTER :  function() {  }, // 動作後執行擴充
});

*/

(function($){
	$.fn.top_box = function(OPTION){
		var TOP = jQuery.extend({
			ACT_TIMER : 1000, //動作花費時間
			AFTER :  function() {  }, // 動作後執行擴充
			
			//----
			AFTER_TIMER : 0,
		}, OPTION);
		
		var $BODY = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
		var THIS = this;
		
		return this.each(function(){
			THIS.click(function(E){
				E.preventDefault();
				$BODY.animate({ scrollTop: 0 }, TOP.ACT_TIMER,function(){
					TOP.AFTER_TIMER++;
					
					if(TOP.AFTER_TIMER == 1){
						TOP.AFTER();
					}
				});
				
				TOP.AFTER_TIMER = 0;
			});
		});
	};
})(jQuery);
