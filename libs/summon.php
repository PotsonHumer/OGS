<?php

	class LIBS extends CORE{
		function __construct(){
			
			$self_path = self::real_path(__FILE__);
			include_once $self_path.'class.TemplatePower.inc.php';
			include_once $self_path.'db.php';
		}
	}
	
	new LIBS;
?>