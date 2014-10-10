<?php	
	class MAIN_CFG{
		function __construct(){
			
			$this->url = "http://hearthfarm.com";
			$this->root = "/";
			$this->file_root = "/";
			
			$this->css = $this->root."css/";
			$this->img = $this->root."image/";
			$this->js = $this->root."js/";
			
			$this->ad_root = $this->root."hf_admin/";
			
			$this->page_num = 12; //單頁顯示數量
			
			$this->upload_files = $this->file_root."upload_files/";
			$this->sess = "hearthfarm"; //seesion name
			
			$this->all_lang = array("cht","eng");
		}
	}

?>