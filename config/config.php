<?php

	$config["root"] = "/OGS/";
	$config["url"] = "59.126.99.159";
	$config["host"] = 'http://'.$config["url"].$config["root"];
	
	// 初始路徑
	$config["img"] = $config["root"].'images/'; // 圖片路徑
	$config["css"] = $config["root"].'css/'; // css 路徑
	$config["js"] = $config["root"].'js/'; // js 路徑
	$config["file"] = $config["root"].'file/'; // 檔案路徑
	$config["manage"] = 'ogsadmin'."/"; // 後台目錄 (請保留最後的斜線)
	$config["temp"] = CORE::$root.'temp'; // 樣板路徑
	
	// 雜項設定
	$config["sort"]	= 'asc'; // 資料庫排序
	$config["item_num"]	= 12; // 每分頁幾個項目
	$config["list_num"]	= 10; // 最多顯示幾個分頁連結
	
	// key : 語系名稱參數 , value : 語系資料庫參數
	$config["lang"] = array(
		'eng' => 'eng', // default
		'cht' => 'cht',
	);
	
	#### DON'T CHANGE THIS ####
	$lang_keys = array_keys($config["lang"]);
	
	$config["langfix"] = $lang_keys[0]; // 紀錄語系狀態變數
	$config["prefix"] = $config["lang"][$lang_keys[0]]; // 紀錄語系資料庫狀態變數
	###########################
	
	
	#### DB connect ####
	$config["connect"] = array(
		'host' => 'localhost',
		'user' => 'root',
		'pass' => 'studiodd',
		'db' => 'ogs'
	);
	####################
	
	
	#### autoload filter ####
	$config["file_filter"] = array('core','router');
	$config["dir_filter"] = array();
	#########################
	
	
	#### MEMBER ####
	$config["m_sync"] = false; // 多語系使用同個會員資料庫
	################
	
	
	#### agents ####
	$config["ag_zone"] = array(
		1 => "Asia",
		2 => "Europe",
		3 => "Middle East",
		4 => "America"
	);
	################
	
	#### AD ####
	// 廣告分類
	$config["ad_cate"] = array(
		1 => '首頁 Banner',
		2 => '內頁 Banner',
		3 => '首頁上方圖示',
		4 => '首頁型錄連結',
	);
	############
	
	$config["sess"] = 'ogs';
	
	#### country ####
	$config["country"] = array(
		"Afghanistan",
		"Albania", 
		"Algeria", 
		"American Samoa", 
		"Andorra", 
		"Angola", 
		"Anguilla", 
		"Antarctica", 
		"Antigua And Barbuda",
		"Argentina", 
		"Armenia", 
		"Aruba", 
		"Australia", 
		"Austria", 
		"Azerbaijan",
		"Bahamas",
		"Bahrain", 
		"Bangladesh", 
		"Barbados", 
		"Belarus", 
		"Belgium",
		"Belize",
		"Benin", 
		"Bermuda", 
		"Bhutan", 
		"Bolivia", 
		"Bosnia Hercegovina",
		"Botswana", 
		"Bouvet Island", 
		"Brazil", 
		"British Indian Ocean Territory", 
		"Brunei Darussalam",
		"Bulgaria", 
		"Burkina Faso",
		"Burundi", 
		"Byelorussian SSR",
		"Cambodia", 
		"Cameroon", 
		"Canada", 
		"Cape Verde",
		"Cayman Islands",
		"Central African Republic",
		"Chad", 
		"Chile", 
		"China", 
		"Christmas Island",
		"Cocos Islands",
		"Colombia", 
		"Comoros", 
		"Congo", 
		"Congo, The Democratic Republic Of",
		"Cook Islands",
		"Costa Rica",
		"Cote D'Ivoire",
		"Croatia", 
		"Cuba", 
		"Cyprus", 
		"Czech Republic",
		"Czechoslovakia",
		"Denmark", 
		"Djibouti", 
		"Dominica", 
		"Dominican Republic",
		"East Timor",
		"Ecuador", 
		"Egypt", 
		"El Salvador",
		"England", 
		"Equatorial Guinea",
		"Eritrea", 
		"Estonia", 
		"Ethiopia", 
		"Falkland Islands",
		"Faroe Islands",
		"Fiji", 
		"Finland", 
		"France", 
		"French Guiana",
		"French Polynesia",
		"French Southern Territories",
		"Gabon", 
		"Gambia", 
		"Georgia", 
		"Germany", 
		"Ghana", 
		"Gibraltar", 
		"Great Britain",
		"Greece", 
		"Greenland", 
		"Grenada", 
		"Guadeloupe", 
		"Guam", 
		"Guatemela", 
		"Guernsey", 
		"Guinea", 
		"Guinea-Bissau",
		"Guyana", 
		"Haiti", 
		"Heard and McDonald Islands",
		"Honduras", 
		"Hong Kong",
		"Hungary", 
		"Iceland", 
		"India", 
		"Indonesia", 
		"Iran",
		"Iraq", 
		"Ireland", 
		"Isle Of Man",
		"Israel", 
		"Italy", 
		"Jamaica", 
		"Japan", 
		"Jersey", 
		"Jordan", 
		"Kazakhstan", 
		"Kenya", 
		"Kiribati", 
		"Korea, Democratic People's Republic Of",
		"Korea, Republic Of",
		"Kuwait", 
		"Kyrgyzstan", 
		"Lao People's Democratic Republic",
		"Latvia", 
		"Lebanon", 
		"Lesotho", 
		"Liberia", 
		"Libyan Arab Jamahiriya",
		"Liechtenstein", 
		"Lithuania", 
		"Luxembourg", 
		"Macau", 
		"Macedonia", 
		"Madagascar", 
		"Malawi", 
		"Malaysia", 
		"Maldives", 
		"Mali", 
		"Malta", 
		"Marshall Islands",
		"Martinique", 
		"Mauritania", 
		"Mauritius", 
		"Mayotte", 
		"Mexico", 
		"Micronesia, Federated States Of",
		"Moldova, Republic Of",
		"Monaco", 
		"Mongolia", 
		"Montserrat", 
		"Morocco", 
		"Mozambique", 
		"Myanmar", 
		"Namibia", 
		"Nauru", 
		"Nepal", 
		"Netherlands", 
		"Netherlands Antilles",
		"Neutral Zone",
		"New Caledonia",
		"New Zealand",
		"Nicaragua", 
		"Niger", 
		"Nigeria", 
		"Niue", 
		"Norfolk Island",
		"Northern Mariana Islands",
		"Norway", 
		"Oman", 
		"Pakistan", 
		"Palau", 
		"Panama", 
		"Papua New Guinea",
		"Paraguay", 
		"Peru", 
		"Philippines", 
		"Pitcairn", 
		"Poland", 
		"Portugal", 
		"Puerto Rico",
		"Qatar", 
		"Reunion", 
		"Romania", 
		"Russian Federation",
		"Rwanda", 
		"Saint Helena",
		"Saint Kitts And Nevis",
		"Saint Lucia",
		"Saint Pierre and Miquelon",
		"Saint Vincent and The Grenadines",
		"Samoa", 
		"San Marino",
		"Sao Tome and Principe",
		"Saudi Arabia",
		"Senegal", 
		"Seychelles", 
		"Sierra Leone",
		"Singapore", 
		"Slovakia", 
		"Slovenia", 
		"Solomon Islands",
		"Somalia", 
		"South Africa",
		"South Georgia and The Sandwich Islands",
		"Spain", 
		"Sri Lanka",
		"Sudan", 
		"Suriname", 
		"Svalbard and JanMayen Islands",
		"Swaziland", 
		"Sweden", 
		"Switzerland", 
		"Syrian Arab Republic",
		"Taiwan", 
		"Tajikista", 
		"Tanzania, United Republic Of",
		"Thailand", 
		"Togo", 
		"Tokelau", 
		"Tonga", 
		"Trinidad and Tobago",
		"Tunisia", 
		"Turkey", 
		"Turkmenistan", 
		"Turks and Caicos Islands",
		"Tuvalu", 
		"Uganda", 
		"Ukraine", 
		"United Arab Emirates",
		"United Kingdom",
		"United States",
		"United States Minor Outlying Islands",
		"Uruguay", 
		"USSR", 
		"Uzbekistan", 
		"Vanuatu", 
		"Vatican City State",
		"Venezuela", 
		"Vietnam", 
		"Virgin Islands (British)",
		"Virgin Islands (U.S.)",
		"Wallis and Futuna Islands",
		"Western Sahara",
		"Yemen, Republic of",
		"Yugoslavia", 
		"Zaire", 
		"Zambia", 
		"Zimbabwe",
	);
	#################
	
	return $config;
	
?>