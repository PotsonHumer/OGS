// JavaScript Document

	function tiny_load(ROOT_PATH,SELECT){
		
		if(typeof(SELECT) == "undefined" || SELECT == ""){
			SELECT = "#elm1,#elm2,#elm3,#elm4,#elm5,#elm6,.tiny_input";
		}
		
		tinymce.init({
			selector: SELECT,
			theme: "modern",
			width: 700,
			height: 200,
			language : 'zh_TW',
			
			image_advtab: true,
			external_filemanager_path: ROOT_PATH +"filemanager/",
			filemanager_title:"檔案管理" ,
			external_plugins: { "filemanager" : ROOT_PATH +"filemanager/plugin.min.js"},
			
			plugins: [
			     "image responsivefilemanager advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker",
			     "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			     "save table contextmenu directionality emoticons template paste textcolor"
			   ],
			
			toolbar1: "insertfile undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink anchor | image responsivefilemanager media |  preview emoticons print code ",
				
			/*
			style_formats: [
				{title: 'Bold text', inline: 'b'},
			    {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
			    {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
			    {title: 'Example 1', inline: 'span', classes: 'example1'},
			    {title: 'Example 2', inline: 'span', classes: 'example2'},
			    {title: 'Table styles'},
			    {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
			]
			*/
		});
	}
	
	function open_popup(url){
		var w = 880;
		var h = 570;
		var l = Math.floor((screen.width-w)/2);
		var t = Math.floor((screen.height-h)/2);
		var win = window.open(url, 'ResponsiveFilemanager', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
	}
