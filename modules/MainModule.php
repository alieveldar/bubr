<?
$_SESSION['onsite']=1; $SafeMode=0; $IsIndex=0;
if ($SafeMode==1) { error_reporting(E_ALL & ~E_NOTICE); } // 0-рабочий режим, 1-режим отладки с выводом логов

// Переменные шаблона (В HTML файле шаблона подставляются значения этих переменных) ========================================================================
// Например: если в index.html поместить "$Caption" эта строка заменится на значение переменной $Page["Caption"] ===========================================
// Так же такое же замещение идет с переменными, определенными через систему управления: "Основные настройки" и "Параметры сайта" (массив VARS) ============
$Page=array();  # В этом массиве хранится весь контент (например: $Page["Caption"], $Page["Content"], $Page["Title"] массив должен быть доступен в функциях модулей)

$VarsToHtml = array(
	// Стандартные переменные - должны определяться в модулях, вывод в "Заполнение шаблона сайта"
	"Заголовок страницы (H1)"						=> "Caption", 		// формируется в модулях
	"Заголовок страницы (title)"					=> "Title", 		// формируется ниже, перед выводом дизайна
	
	"Содержание страницы"							=> "Content", 		// формируется в модулях
	"Содержание верхней колонки"					=> "TopContent", 	// формируется в модулях
	"Содержание правой колонки"						=> "RightContent", 	// формируется в модулях
	"Содержание нижний колонки"						=> "BottomContent", // формируется в модулях
		
	"Ключевые слова (keywords)"						=> "KeyWords", 		// формируется ниже, перед выводом дизайна
	"Описание страницы (description)"				=> "Description", 	// формируется ниже, перед выводом дизайна
	"Дочерние страницы"								=> "ChildPages", 	// формируется ниже, только для статичных страниц.
	"Поиск по сайту Яндекс"							=> "SiteSearch",	// Поиск по сайту Яндекс
	
	"Авторизация"									=> "UserAuthLine",	//
	"Регистрация"									=> "UserRegBox",	//  
	
);

// Список необходимых файлов и модулей =====================================================================================================================
// $JSmodules и $CSSmodules можно пополнять в модулях сайта ================================================================================================
// $JSmodules и $CSSmodules дополняются автоматически при запросе модуля (запрашиваются соответствующие js и css файлы) ====================================
$PHPmodules = array(
	"Работа с кэшем"					=> "modules/standart/Cache.php",
	"Общие функции"						=> "modules/standart/Settings.php",
	"Отправка E-mail"					=> "modules/standart/MailSend.php",
	"Авторизация и данные"				=> "modules/standart/UsersLogin.php",
	"Навигация сайта"					=> "modules/standart/CreateMenu.php",
	"Комментарии пользователей"			=> "modules/standart/UsersComments.php",
);

$JSmodules = array(	
	"Библиотека JQuery"					=> "/modules/standart/js/JQuery.js",
	"Передача данных JsHttpRequest"		=> "/modules/standart/js/JsHttpRequest.js",	
	"Основной JS сайта"  				=> "/modules/standart/js/MainModule.js",
	"VK комменты и группы" 				=> "http://vk.com/js/api/openapi.js?123",
);

$CSSmodules = array(
	"Стандартный Pro.CMS"				=> "/template/standart/standart.css",
	"Google fonts: Заголовок"			=> "http://fonts.googleapis.com/css?family=PT+Sans+Caption:700italic,400,700&subset=latin,cyrillic",
);

// Подключение БД ==========================================================================================================================================
/* $GLOBAL=array();*/ $GLOBAL["sitekey"]=1;													// Глобальный массив сайта
require("modules/standart/DataBase.php"); 												// подключение БД
$GLOBAL["StartTime"]=GetMicroTime(); 													// начало работы скриптов
$RealPage = trim($_SERVER["REQUEST_URI"], "/");											// Текущая страница
$RealHost = str_replace(array('http://', 'www.'), '', $_SERVER['HTTP_HOST']);			// Текущий адрес сайта
// Oпределение данных из URL ===============================================================================================================================
$dir=explode("/", $RealPage);
$link	= $dir[0];
$start	= $dir[1];
$page	= $dir[2];
$id		= $dir[3];
$part	= $dir[4];
$sel	= $dir[5];

if ($link=="index") { @header("Location: /"); exit(); }
if (!isset($link) || $link=="" || $link=="/") { $link = ""; $IsIndex=1; }
if (!isset($start) || $start=="") $s = 0;
if (!isset($page) || $page=="")	$page = 0;
if (!isset($id) || $id=="")		$id = 0;
if (!isset($part) || $part=="")	$part = 0;
if (!isset($sel) || $sel=="")	$sel = 0;
if (!isset($fd) || $fd=="")		$fd = 0;

