// JavaScript Document

	function tiny_load(ROOT_PATH,SELECT){
		
		if(typeof(SELECT) == "undefined" || SELECT == ""){
			SELECT = "#elm1,#elm2,#elm3,#elm4,#elm5,#elm6,.mceEditor";
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
			     "save table contextmenu directionality emoticons template paste textcolor colorpicker"
			   ],
			
			toolbar1: "insertfile undo redo | styleselect fontselect fontsizeselect | cut copy paste pastetext | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink anchor | image responsivefilemanager media |  preview emoticons print code ",
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
			
			table_advtab: true,
			table_cell_advtab: true,
			table_row_advtab: true,

                        convert_urls: false,
                        paste_auto_cleanup_on_paste : true,
                        paste_postprocess : function(pl, o) {
                            // remove &nbsp
                            o.node.innerHTML = o.node.innerHTML.replace(/&nbsp;/ig, " ");
                         },
                         extended_valid_elements : "iframe[src|width|height|name|align],span[style|id|nam|class|lang]",
                        paste_retain_style_properties : "margin padding width height font-size font-weight font-family color text-align ul ol li text-decoration border background float display background-color",
                        paste_word_valid_elements: "b,strong,i,em,h1,h2,table,tr,th,td,ul,ol,li,style,img",
                        paste_data_images: true,
                        template_replace_values: {
                            username : "Jack Black",
                            staffid : "991234",
                            mybb: function(e){
                                e.innerHTML = 'mybb';
                            }
                        },
                        
                        /*           
                        templates : [
                            {
                                title: "edm01單欄",
                                url: "templates/epaper/edm01-single-column.html",
                                description: "edm01的單欄表格"
                            },
                            {
                                title: "edm01雙欄",
                                url: "templates/epaper/edm01-double-column.html",
                                description: "edm01的雙欄表格"
                            },
                            {
                                title: "edm02示範內容",
                                url: "templates/epaper/edm02-template.html",
                                description: "edm02的示範內容"
                            },
                            {
                                title: "edm02空白區域",
                                url: "templates/epaper/edm02-empty-block.html",
                                description: "edm02的空白區域"
                            },
                            {
                                title: "Editor Details",
                                url: "templates/epaper/editor_details.htm",
                                description: "Adds Editor Name and Staff ID"
                            },
                            {
                                title: "Timestamp",
                                url: "templates/epaper/time.htm",
                                description: "Adds an editing timestamp."
                            }
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
