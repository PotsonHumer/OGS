
	//<script type="text/javascript" src="http://connect.facebook.net/zh_TW/all.js"></script>
	//<div id="fb-root"></div>
	
	function is_member(UID,NAME,SEX){
		$.post("member.php", {
			func : "is_member",
			UID : UID,
		}, function(DATA,STATUS){
			if(DATA == 0){
				fb_form(UID,NAME,SEX);
			}else{
				fb_member_login(UID);
			}
		});
	}

	function fb_form(UID,NAME,SEX){
		$("#float_block").fadeIn();
		
		$("#float_block input[name=m_name]").val(NAME);
		
		if(SEX == "male"){
			$("#float_block input[name=m_contact_s] option:eq(0)").attr("selected","selected");
		}else{
			$("#float_block input[name=m_contact_s] option:eq(1)").attr("selected","selected");
		}
		
		$("#float_block input[name=m_fb_uid]").val(UID);
	}
	
	window.fbAsyncInit = function() {
		FB.init({
			appId : '0000000000', // App ID
			channelUrl : '{TAG_BASE_URL}channel.html', // Channel File
			status : true, // check login status
			cookie : true, // enable cookies to allow the server to access the session
			xfbml : true // parse XFBML
		});

		FB.Event.subscribe('auth.authResponseChange', function(response) {
			if (response.status === 'connected') {
				
				FB.api('/me', function(response_user) {
					is_member(response.authResponse.userID,response_user.name,response_user.gender);
				});
			
			} else if (response.status === 'not_authorized') {
				FB.login();
			} else {
				FB.login();
			}
		});
	};

	// Load the SDK asynchronously
	(function(d){
			var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {
				return;
			}
			js = d.createElement('script');
			js.id = id;
			js.async = true;
			js.src = "//connect.facebook.net/en_US/all.js";
			ref.parentNode.insertBefore(js, ref);
	}(document));