// Запрос стандартных модулей PHP ====================================================================================================================================
foreach ($PHPmodules as $name=>$module) {
	if (is_file(trim($module,"/"))) { $GLOBAL["log"].="<i>Подключение PHP</i>: модуль &laquo;".$name."&raquo; подключен<hr>";
	require($module); } else { $GLOBAL["log"].="<u>Подключение PHP</u>: модуль &laquo;".$name."&raquo; не найден (<b>".$module."</b>)<hr>"; }
}

if ($GLOBAL["USER"]["role"]>1) { $VARS["cachemenu"]=0; $VARS["cacheblock"]=0; $VARS["cachepages"]=0; }
if ($_SESSION["userid"]==1 || $_SESSION["userid"]==2) { $VARS["cachemenu"]=0; $VARS["cacheblock"]=0; $VARS["cachepages"]=0; }

// Содержание страницы =======================================================================================================================================
### Массив доменов и субдоменов и Определяем текущий поддомен
#$Domains = GetDomains(); $tmp=array(); $tmp=array_flip($Domains); 
#$sd=explode(".", $RealHost); $SubDomain = $tmp[$sd[(sizeof($sd)-3)]];

# Запрос стандартного модуля со общими данными (контент для всего сайта, а не определенного раздела)
if (is_file("modules/StaticBlocks.php")) { require("modules/StaticBlocks.php"); $GLOBAL["log"].="<i>Подключение PHP</i>: общий модуль &laquo;StaticBlocks.php&raquo; подключен<hr>"; }

if ($IsIndex==1) {
	# Если это главная страница сайта
	$data=DB("SELECT * FROM `_pages` WHERE (`isindex`='1' && `stat`='1' && domain='".(int)$SubDomain."') LIMIT 1");
} else {
	# Если НЕ главная страница сайта 
	if (Dbsel($RealPage)!=Dbsel($link)) { $q="((`link`='".Dbsel($RealPage)."' && `module`='') || (`link`='".Dbsel($link)."' && `module`!=''))"; } else { $q="`link`='".Dbsel($link)."'"; }
	$data=DB("SELECT * FROM `_pages` WHERE (".$q." && `stat`='1') LIMIT 1");
}

