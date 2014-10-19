// JavaScript Document

/* 使用方法

	$(".class or #id").mag_box({
		W : 200, //放大鏡寬
		H : 200, //放大鏡高
	});

	**********************************************************************
	INFO :
	利用 css 設定 #mag_box 可以修改放大鏡框的樣式。
	
*/

(function($){
	$.fn.mag_box = function(OPTION){
		var MAG = jQuery.extend({
			W : 200, //放大鏡寬
			H : 200, //放大鏡高
			
			//----
			SRC : "",
		}, OPTION);
		
		function log(OUTPUT){
			try{
				console.log(OUTPUT);
			}catch(e){}
		}
		
		// 精準減法
		function accSubtr(arg1,arg2){
			var r1,r2,m,n; 
			try{ r1=arg1.toString().split(".")[1].length }catch(e){ r1=0 } 
			try{ r2=arg2.toString().split(".")[1].length }catch(e){ r2=0 } 
			m=Math.pow(10,Math.max(r1,r2)); 
			//動態控制精度長度 
			n=(r1>=r2)?r1:r2; 
			return ((arg1*m-arg2*m)/m).toFixed(n); 
		}
		
		// 精準除法
		function accDiv(arg1,arg2){
			var t1=0,t2=0,r1,r2;
			try{ t1=arg1.toString().split(".")[1].length }catch(e){}
			try{ t2=arg2.toString().split(".")[1].length }catch(e){}
			with(Math){
				r1=Number(arg1.toString().replace(".",""))
				r2=Number(arg2.toString().replace(".",""))
				return (r1/r2)*pow(10,t2-t1);
			}
		}
		
		// 精準乘法
		function accMul(arg1,arg2){
			var m=0,s1=arg1.toString(),s2=arg2.toString();
			try{ m+=s1.split(".")[1].length }catch(e){}
			try{ m+=s2.split(".")[1].length }catch(e){}
			return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
		}
		
		//重新讀取圖片
		function LOAD(OBJ,THIS_X,THIS_Y,MOVE_PRE_X,MOVE_PRE_Y,IMG,CALLBACK){
			$(IMG).load(function(){
				CALLBACK(OBJ,THIS_X,THIS_Y,MOVE_PRE_X,MOVE_PRE_Y,IMG.width,IMG.height);
			});
		}
		
		//啟動
		function ACT(OBJ,E_X,E_Y,MAG_X,MAG_Y,MAG_W,MAG_H){
			if(typeof(MAG_W) != "undefined" && typeof(MAG_H) != "undefined" && MAG_W != 0 && MAG_H != 0){

				$("#mag_box").css({
					"width" : MAG.W +"px",
					"height" : MAG.H +"px",
					"position" : "absolute",
					"top" : E_Y - MAG.H - 10 + "px",
					"left" : E_X - -10 + "px",
					"display" : "block",
					"overflow" : "hidden",
				});
				
				$("#mag_box img").css({
					"position" : "absolute",
					"top" : - Math.round(accMul(MAG_Y,accDiv(MAG_H,100))) + accDiv(MAG.H,2) +"px",
					"left" : - Math.round(accMul(MAG_X,accDiv(MAG_W,100))) + accDiv(MAG.W,2) +"px",
				});
				
				var MAG_SRC = $("#mag_box img").attr("src");
				
				if(MAG.SRC != MAG_SRC){
					$("#mag_box img").remove();
					$("#mag_box").append("<img>");
					$("#mag_box img").attr("src",MAG.SRC);
				}
			}else{
				log('mag_box Error!! Please make sure element tag is <img> and good url link.');
			}
		}
		
		//初始化
		function INIT(THIS,EVENT){
			var OBJ = $(THIS);
			
			// 滑鼠定位
			var THIS_LEFT = Math.floor(OBJ.offset().left);
			var THIS_X = EVENT.pageX;
			var MOVE_X = accSubtr(THIS_X,THIS_LEFT);
			
			var THIS_TOP = Math.floor(OBJ.offset().top);
			var THIS_Y = EVENT.pageY;
			var MOVE_Y = accSubtr(THIS_Y,THIS_TOP);
			
			var CURRENT_W = OBJ.css("width").replace("px", "");
			var CURRENT_H = OBJ.css("height").replace("px", "");
			
			var MOVE_PRE_X = Math.round(accDiv(MOVE_X,accDiv(CURRENT_W,100)));
			var MOVE_PRE_Y = Math.round(accDiv(MOVE_Y,accDiv(CURRENT_H,100)));
			
			// 圖片讀取
			var IMG_SRC = $(THIS).attr("src");
			var IMG = new Image();
			MAG.SRC = IMG.src = IMG_SRC;
			
			if(IMG.complete == false){
				LOAD(OBJ,THIS_X,THIS_Y,MOVE_PRE_X,MOVE_PRE_Y,IMG,ACT);
			}else{
				ACT(OBJ,THIS_X,THIS_Y,MOVE_PRE_X,MOVE_PRE_Y,IMG.width,IMG.height);
			}
		}
		
		// 載入元素
		$("body").append('<div id="mag_box" style="display: none;"></div>');
		
		this.on('mousemove',this,function(E){
			INIT(this,E);
		});
		
		this.on('mouseout',this,function(E){
			$("#mag_box").hide();
		});
	};
})(jQuery);