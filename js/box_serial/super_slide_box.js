// JavaScript Document

/* 使用方法

	$("#id").super_slide_box({
		SHOW_NUM : 3, //一次顯示數量
		TYPE : 0, // 0 => 移動 (slide) , 1 => 漸層 (fade) , 2 => 旋轉(Circle) , 3 => 環繞(Orbit)
		OUTER_WIDTH : 20, //額外間距
		ACT_TIMER : 1000, //動作間隔時間
		POSITION : 0, // 起始位置
		AUTO : true, // true => 自動動作 , false => 手動動作
		WIDTH : 200, // 圖片大小
		HEIGHT : 200, // 圖片高度
		HOVER : false, // 滑鼠hover停止動作 , true => 停止 , false => 不停止
		CYCLE : true, // 循環 / 回放切換 , true => 循環 , false => 回放
		VERTICAL : false, //移動方向 , true => 垂直 , false => 水平
	},function(KEY){
		// key_callback
	});
	
	
	**********************************************************************
	

*/


(function($){
	$.fn.super_slide_box = function(OPTION,KEY_CALLBACK){
		var SSLIDE = jQuery.extend({
			SHOW_NUM : 3, //一次顯示數量
			TYPE : 0, // 0 => 移動 (slide) , 1 => 漸層 (fade) , 2 => 旋轉(Circle) , 3 => 環繞(Orbit)
			OUTER_WIDTH : 20, //額外間距
			ACT_TIMER : 3000, //動作間隔時間
			POSITION : 0, //起始位置
			AUTO : true, // true => 自動動作 , false => 手動動作
			WIDTH : 200, //圖片寬度
			HEIGHT : 200, // 圖片高度
			HOVER : false, // 滑鼠hover停止動作 , true => 停止 , false => 不停止
			CYCLE : false, // 循環 / 回放切換 , true => 循環 , false => 回放
			VERTICAL : false, //移動方向 , true => 垂直 , false => 水平
			
			//----
			NUM : 0,
			TIMER : 0,
			KEY: 0,
			CYCLE_ACT: false,
			BLOCK_W: 0,
			
			//---- circle only
			LEVEL_W: new Array(),
			LEVEL_H: new Array(),
			LEVEL_Z: new Array(),
			LEVEL_S: new Array(),
			LEVEL_X: new Array(),
			LEVEL_Y: new Array(),
			LEVEL_P_X: new Array(),
			LEVEL_P_Y: new Array(),
			LEVEL_B: new Array(),
			
			//---- orbit only
			NAV: 0,
			DEF_NAV: new Array(),
			DIR_SET : false,
		}, OPTION);
		
		var THIS = this;
		SSLIDE.NUM = THIS.find(".slide_pic").length;
		
		function log(VALUE){
			try{
				console.log(VALUE);
			}
			catch(e){
				alert(VALUE);
			}
		}
		
		return this.each(function(){
			// 起始位置設定
			if(SSLIDE.TYPE != 3){
				if(SSLIDE.POSITION > 0 && SSLIDE.POSITION <= SSLIDE.NUM){
					$(this).find(".slide_pic").eq(SSLIDE.POSITION - 1).addClass("current");
					SSLIDE.KEY = SSLIDE.POSITION - 1;
				}else{
					$(this).find(".slide_pic").eq(0).addClass("current");
					SSLIDE.KEY = 0;
				}
			}
			
			// 初始排序
			switch(SSLIDE.TYPE){
				// Slide
				default:
					SSLIDE.BLOCK_W = SSLIDE.WIDTH - -SSLIDE.OUTER_WIDTH;
					
					//檢查顯示單項是否足夠數量 , 足夠則循環複製
					if(SSLIDE.NUM > SSLIDE.SHOW_NUM && SSLIDE.CYCLE){
						var COPY_PIC = THIS.find(".slide_move").children().clone(true); //mod by Xin 2013-10-11
						THIS.find(".slide_move").append(COPY_PIC);
						
						SSLIDE.CYCLE_ACT = true; //循環確認子
					}
					
					$(this).find(".slide_pic").each(function(I){
						//垂直
						if(SSLIDE.VERTICAL){
							$(this).css({ "top":SSLIDE.BLOCK_W * I +"px" });
						}else{
							$(this).css({ "left":SSLIDE.BLOCK_W * I +"px" });
						}
					});
					
					THIS.find(".slide_move").css({ "left":"-"+ SSLIDE.BLOCK_W * SSLIDE.KEY +"px" });
				break;
				
				// Fade
				case 1:
					$(this).find(".slide_pic").each(function(I){
						if(SSLIDE.KEY != I){
							$(this).hide();
						}
					});
				break;
				
				// Circle
				case 2:
					var BLOCK_W = THIS.find(".slide_move").outerWidth();
					var BLOCK_H = THIS.find(".slide_move").outerHeight();
					var ACT_W = new Array();
					
					// 雙數
					if(SSLIDE.NUM % 2 == 0){
						//高度階層
						var BLOCK_H_LEVEL = (SSLIDE.NUM - 2) / 2 + 1;
						var H_ROW_FIX = 0;
						
						//寬度階層
						if((SSLIDE.NUM / 2) % 2 == 0){
							var BLOCK_W_LEVEL = (SSLIDE.NUM - 2) / 2 + 2;
							var W_ROW_FIX = 2;
						}else{
							var BLOCK_W_LEVEL = SSLIDE.NUM / 2;
							var W_ROW_FIX = 1;
						}
					// 單數	
					}else{
						//高度階層
						var BLOCK_H_LEVEL = (SSLIDE.NUM - 1) / 2;
						var H_ROW_FIX = 1;
						
						//寬度階層
						if(((SSLIDE.NUM - 1) / 2) % 2 == 0){
							var BLOCK_W_LEVEL = (SSLIDE.NUM - 1) / 2 + 1;
							var W_ROW_FIX = 3;
						}else{
							var BLOCK_W_LEVEL = (SSLIDE.NUM - 3) / 2 + 3;
							var W_ROW_FIX = 4;
						}
					}
					
					// 寬度階層計算
					var LEVEL_ALL_W = BLOCK_W - SSLIDE.WIDTH; // 減去單一寬度後的所有寬
					var LEVEL_W_MARGIN = Math.round(LEVEL_ALL_W / (BLOCK_W_LEVEL - 1)); // 取得單一寬度間距
					SSLIDE.LEVEL_W[0] = ((Math.ceil(BLOCK_W_LEVEL / 2) - 1) * LEVEL_W_MARGIN); // 設定第一個錨點寬度
					
					// 累算各錨點寬度
					var ACT_ROW = Math.floor(BLOCK_W_LEVEL / 2);
					
					/*
						1 => ++
						2 => ~~
						3 => --
						4 => ++++
						5 => ----
					*/
					
					switch(W_ROW_FIX){
						case 1:
							ACT_W[ACT_ROW + 1] = 2;
							ACT_W[ACT_ROW + 2] = 3;
							ACT_W[ACT_ROW + BLOCK_W_LEVEL + 1] = 2;
							ACT_W[ACT_ROW + BLOCK_W_LEVEL + 2] = 1;
						break;
						case 3:
							ACT_W[ACT_ROW + 1] = 2;
							ACT_W[ACT_ROW + 2] = 3;
							ACT_W[ACT_ROW + ACT_ROW + 1] = 5;
							ACT_W[ACT_ROW + ACT_ROW + 2] = 3;
							ACT_W[ACT_ROW + BLOCK_W_LEVEL] = 2;
							ACT_W[ACT_ROW + BLOCK_W_LEVEL + 1] = 1;
						break;
						case 2:
							ACT_W[ACT_ROW + 1] = 3;
							ACT_W[ACT_ROW + BLOCK_W_LEVEL] = 1;
						break;
						case 4:
							ACT_W[ACT_ROW + 1] = 3;
							ACT_W[ACT_ROW + ACT_ROW] = 5;
							ACT_W[ACT_ROW + ACT_ROW + 1] = 3;
							ACT_W[ACT_ROW + BLOCK_W_LEVEL - 1] = 1;
						break;
					}
					
					var W_KEY = 0;
					var W_POT = 1;
					for(var W=1;W<SSLIDE.NUM;W++){
						
						if(ACT_W[W] > 0){
							W_POT = ACT_W[W];
						}
						
						switch(W_POT){
							case 1:
								W_KEY++;
							break;
							case 2:
								//W_KEY = W_KEY;
							break;
							case 3:
								W_KEY--;
							break;
							case 4:
								W_KEY = W_KEY + 2;
							break;
							case 5:
								W_KEY = W_KEY - 2;
							break;
						}
						
						SSLIDE.LEVEL_W[W] = SSLIDE.LEVEL_W[0] + (W_KEY * LEVEL_W_MARGIN);
					}
					
					//----------------------
					
					// 高度設定
					var LEVEL_ALL_H = BLOCK_H - SSLIDE.HEIGHT; // 減去單一高度後的所有高
					var LEVEL_H_MARGIN = Math.round(LEVEL_ALL_H / BLOCK_H_LEVEL); // 取得單一高度間距
					SSLIDE.LEVEL_H[0] = LEVEL_ALL_H; // 設定第一個錨點高度
					
					// 累算各錨點高度
					for(var H=1;H<=BLOCK_H_LEVEL * 2;H++){
						if(H <= BLOCK_H_LEVEL){
							SSLIDE.LEVEL_H[H] = LEVEL_ALL_H - LEVEL_H_MARGIN * H;
						}else{
							if(H == (BLOCK_H_LEVEL - -1) && SSLIDE.NUM % 2 != 0){
								SSLIDE.LEVEL_H[H] = LEVEL_ALL_H - LEVEL_H_MARGIN * BLOCK_H_LEVEL;
							}else{
								SSLIDE.LEVEL_H[H] = LEVEL_ALL_H - (LEVEL_H_MARGIN * BLOCK_H_LEVEL - LEVEL_H_MARGIN * ((H - H_ROW_FIX) - BLOCK_H_LEVEL));
							}
						}
					}
					
					//----------------------
					
					// Z軸設定
					if(SSLIDE.NUM % 2 == 0){
						var Z_NUM = SSLIDE.NUM / 2;
					}else{
						var Z_NUM = (SSLIDE.NUM - 1) / 2;
					}
					
					SSLIDE.LEVEL_Z[0] = SSLIDE.NUM + 1;
					
					for(var Z=1;Z<=Z_NUM * 2;Z++){
						if(Z <= Z_NUM){
							SSLIDE.LEVEL_Z[Z] = SSLIDE.NUM - Z;
							var LAST_LEVEL_Z = SSLIDE.LEVEL_Z[Z];
						}else{
							LAST_LEVEL_Z++;
							SSLIDE.LEVEL_Z[Z] = LAST_LEVEL_Z;
						} 
					}
					
					//----------------------
					
					// 遞縮圖設定 , 遞模糊設定
					if(SSLIDE.NUM % 2 == 0){
						var S_NUM = SSLIDE.NUM / 2 - 1;
						var S_JUMP = 0;
					}else{
						var S_NUM = (SSLIDE.NUM - 1) / 2 - 1;
						var S_JUMP = 1;
					}
					
					var S_MARGIN = Math.floor(50 / (S_NUM + 1)); // 縮圖百分比間距
					
					SSLIDE.LEVEL_S[0] = 100;
					SSLIDE.LEVEL_B[0] = 0;
					
					// 縮圖 % 設定					
					for(var S=1;S<SSLIDE.NUM;S++){
						if(S_JUMP == 1){
							if(S <= S_NUM){
								SSLIDE.LEVEL_S[S] = SSLIDE.LEVEL_S[S - 1] - S_MARGIN;
							}
							
							if(S == S_NUM + 1){
								SSLIDE.LEVEL_S[S] = SSLIDE.LEVEL_S[S - 1] - S_MARGIN;
							}
							
							if(S == S_NUM + 2){
								SSLIDE.LEVEL_S[S] = SSLIDE.LEVEL_S[S - 1];
							}
							
							if(S == S_NUM + 1 || S == S_NUM + 2){
								SSLIDE.LEVEL_B[S] = 5;
							}else{
								SSLIDE.LEVEL_B[S] = 0;
							}
							
							if(S > S_NUM + 2){
								SSLIDE.LEVEL_S[S] = SSLIDE.LEVEL_S[S - 1] + S_MARGIN;
							}
						}
						
						if(S_JUMP == 0){
							if(S <= S_NUM + 1){
								SSLIDE.LEVEL_S[S] = SSLIDE.LEVEL_S[S - 1] - S_MARGIN;
							}
							
							if(S > S_NUM + 1){
								SSLIDE.LEVEL_S[S] = SSLIDE.LEVEL_S[S - 1] + S_MARGIN;
							}
							
							if(S == S_NUM || S == S_NUM + 1 || S == S_NUM + 2){
								SSLIDE.LEVEL_B[S] = 5;
							}else{
								SSLIDE.LEVEL_B[S] = 0;
							}
						}
					}
					
					$.each(SSLIDE.LEVEL_S,function(KEY,VAL){
						if(KEY > 0){
							var RATIO = SSLIDE.WIDTH / SSLIDE.HEIGHT;
							
							SSLIDE.LEVEL_X[KEY] = Math.round(SSLIDE.WIDTH * (VAL / 100));
							SSLIDE.LEVEL_P_X[KEY] = Math.round((SSLIDE.WIDTH - SSLIDE.LEVEL_X[KEY]) / 2);
							
							SSLIDE.LEVEL_Y[KEY] = Math.round(SSLIDE.LEVEL_X[KEY] / RATIO);
							SSLIDE.LEVEL_P_Y[KEY] = Math.round((SSLIDE.HEIGHT - SSLIDE.LEVEL_Y[KEY]) / 2);
						}else{
							SSLIDE.LEVEL_X[KEY] = SSLIDE.WIDTH;
							SSLIDE.LEVEL_Y[KEY] = SSLIDE.HEIGHT;
							SSLIDE.LEVEL_P_X[KEY] = 0;
							SSLIDE.LEVEL_P_Y[KEY] = 0;
						}
					});
					
					//----------------------
					
					// 設置位置
					$(this).find(".slide_pic").each(function(KEY){
						$(this).css({
							"left":SSLIDE.LEVEL_W[KEY] +"px",
							"top":SSLIDE.LEVEL_H[KEY] +"px",
							"z-index":SSLIDE.LEVEL_Z[KEY],
							"width":SSLIDE.LEVEL_X[KEY],
							"height":SSLIDE.LEVEL_Y[KEY],
							"padding":SSLIDE.LEVEL_P_X[KEY],
						});
						
						$(this).find("img").css({
							"width":SSLIDE.LEVEL_X[KEY],
							"height":SSLIDE.LEVEL_Y[KEY],
						});
						
						$(this).find("img").css({
							"-webkit-filter":"blur("+ SSLIDE.LEVEL_B[KEY] +"px)",
							"-moz-filter":"blur("+ SSLIDE.LEVEL_B[KEY] +"px)",
							"-o-filter":"blur("+ SSLIDE.LEVEL_B[KEY] +"px)",
							"-ms-filter":"blur("+ SSLIDE.LEVEL_B[KEY] +"px)",
							"filter":"blur("+ SSLIDE.LEVEL_B[KEY] +"px)",
						});
					});
					
				break;
				
				// Orbit
				case 3:
					var BLOCK_W_HF = Math.round(THIS.find(".slide_move").outerWidth() / 2);
					var BLOCK_H_HF = Math.round(THIS.find(".slide_move").outerHeight() / 2);
					
					// 取得路徑點
					SSLIDE.NAV = make_points(BLOCK_W_HF,BLOCK_H_HF,0.5);
					
					var NAV_NUM = SSLIDE.NAV.length;
					var NAV_START = Math.round(NAV_NUM / 4);
					var NAV_AVG = Math.round(NAV_NUM / SSLIDE.NUM);
					
					$(this).find(".slide_pic").each(function(KEY){
						if(KEY == 0){
							var NAV_SET = NAV_START;
							var NAV = SSLIDE.NAV[NAV_START];
						}else{
							var NAV_SET = NAV_START + NAV_AVG * KEY;
							
							if(NAV_SET > NAV_NUM){
								NAV_SET = (NAV_START + NAV_AVG * KEY) - NAV_NUM;
							}
							
							var NAV = SSLIDE.NAV[NAV_SET - 1];
						}
									
						SSLIDE.DEF_NAV[KEY] = NAV_SET;
						
						// 置中調整
						var MARGIN_L = - Math.round(SSLIDE.WIDTH / 2);
						var MARGIN_T = - Math.round(SSLIDE.HEIGHT / 2);
						
						// Z-INDEX
						var Z_INDEX = Math.round(NAV[1]);
						
						
						// 顯示大小設定
						var SIZE = ORBIT_SIZE(NAV[1]);
						
						$(this).css({ 
							"left":NAV[0],
							"top":NAV[1],
							"margin":MARGIN_T +"px 0 0 "+ MARGIN_L +"px",
							"z-index":Z_INDEX,
							"width":SIZE[0] +"px" ,
							"height":SIZE[1] +"px" ,
							"padding":SIZE[2] +"px "+ SIZE[3] +"px",
						});
						
						$(this).find("img").css({
							"width":SIZE[0] +"px",
							"height":SIZE[1] +"px",
						});
					});
				break;
			}
			
			// 滑鼠暫停功能
			if(SSLIDE.AUTO && (SSLIDE.HOVER && SSLIDE.NUM > SSLIDE.SHOW_NUM && SSLIDE.TYPE == 0 || SSLIDE.TYPE == 1 && SSLIDE.HOVER || SSLIDE.TYPE == 2 && SSLIDE.HOVER || SSLIDE.TYPE == 3 && SSLIDE.HOVER)){
				THIS.hover(function(){
					clearTimeout(SSLIDE.TIMER);
				},function(){
					DELAY();
				});
			}
			
			// 左右鍵功能
			THIS.find(".arrow").click(function(E){
				E.preventDefault();
				clearTimeout(SSLIDE.TIMER);
				
				var ARROW_INDEX = THIS.find(".arrow").index(this);
				
				switch(ARROW_INDEX){
					default:
						var ARROW_SET = 1;
					break;
					case 1:
						var ARROW_SET = -1;
					break;
				}
				
				if(SSLIDE.TYPE != 3){
					var ARROW = SSLIDE.KEY - ARROW_SET;
					CURRENT_MOVE(ARROW,0,1);
				}else{
					ARROW_SET = (ARROW_SET > 0)?true:false;
					CURRENT_MOVE(ARROW_SET,0,3);
				}
			})
			
			// 頁次鍵功能
			THIS.find(".key").click(function(E){
				E.preventDefault();
				
				if(SSLIDE.TYPE == 3){
					return true;
				}
				
				clearTimeout(SSLIDE.TIMER);
				
				var REL_KEY = $(this).attr("rel");				
				
				if(typeof(REL_KEY) != "undefined"){
					var KEY_INDEX = REL_KEY - 1;
				}else{
					var KEY_INDEX = THIS.find(".key").index(this);
				}
				
				CURRENT_MOVE(0,KEY_INDEX,2);
			});
			
			if(SSLIDE.AUTO){
				DELAY();
			}
		});
		
		// 延遲計算
		function DELAY(){
			SSLIDE.TIMER = setTimeout(CURRENT_MOVE,SSLIDE.ACT_TIMER);
		}
		
		// "目前" 標籤移動
		function CURRENT_MOVE(ARROW,KEY,SWITCH){
			SSLIDE.KEY = SSLIDE.KEY - -1;
			
			switch(SWITCH){
				case 1:
					SSLIDE.KEY = ARROW;
				break;
				case 2:
					SSLIDE.KEY = KEY;
				break;
				case 3:
					clearTimeout(SSLIDE.TIMER);
					SSLIDE.DIR_SET = ARROW;
					
					if(!SSLIDE.HOVER){
						DELAY();
					}
					return;
				break;
			}
			
			if(SSLIDE.TYPE != 3){
			
				// 未循環
				if(SSLIDE.CYCLE_ACT == false && SSLIDE.KEY >= SSLIDE.NUM || SSLIDE.KEY < 0){
					SSLIDE.KEY = 0;
				}
				
				// 循環
				if(SSLIDE.CYCLE_ACT == true && SSLIDE.KEY > SSLIDE.NUM || SSLIDE.KEY < 0){
					SSLIDE.KEY = 1;
				}
				
				THIS.find(".slide_pic.current").removeClass("current");
				THIS.find(".slide_pic").eq(SSLIDE.KEY).addClass("current");
				
				// 判斷輸出 KEY
				if(SSLIDE.CYCLE_ACT == true && SSLIDE.KEY == SSLIDE.NUM){
					var OUTPUT_KEY = 0;
				}else{
					var OUTPUT_KEY = SSLIDE.KEY;
				}
				
				// 輸出 KEY
				KEY_CALLBACK(OUTPUT_KEY);
			
			}
			
			// 啟動
			if(SSLIDE.NUM > SSLIDE.SHOW_NUM || SSLIDE.TYPE == 3){
				switch(SSLIDE.TYPE){
					// Slide
					default:
						SLIDE_ACT();
					break;
					// Fade
					case 1:
						FADE_ACT();
					break;
					// Circle
					case 2:
						CIRCLE_ACT();
					break;
					case 3:
						ORBIT_ACT();
					break;
				}
			}
			
			if(SSLIDE.AUTO && (!SWITCH || SWITCH && !SSLIDE.HOVER)){
				DELAY();
			}
		}
		
		// 環繞路徑點
		function make_points(a, b, interval, addX, addY) {
			var x, y;
			var xy = [], xy1 = [], xy2 = [];
			
			//interval = 3;
			
			addX = addX ? addX : a;
			addY = addY ? addY : b;
						
			x = -Math.sqrt((b * b) * a * a / (b * b));
			
			//---- 可變取樣 start
			
			var SAMP_NUM = Math.floor((0 - x) / interval); //總取樣數
			var SAMP_SPLIT = round(SAMP_NUM / 2.5,0); //取樣分區
			
			var SAMP_CHANGE = new Array();
			
			SAMP_CHANGE[0] = 0; // 變態取樣起始
			SAMP_CHANGE[1] = SAMP_SPLIT * 1; // 變態取樣結束
			
			//---- 可變取樣 end 
						
			for (var n = 0; 1; n++) {
				y = Math.sqrt(b * b - (b * b * x * x) / (a * a));
				
				if (n == 0) {
					y0 = y;
				}
	
				xy1[n] = [x + addX, y + addY];
				//n++;
				xy2[n] = [x + addX, -y + addY];
				
				//---- 可變取樣設定 start
				
				if(x >= 0){
					if(x <= SAMP_CHANGE[1]){
						var samp = 2;
					}else{
						var samp = interval;
					}
				}else{
					if(x > -SAMP_CHANGE[1]){
						var samp = 2;
					}else{
						var samp = interval;
					}
				}
				
				//---- 可變取樣設定 end
				
				x = x + samp;
				
				if (x > Math.sqrt((b * b) * a * a / (b * b))) {
					break;
				}				
			}
	
			//xy2倒序
			/*
			 xy2 = xy2.sort(function(x,y){
				return x == y ? 0 : (x > y ? -1 : 1);
			 });
			 */
			
			for (var n = 0, n2 = len = xy2.length - 1; n <= len; n++, n2--) {
				xy[n] = xy2[n2];
			}
			
			/*
			$.each(xy1,function(KEY,VALUE){
				THIS.append('<div class="nv_point" style="top:'+ VALUE[1] +'px; left:'+ VALUE[0] +'px;"></div>');
			});
			
			$.each(xy2,function(KEY,VALUE){
				THIS.append('<div class="nv_point" style="top:'+ VALUE[1] +'px; left:'+ VALUE[0] +'px;"></div>');
			});
			*/

			xy = xy1.concat(xy);
			
			return xy;
		}
		
		// Slide 啟動
		function SLIDE_ACT(){
			
			// 垂直
			if(SSLIDE.VERTICAL){
				THIS.find(".slide_move").stop().animate({ "top":"-"+ SSLIDE.BLOCK_W * SSLIDE.KEY +"px" },function(){
					if(SSLIDE.KEY == SSLIDE.NUM){
						$(this).css({ "top":"0" });
					}
				});			
			}else{
				THIS.find(".slide_move").stop().animate({ "left":"-"+ SSLIDE.BLOCK_W * SSLIDE.KEY +"px" },function(){
					if(SSLIDE.KEY == SSLIDE.NUM){
						$(this).css({ "left":"0" });
					}
				});
			}
			
		}
		
		// Fade 啟動
		function FADE_ACT(){
			THIS.find(".slide_pic").fadeOut("slow");
			THIS.find(".slide_pic:eq("+ SSLIDE.KEY +")").fadeIn("slow");
		}
		
		// Circle 啟動
		function CIRCLE_ACT(){
			var C_KEY = SSLIDE.KEY;
			for(var C=0;C<SSLIDE.NUM;C++){
				THIS.find(".slide_pic:eq("+ C_KEY +")").css({ "z-index":SSLIDE.LEVEL_Z[C] }).stop().animate({
					"left":SSLIDE.LEVEL_W[C] +"px",
					"top":SSLIDE.LEVEL_H[C] +"px",
					"z-index":SSLIDE.LEVEL_Z[C],
					"width":SSLIDE.LEVEL_X[C],
					"height":SSLIDE.LEVEL_Y[C],
					"padding":SSLIDE.LEVEL_P_X[C],
				});
				
				THIS.find(".slide_pic:eq("+ C_KEY +") img").animate({
					"width":SSLIDE.LEVEL_X[C],
					"height":SSLIDE.LEVEL_Y[C],
				});
				
				THIS.find(".slide_pic:eq("+ C_KEY +") img").css({
					"-webkit-filter":"blur("+ SSLIDE.LEVEL_B[C] +"px)",
					"-moz-filter":"blur("+ SSLIDE.LEVEL_B[C] +"px)",
					"-o-filter":"blur("+ SSLIDE.LEVEL_B[C] +"px)",
					"-ms-filter":"blur("+ SSLIDE.LEVEL_B[C] +"px)",
					"filter":"blur("+ SSLIDE.LEVEL_B[C] +"px)",
				});
				
				C_KEY++;
				
				if(C_KEY >= SSLIDE.NUM){
					C_KEY = 0;
				}
			}
		}
		
		// 小數點位數保留
		function round(num, pos){
			var size = Math.pow(10, pos);
			return Math.round(num * size) / size;
		}
		
		// Orbit 顯示大小
		function ORBIT_SIZE(NOW_H){
			var BLOCK_H = Math.round(THIS.find(".slide_move").outerHeight());
			
			var RATIO = BLOCK_H / 100;
			
			var SIZE_W_RATIO = (SSLIDE.WIDTH / 2) / 100;
			var SIZE_H_RATIO = (SSLIDE.HEIGHT / 2) / 100;
			
			var NOW_RATIO = (0 - (NOW_H - BLOCK_H)) / RATIO;
			
			var SIZE_W_RANGE = Math.round(SIZE_W_RATIO * NOW_RATIO);
			var SIZE_H_RANGE = Math.round(SIZE_H_RATIO * NOW_RATIO);
			
			var RETURN_SIZE = new Array();
			
			RETURN_SIZE[0] = SSLIDE.WIDTH - SIZE_W_RANGE;
			RETURN_SIZE[1] = SSLIDE.HEIGHT - SIZE_H_RANGE;
			
			RETURN_SIZE[2] = round((SSLIDE.WIDTH - RETURN_SIZE[0]) / 2,1);
			RETURN_SIZE[3] = round((SSLIDE.HEIGHT - RETURN_SIZE[1]) / 2,1);
			
			return RETURN_SIZE;
		}
				
		// Orbit 啟動
		function ORBIT_ACT(){
			var NAV_NUM = SSLIDE.NAV.length;
						
			THIS.find(".slide_pic").each(function(KEY){
				if(SSLIDE.DIR_SET){
					SSLIDE.DEF_NAV[KEY]++;
					
					if(SSLIDE.DEF_NAV[KEY] >= NAV_NUM){
						SSLIDE.DEF_NAV[KEY] = 0;
					}
				}else{
					SSLIDE.DEF_NAV[KEY]--;
					
					if(SSLIDE.DEF_NAV[KEY] <= 0){
						SSLIDE.DEF_NAV[KEY] = NAV_NUM - 1;
					}
				}
				
				var NAV = SSLIDE.NAV[SSLIDE.DEF_NAV[KEY]];
				var Z_INDEX = Math.round(NAV[1]);
				
				var SIZE = ORBIT_SIZE(NAV[1]);
				
				$(this).css({ 
					"left":NAV[0],
					"top":round(NAV[1],2),
					"z-index":Z_INDEX,
					"width":SIZE[0] +"px" ,
					"height":SIZE[1] +"px" ,
					"padding":SIZE[2] +"px "+ SIZE[3] +"px",
				});
				
				$(this).find("img").css({
					"width":SIZE[0] +"px",
					"height":SIZE[1] +"px",
				});
			});
		}
	};
})(jQuery);