if ($data["total"]==0){
	@header("HTTP/1.1 404 Not Found"); $Robots='<meta name="robots" content="noindex" />'; $Page404=1;
	$Page["Content"]=@file_get_contents($ROOT."/template/404.html"); $Page["Caption"]="Страница не найдена - 404";
	$GLOBAL["log"].="<u>Содержание</u>: страница &laquo;".$RealPage."&raquo; не найдена<hr>";
} else {
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if ($IsIndex==1) { $link=$node["link"]; }

	# Если данный раздел принадлежит другому поддомену
	#if (trim($Domains[$node["domain"]].".".$VARS["mdomain"], ".")!=$RealHost) { @header("location: http://".trim($Domains[$node["domain"]].".".$VARS["mdomain"], ".")."/".$RealPage); exit(); }
	
	# Генерация контента
	$Page["Node"]		 = $node;
	$Page["Link"]	 	 = $node["link"];
	$Page["KeyWords"]	 = $node["kw"];
	$Page["Description"] = $node["ds"];
	$Page["Data"]		 = $node["data"];
	$Page["Caption"]	 = $node["name"];
	$Page["ShortName"]	 = $node["shortname"];
	$Page["Content"]	 = $node["text"];

	if ($node["module"]=="") {
		#Если найдена статичная страница
		@header ("HTTP/1.0 200 Ok"); $Robots='<meta name="robots" content="index, follow" />'; $Page404=0;
		$GLOBAL["log"].="<i>Содержание</i>: вывод статичной страницы &laquo;<b>".$Page["Link"]."</b>&raquo;<hr>";
	} else {
		
	$GLOBAL["log"].="<h1>Работа основного модуля: ".$node["module"]."</h1>";
	
	if (is_file("modules/page_mods/".$node["module"]."-".$node["link"].".php")) { 
			/* PHP */ @header ("HTTP/1.0 200 Ok"); $Robots='<meta name="robots" content="index, follow" />'; require ("modules/page_mods/".$node["module"]."-".$node["link"].".php");
			/* JS */  if (is_file("modules/page_mods/".$node["module"]."-".$node["link"].".js")) { $JSmodules[$node["name"]]="/modules/page_mods/".$node["module"]."-".$node["link"].".js"; }
			/* CSS */ if (is_file("modules/page_mods/".$node["module"]."-".$node["link"].".css")) { $CSSmodules[$node["name"]]="/modules/page_mods/".$node["module"]."-".$node["link"].".css"; }
			$GLOBAL["log"].="<i>Модификатор PHP</i>: файл &laquo;".$node["module"]."_".$node["link"].".php&raquo; раздела &laquo;".$link."&raquo; подключен<hr>";
	#Ищем основной файл php (пример: /modules/lenta/lenta.php)
	} elseif (is_file("modules/".$node["module"]."/".$node["module"].".php")) { 
			/* PHP */ @header ("HTTP/1.0 200 Ok"); $Robots='<meta name="robots" content="index, follow" />'; require ("modules/".$node["module"]."/".$node["module"].".php");
			/* JS */  if (is_file("modules/".$node["module"]."/".$node["module"].".js")) { $JSmodules[$node["name"]]="/modules/".$node["module"]."/".$node["module"].".js"; }
			/* CSS */ if (is_file("modules/".$node["module"]."/".$node["module"].".css")) { $CSSmodules[$node["name"]]="/modules/".$node["module"]."/".$node["module"].".css"; }
			$GLOBAL["log"].="<i>Подключение PHP</i>: модуль &laquo;".$node["module"]."&raquo; раздела &laquo;".$link."&raquo; подключен<hr>";
	} else {
	#Раздел на модуле, но файлы не найдены
			@header("HTTP/1.1 404 Not Found"); $Robots='<meta name="robots" content="noindex" />'; $Page404=1;
			$Page["Content"]=@file_get_contents($ROOT."/template/404.html"); $Page["Caption"]="Страница не найдена - 404";
			$GLOBAL["log"].="<u>Подключение PHP</u>: модуль &laquo;".$node["module"]."&raquo; раздела &laquo;".$link."&raquo; не найден<hr>";
	}
	$GLOBAL["log"].="<h1>Работа дополнительных модулей</h1>";	
	}
	
	if (is_file("modules/test/".$node["link"]."-".$page.".php") && $Page404!=1) { @require("modules/test/".$node["link"]."-".$page.".php"); }
	
	if (is_file("modules/page_mods/".$node["link"].".php") && $Page404!=1) { @require("modules/page_mods/".$node["link"].".php"); }
	if (is_file("modules/page_mods/".$node["link"].".js") && $Page404!=1) { $JSmodules["modules/page_mods/".$node["link"].".js"]="/modules/page_mods/".$node["link"].".js"; }
	if (is_file("modules/page_mods/".$node["link"].".css") && $Page404!=1) { $CSSmodules["modules/page_mods/".$node["link"].".css"]="/modules/page_mods/".$node["link"].".css"; }	
}

############################################################################################################################################
############################################################################################################################################
############################################################################################################################################

// Определение шаблона сайта ==================================================================================================================================================
/* if ($node["design"]=="0" || $node["design"]=="" || $Page404==1) { $data=DB("SELECT `folder` FROM `_designs` WHERE (`stat`='1') LIMIT 1"); if ($data["total"]==1) { @mysql_data_seek($data["result"], 0); $tmp=@mysql_fetch_array($data["result"]); $design=$tmp["folder"]; } else { $design="index"; } $GLOBAL["log"].="<i>Шаблон дизайна</i>: определена папка по умолчанию &laquo;".$design."&raquo;<hr>";
} else { $design=$node["design"]; $GLOBAL["log"].="<i>Шаблон дизайна</i>: определена папка раздела &laquo;".$design."&raquo;<hr>"; } */
// Загрузка шаблона сайта =====================================================================================================================================================

if (!$GLOBAL["design"]) { 
	$specialdesign=array("index"); if (in_array($node["module"], $specialdesign) || in_array($node["link"], $specialdesign)) { $design="mainpage"; } else { $design="index"; }
} else {
	$design=$GLOBAL["design"];
}

if (is_file("template/".$design."/".$design.".html")) {
	$DesignHtml=@file_get_contents("template/".$design."/".$design.".html");
	$GLOBAL["log"].="<i>Шаблон дизайна</i>: загружен шаблон &laquo;"."template/".$design."/".$design.".html"."&raquo;<hr>";
} else {
	$DesignHtml="<h1>Не подключен шаблон дизайна</h1>";
	$GLOBAL["log"].="<u>Шаблон дизайна</u>: не найден шаблон &laquo;"."template/".$design."/".$design.".html"."&raquo;<hr>";
}

