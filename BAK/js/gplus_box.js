// JavaScript Document

/* 使用方法

	$(document).gplus_box({
	});

	**********************************************************************
	INFO : 
	
	預先完成動作 : 將底下兩個部分貼至 HTML 中
	
	<!-- Button HTML -->
	<span id="signinButton">
		<span
			class="g-signin"
			data-callback="signinCallback"
			data-clientid="1041986608795-85kc87nbuchjj5fj2316pdjbcb8228e8.apps.googleusercontent.com"
			data-cookiepolicy="single_host_origin"
			data-scope="https://www.googleapis.com/auth/userinfo.profile"
			data-theme="dark"
		></span>
	</span>
	
	//--------------------------------------------------
	
	<!-- Place this asynchronous JavaScript just before your </body> tag -->
	<script type="text/javascript">
		(function(){
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/client:plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		})();
	</script>
	
*/

(function($){
	$.fn.gplus_box = function(OPTION){
		var GP = jQuery.extend({
			//GAP : 10, // 書籤與內容間距寬度 (px)
			
			//----
			TOKEN : "",
		}, OPTION);
		
		function log(OUTPUT){
			try{
				console.log(OUTPUT);
			}catch(e){
				alert(OUTPUT);
			}
		}
		
		//---- LOGIN
		function signinCallback(authResult){
			
			GP.TOKEN = authResult.access_token;
			
			// 登入成功
			if(authResult['access_token']){
				$("#signinButton").fadeOut(); // 隱藏按鈕
				
			// 登入失敗
			}else if(authResult['error']){
				// There was an error.
				// Possible error codes:
				//   "access_denied" - User denied access to your app
				//   "immediate_failed" - Could not automatically log in the user
				// console.log('There was an error: ' + authResult['error']);
			}
		}
		
		//---- LOG OUT
		function disconnectUser(access_token){
			var revokeUrl = 'https://accounts.google.com/o/oauth2/revoke?token=' +
				GP.TOKEN;
				
			// Perform an asynchronous GET request.
			$.ajax({
				type: 'GET',
				url: revokeUrl,
				async: false,
				contentType: "application/json",
				dataType: 'jsonp',
				success: function(nullResponse) {
					// Do something now that user is disconnected
					// The response is always undefined.
				},
				error: function(e){
					// Handle the error
					// console.log(e);
					// You could point users to manually disconnect if unsuccessful
					// https://plus.google.com/apps
				}
			});
		}
		
		//----
		/*
		$('#g_logout').click(function(E){
			E.preventDefault();
			
			disconnectUser();
		});
		*/
	};
})(jQuery);