// JavaScript Document

/* 使用方法

$("#id").tab_box({
	TITLE : true, //自動生成頁籤 , true => 啟動 , false => 停用
	AFTER :  function() {  }, // 動作後執行擴充			
});

頁籤 css : class => tab_title
內容 css : class => tab
*/

(function($){
	$.fn.tab_box = function(OPTION){
		var TAB = jQuery.extend({
			TITLE : true, //自動生成頁籤 , true => 啟動 , false => 停用
			AFTER :  function() {  }, // 動作後執行擴充			
		}, OPTION);
		
		var THIS = this;
		
		return this.each(function(){
			THIS.find(".tab").hide();
			THIS.find(".tab:eq(0)").show();
			
			if(TAB.TITLE == true){
				THIS.find(".tab").each(function(KEY){
					var TITLE = $(this).attr("rel");
					if(typeof(TITLE) == "undefined" || typeof(TITLE) != "undefined" && TITLE == ""){
						var TITLE_NUM = KEY - -1;
						TITLE = "TITLE "+ TITLE_NUM;
					}
					
					THIS.find(".tab:eq(0)").before('<div class="tab_title">'+ TITLE +'</div>');
				});
			}
			
			THIS.find(".tab:eq(0)").before('<div style="clear:both"></div>');
			
			THIS.find(".tab_title").click("click",function(){
				var TAB_INDEX = THIS.find(".tab_title").index(this);
				THIS.find(".tab_title").removeClass("current");
				$(this).addClass("current");
				THIS.find(".tab").hide();
				THIS.find(".tab:eq("+ TAB_INDEX +")").show();
				
				TAB.AFTER();
			});
			
			THIS.find(".tab_title:eq(0)").addClass("current");
			THIS.find(".tab_title").css({
				"cursor":"pointer",
				"float":"left",
			});
		});
	};
})(jQuery);
