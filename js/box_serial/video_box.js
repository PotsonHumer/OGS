// JavaScript Document

/* 使用方法

$(".class / #id").video_box({
	VIDEO_W : "640", // 影片寬度
	VIDEO_H : "480", // 影片寬度
	CODE : "", // 影片辨識碼
	FROM : 0, // 影片來源，0 => YouTube, 1 => YouKu, 2 => Vimeo
},function(DATA){
	// callback
});

*/

(function($){
	$.fn.video_box = function(OPTION,CALLBACK){
		var VIDEO = jQuery.extend({
			VIDEO_W : "640", // 影片寬度
			VIDEO_H : "480", // 影片寬度
			CODE : "", // YouTube 辨識碼
			FROM : 0, // 影片來源，0 => YouTube, 1 => YouKu, 2 => Vimeo
			//AFTER :  function() {  }, // 動作後執行擴充
			
			//----
			PREIVEW : new Array(),
			LENGTH: this.length,
		}, OPTION);
		
		var THIS = this;
		
		function log(OUTPUT){
			try{
				console.log(OUTPUT);
			}catch(e){
				alert(OUTPUT);
			}
		}
		
		// 取得預覽圖
		function PREVIEW_IMG(CODE,PREVIEW_GET){
			if(CODE){
				switch(VIDEO.FROM){
					case 1:
						var FEEBACK_STR = "YouKu 不接受跨域請求，需要另外處理|http://v.youku.com/player/getPlayList/VideoIDS/"+ CODE;
						var PREVIEW_PATH = false;
						log(FEEBACK_STR);
						
						PREVIEW_GET(PREVIEW_PATH);
					break;
					case 2:
						$(document).load("http://vimeo.com/api/v2/video/"+ CODE +".json",function(FEEBACK){
							if(typeof(FEEBACK) != "undefined" && FEEBACK != ''){
								var V_OBJ = JSON.parse(FEEBACK);
								var PREVIEW_PATH = V_OBJ[0].thumbnail_large;
							}else{
								var PREVIEW_PATH = false;
							}
							
							PREVIEW_GET(PREVIEW_PATH);
						});
					break;
					default:
						var FEEBACK_STR = "http://img.youtube.com/vi/"+ CODE +"/default.jpg";
						var PREVIEW_PATH = FEEBACK_STR;
						
						PREVIEW_GET(PREVIEW_PATH);
					break;
				}
			}else{
				log('錯誤；消失的辨識碼!!!');
			}
		}
		
		// 取得影片辨識碼
		function CODE_REQUEST(OBJ){
			if(VIDEO.CODE != ""){
				var CODE = VIDEO.CODE;
			}else{
				var CODE = OBJ.attr("rel");
			}
			
			return CODE;
		}
		
		// 啟動影片
		THIS.click(function(E){
			E.preventDefault();
			
			$("body").append('<div id="video_box"></div>');
			$("#video_box").html("");
			
			var CODE = CODE_REQUEST($(this));
			
			if(typeof(CODE) != "undefined" && CODE != ""){
				switch(VIDEO.FROM){
					case 1:
						var VIDEO_FRAME = '<embed id="video_iframe" src="http://player.youku.com/player.php/sid/'+ CODE +'/v.swf" allowFullScreen="true" quality="high" width="'+ VIDEO.VIDEO_W +'" height="'+ VIDEO.VIDEO_H +'" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>';
					break;
					case 2:
						var VIDEO_FRAME = '<iframe id="video_iframe" src="//player.vimeo.com/video/'+ CODE +'" width="'+ VIDEO.VIDEO_W +'" height="'+ VIDEO.VIDEO_H +'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
					break;
					default:
						var VIDEO_FRAME = '<iframe id="video_iframe" src="http://www.youtube.com/embed/'+ CODE +'?rel=0" width="'+ VIDEO.VIDEO_W +'" height="'+ VIDEO.VIDEO_H +'" frameborder="0" allowfullscreen></iframe>';
					break;
				}
				
				$("body").append(
					VIDEO_FRAME+ 
					'<div id="video_close">Close</div>'
				);
				
				$("#video_box").css({
					"width":"100%",
					"height":"100%",
					"display":"none",
					"position":"fixed",
					"z-index":"998",
					"background":"#000",
					"top":"0",
					"left":"0",
					"opacity":"0.6",
					"filter":"alpha(opacity=60) /9",
				});
				
				$("#video_iframe").css({
					"position":"fixed",
					"z-index":"999",
					"top":"50%",
					"left":"50%",
					"margin":"-"+ VIDEO.VIDEO_H / 2 +"px 0 0 -"+ VIDEO.VIDEO_W / 2 +"px"
				});
				
				$("#video_close").css({
					"display":"none",
					"position":"fixed",
					"width":"100px",
					"height":"25px",
					"z-index":"999",
					"top":"50%",
					"left":"50%",
					"background":"#000",
					"color":"#DDD",
					"margin":VIDEO.VIDEO_H / 2 +"px 0 0 "+ (VIDEO.VIDEO_W / 2 - 100) +"px",
					"text-align":"center",
					"letter-spacing":"0.1em",
					"line-height":"25px",
					"cursor":"pointer",
				});
				
				$("#video_close").hover(function(){
					$(this).css({
						"background":"#FFF",
						"color":"#900",
					});
				}, function(){
					$(this).css({
						"background":"#000",
						"color":"#DDD",
					});
				});
				
				$("#video_box,#video_close").fadeIn("slow");
			}
		});
		
		$(document).on("click","#video_box,#video_close",function(){
			$("#video_iframe").hide();
			$("#video_box,#video_close").fadeOut("slow",function(){
				$("#video_box,#video_iframe,#video_close").remove();
			});
		});
		
		// 取得預覽圖、並回傳完成至 CALLBACK
		THIS.each(function(KEY){
			var OBJ = $(this);
			var CODE = CODE_REQUEST(OBJ);
			
			if(typeof(CODE) != "undefined" && CODE != ""){
				OBJ.addClass('is_preview');
				PREVIEW_IMG(CODE,function(PATH){
					OBJ.attr('alt',PATH); // 輸出至 html
					
					if(VIDEO.LENGTH == (Number(KEY) + 1)){
						CALLBACK(true);
					}
				});
			}
		});
	};
})(jQuery);