if (is_file("template/".$design."/".$design.".css")) {
	$CSSmodules["template/".$design."/".$design.".css"]="/template/".$design."/".$design.".css";
}

if (is_file("template/".$design."/".$design.".js")) {
	$JSmodules["template/".$design."/".$design.".js"]="/template/".$design."/".$design.".js";
}

// Заполнение шаблона сайта ===================================================================================================================================================
if ($node["isindex"]==1) { $Page["Title"]=$VARS["sitename"]; } else { if ($Page["Caption"]!="") { $Page["Title"]=strip_tags(NormalCaption($Page["Caption"]))." ".$VARS["splitter"]." ".$VARS["sitename"]; } else { $Page["Title"]=$Page["Title"]." ".$VARS["splitter"]." ".$VARS["sitename"]; }}
if ($Page["KeyWords"]=="") { $Page["KeyWords"]=NormalCaption($Page["Caption"]).", ".$VARS["keywords"]; } else { $Page["KeyWords"]=$Page["KeyWords"]; }
if ($Page["Description"]=="") { $Page["Description"]=NormalCaption($Page["Caption"]).", ".$VARS["description"]; } else { $Page["Description"]=$Page["Description"]; }
if ($Page["Caption"]!="" && $Page404==0) { $Page["Caption"]="<h1>".nl2br($Page["Caption"])."</h1>"; } if ($node["isindex"]==1) { $Page["Caption"]=""; }

$Page["Title"]=NormalCaption($Page["Title"]); if ($link=="post" && $start=="view") { $Page["Caption"]=''; } 

foreach ($VarsToHtml as $key=>$value) { $DesignHtml=str_replace('$'.$value, $Page[$value], $DesignHtml); } # Переменные шаблона дизайна (определяются в начале этого файла)
foreach ($VARS as $key=>$value) { $DesignHtml=str_replace('$'.$key, $value, $DesignHtml); } # Параметры и настройки сайта (определяются в панели администрирования)
foreach ($MENU as $key=>$value) { $DesignHtml=str_replace('$'.$key, $value, $DesignHtml); } # Меню сайта (определяются в панели администрирования)

// Запрос вспомогательных модулей JS ====================================================================================================================================
$GLOBAL["log"].="<h1>Запрос дополнительных скриптов</h1>";
foreach ($JSmodules as $name=>$module) {
	if (strpos($module, "http:")===false) {
		if (is_file(trim($module,"/"))) { $GLOBAL["log"].="<i>Подключение JS</i>: скрипт &laquo;".$name."&raquo; подключен<hr>";
		$GLOBAL["JSModules"].="<script src='".$module."' type='text/javascript'></script>"."\r\n";
		} else { $GLOBAL["log"].="<u>Подключение JS</u>: скрипт &laquo;".$name."&raquo; не найден (<b>".$module."</b>)<hr>"; }
	} else {
		$GLOBAL["log"].="<i>Подключение JS</i>: внешний скрипт &laquo;".$name."&raquo; подключен<hr>";
		$GLOBAL["JSModules"].="<script src='".$module."' type='text/javascript'></script>"."\r\n";
	}
}

// Запрос CSS для вспомогательных модулей =====================================================================================================================================
$GLOBAL["log"].="<h1>Запрос дополнительных стилей</h1>";
foreach ($CSSmodules as $name=>$module) {
	if (strpos($module, "http")===false) {
		if (is_file(trim($module,"/"))) { $GLOBAL["log"].="<i>Подключение CSS</i>: стиль &laquo;".$name."&raquo; подключен<hr>";
		$GLOBAL["CSSModules"].="<link rel='stylesheet' type='text/css' href='".$module."' media='all' />"."\r\n";
		} else { $GLOBAL["log"].="<u>Подключение CSS</u>: стиль &laquo;".$name."&raquo; не найден (<b>".$module."</b>)<hr>"; }
	} else {
		$GLOBAL["log"].="<i>Подключение CSS</i>: внешний стиль &laquo;".$name."&raquo; подключен<hr>";
		$GLOBAL["CSSModules"].="<link rel='stylesheet' type='text/css' href='".$module."' media='all' />"."\r\n";
	}
}

@mysql_close();

