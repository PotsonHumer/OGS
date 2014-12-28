<?php

	class TEST{
		function __construct(){
			
			//SESS::write('system',array('sys_test' => 'test','sys_id' => 'ok!!'));
			SESS::get('system');
			print_r(SESS::$val);
		}
	}
	

?>