// Вывод шаблона сайта ========================================================================================================================================================
$RENDER='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.$r;
$RENDER.='<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" dir="ltr">'.$r.'<head>'.$r;
$RENDER.='<title>'.$Page["Title"].'</title>'.$r;
$RENDER.=$Robots.$r;
$RENDER.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$r;
$RENDER.='<meta name="keywords" content=\''.trim($Page["KeyWords"], ",").'\' />'.$r;
$RENDER.='<meta name="description" content=\''.trim($Page["Description"], ",").'\' />'.$r;
$RENDER.='<link rel="shortcut icon" href="/favicon.png" type="image/x-icon" />'.$r;
$RENDER.='<link rel="alternate" type="application/rss+xml" title=\''.$VARS["sitename"].'\' href="http://'.$VARS["mdomain"].'/rss.xml" />'.$r;
$RENDER.="<meta name='yandex-verification' content='db0c96ec7541fafb' />";
$RENDER.=$GLOBAL["CSSModules"];
$RENDER.=$GLOBAL["JSModules"];
$RENDER.='</head>'.$r.'<body>'.$r;
$RENDER.='<input type="hidden" id="BoxCount" value="0" /><input type="hidden" id="DomainId" value="'.(int)$SubDomain.'" /><input type="hidden" id="UserId" value="'.(int)$_SESSION["userid"].'" />';
$RENDER.=$DesignHtml.$r;
$RENDER.="<div class='CountersSet'>".$VARS["counters"]."</div>";
$RENDER.="<script type='text/javascript' src='/modules/standart/js/JQuery.COOKIE.js?123'></script>";
if (strpos($_SESSION["Referer"], "facebook")===false) { $RENDER.="<div id='VKGroupInvite'><div id='vk_group_invite'></div><div class='C10'></div><a href='javascript:void(0);' id='cancelVKinvate'>Спасибо, я уже состою в группе</a><a href='javascript:void(0);' id='closeVKinvate'>Закрыть окно</a><div class='C'></div></div>";
} else { $RENDER.="<div id='FBGroupInvite'>".'<iframe id="fb_group_invite" src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fbubrru&amp;width=350&amp;height=290&amp;colorscheme=light&amp;show_faces=true&amp;header=true&amp;stream=false&amp;show_border=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:350px; height:290px;" allowTransparency="true"></iframe>	'."<div class='C10'></div><a href='javascript:void(0);' id='cancelFBinvate'>Спасибо, я уже состою в группе</a><a href='javascript:void(0);' id='closeFBinvate'>Закрыть окно</a><div class='C'></div></div>"; }
$RENDER.="<!-- ".$_SESSION["Referer"]." -->";
$RENDER.='</body>'.$r.'</html>';


echo $RENDER; if ($HardCacheFile!="" && $HardCacheFile!=NULL && (int)$_SESSION['userid']==0) { @file_put_contents($HardCacheFile, $RENDER); }
// Вывод логов сайта ===========================================================================================================================================================
$GLOBAL["StopTime"]=GetMicroTime(); 
$GLOBAL["RunTime"]=$GLOBAL["StopTime"]-$GLOBAL["StartTime"];

if ($SafeMode==1 && ($_SESSION["userid"]==1 || $_SESSION["userid"]==2)) { 
	echo "<div id='SystemLogs'>";
		echo "<h1>Лог выполнения скриптов</h1>".$GLOBAL["log"];
		if (isset($_SESSION)) {
			echo "<h1>Значения  в ".'$_SESSION'."</h1>";
			foreach ($_SESSION as $key=>$value) { echo "<b>$key</b> -> &laquo;<i>$value</i>&raquo;<hr>"; }
		}
		if (isset($VARS)) {
			echo "<h1>Значения  в ".'$VARS'."</h1>";
			foreach ($VARS as $key=>$value) { echo "<b>$key</b> -> &laquo;<i>$value</i>&raquo;<hr>"; }
		}
		echo "<h1>Время выполнения и количество запросов</h1>";
		echo "<i>Количество запросов SQL:</i> <b>".round($GLOBAL["sqlcount"], 3)."</b><hr>";
		echo "<i>Время выполнения SQL:</i> <b>".round($GLOBAL["sqltime"], 3)."</b> с.<hr>";
		echo "<i>Время выполнения PHP:</i> <b>".round($GLOBAL["RunTime"], 3)."</b> с.";
	echo "</div>";
} else {
	echo "<!-- CountSQL: ".round($GLOBAL["sqlcount"], 3)." | TimeSQL: ".round($GLOBAL["sqltime"], 3)."c. | TotalTime: ".round($GLOBAL["RunTime"], 3)."c. -->";
}
// =============================================================================================================================================================================
?>