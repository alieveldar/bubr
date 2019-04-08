<?
session_start(); $G=$_GET;
$cat=cutdata($G["cat"]);
$id=cutdata($G["id"]);
$pid=cutdata($G["pid"]);
$cid=cutdata($G["cid"]);
$d1=cutdata($G["d1"]);
$d2=cutdata($G["d2"]);
$it=cutdata($G["it"]);
$pg=$G["pg"] ? cutdata($G["pg"]) : 1;

mb_internal_encoding("UTF-8");

if ($cat=='') { $isindex=1; $cat="adm_index"; }
if (!isset($_SESSION['userid']) || !isset($_SESSION['userrole'])) { $_SESSION['userrole']=0; $_SESSION['userid']=0; }
if (!isset($_SESSION['adminblock']) || !isset($_SESSION['adminlevel'])) { $_SESSION['adminblock']=0; $_SESSION['adminlevel']=0; }

//Error_Reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL | E_STRICT); ini_set('display_errors', 'Off');
$GLOBAL["sitekey"]=1; $AdminText=''; $AdminMenu=''; $AdminLogin=''; $Advert='';
$GLOBAL["error"]=0; $AddJSFile=""; $AddCSSFile="";

### Подключение стандартных модулей
@require("../modules/standart/DataBase.php");
@require("modules/AdminTextModule.php");

### Подключение модулей администрирования
if (!isset($_SESSION['userrole']) || (int)$_SESSION['userrole']<2 || $_SESSION['userid']==0) { 
	@require("modules/AdminLogin.php");
} else {
	@require("../modules/standart/Settings.php");
	#находим раздел сайта по алиасу раздела
	list($alias, $file)=explode("_", $cat);	
	if ($alias=="adm") {
	#стандартный административный модуль
		if (is_file("modules/adm/".$file.".php")) { @require("modules/adm/".$file.".php");
			if (is_file("modules/adm/js/".$file.".js")) { $AddJSFile='<script type="text/javascript" language="javascript" src="modules/adm/js/'.$file.'.js"></script>'; }
			if (is_file("modules/adm/css/".$file.".css")) { $AddCSSFile='<link rel="stylesheet" type="text/css" href="modules/adm/css/'.$file.'.css">'; } 
		} else { $AdminText=ATextReplace("AdmModuleError", $file); $GLOBAL["error"]=1; }
		
		
	} elseif ($alias=="banners") {
		if (is_file("modules/banners/".$file.".php")) { @require("modules/banners/".$file.".php");
			if (is_file("modules/banners/js/".$file.".js")) { $AddJSFile='<script type="text/javascript" language="javascript" src="modules/banners/js/'.$file.'.js"></script>'; }
		} else { $AdminText=ATextReplace("AdmModuleError", $file); $GLOBAL["error"]=1; }
		
		
	} elseif ($alias=="import") {
		if (is_file("modules/importsys/".$file.".php")) { @require("modules/importsys/".$file.".php");
			if (is_file("modules/importsys/js/".$file.".js")) { $AddJSFile='<script type="text/javascript" language="javascript" src="modules/importsys/js/'.$file.'.js"></script>'; }
		} else { $AdminText=ATextReplace("AdmModuleError", $file); $GLOBAL["error"]=1; }
		
		
	} elseif ($alias=="strochki") {
	if (is_file("modules/strochki/".$file.".php")) { @require("modules/strochki/".$file.".php");
		if (is_file("modules/strochki/js/".$file.".js")) { $AddJSFile='<script type="text/javascript" language="javascript" src="modules/strochki/js/'.$file.'.js"></script>'; }
	} else { $AdminText=ATextReplace("AdmModuleError", $file); $GLOBAL["error"]=1; }

	} elseif ($alias=="moderate") {
	if (is_file("modules/moderate/".$file.".php")) { @require("modules/moderate/".$file.".php");
		if (is_file("modules/moderate/js/".$file.".js")) { $AddJSFile='<script type="text/javascript" language="javascript" src="modules/moderate/js/'.$file.'.js"></script>'; }
	} else { $AdminText=ATextReplace("AdmModuleError", $file); $GLOBAL["error"]=1; }	
		
	} else {
	#внешний подключаемый модуль
		$data=DB("SELECT `id`, `module`, `shortname`, `link` FROM `_pages` WHERE (`link`='$alias') LIMIT 1"); 
		if ($data["total"]==1) {
			#находим тип модуля по алиасу раздела сайта в таблице _pages (`link` - название раздела, `module` - соответствующий модуль)
			@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $module=$ar["module"];
			#подключаем файл модуля согласно типу модуля и его разделу, если файл не найден выводим ошибку
			if (is_file("modules/".$module."/".$file.".php")) {
				@require("modules/".$module."/".$file.".php");
				if (is_file("modules/".$module."/js/".$file.".js")) { $AddJSFile='<script type="text/javascript" language="javascript" src="modules/'.$module.'/js/'.$file.'.js"></script>'; } 
				if (is_file("modules/".$module."/css/".$file.".css")) { $AddCSSFile='<link rel="stylesheet" type="text/css" href="modules/'.$module.'/css/'.$file.'.css">'; } 
			} else { $AdminText=ATextReplace("PlugInModuleError", $alias." / ".$file, $module); $GLOBAL["error"]=1; }
		} else {
			$AdminText=ATextReplace("AliasWithoutModule", $alias); $GLOBAL["error"]=1;
		}
	}
	@require("modules/AdminMenuModule.php");
}

function cutdata($var) { $cut=array("'", '"', "(", ")", "&", "+", "-", "%"); $var=str_replace($cut, "", $var); return $var; }

function GetDataSet($data=0, $add="") {
	global $GLOBAL; if ($data==0) { $data=mktime()+($GLOBAL["timezone"]*3600); }
	$h=date("H", $data); $i=date("i", $data); $s=date("s", $data);
	for ($j=0; $j<24; $j++) { if ($j<10) { $k="0".$j; } else { $k=$j; } if ($j==($h+0)) { $opt1.="<option value='$k' selected>".$k."</option>"; } else { $opt1.="<option value='$k'>".$k."</option>"; }}
	for ($j=0; $j<60; $j++) { if ($j<10) { $k="0".$j; } else { $k=$j; } if ($j==($i+0)) { $opt2.="<option value='$k' selected>".$k."</option>"; } else { $opt2.="<option value='$k'>".$k."</option>"; }}
	for ($j=0; $j<60; $j++) { if ($j<10) { $k="0".$j; } else { $k=$j; } if ($j==($s+0)) { $opt3.="<option value='$k' selected>".$k."</option>"; } else { $opt3.="<option value='$k'>".$k."</option>"; }}
	$text='<input id="datepick'.$add.'" name="ddata1'.$add.'" type="text" readonly value="'.date("d.m.Y", $data).'"><div><select name="ddata2'.$add.'">'.$opt1.'</select></div> : ';
	$text.='<div><select name="ddata3'.$add.'">'.$opt2.'</select></div> : <div><select name="ddata4'.$add.'">'.$opt3.'</select></div>'; return $text;
}


function GetTimeSet($data=0, $add="") {
	global $GLOBAL; if ($data==0) { $data=mktime()+($GLOBAL["timezone"]*3600); }
	$h=date("H", $data); $i=date("i", $data);
	for ($j=0; $j<24; $j++) { if ($j<10) { $k="0".$j; } else { $k=$j; } if ($j==($h+0)) { $opt1.="<option value='$k' selected>".$k."</option>"; } else { $opt1.="<option value='$k'>".$k."</option>"; }}
	for ($j=0; $j<60; $j++) { if ($j<10) { $k="0".$j; } else { $k=$j; } if ($j==($i+0)) { $opt2.="<option value='$k' selected>".$k."</option>"; } else { $opt2.="<option value='$k'>".$k."</option>"; }}
	for ($j=0; $j<60; $j++) { if ($j<10) { $k="0".$j; } else { $k=$j; } if ($j==($s+0)) { $opt3.="<option value='$k' selected>".$k."</option>"; } else { $opt3.="<option value='$k'>".$k."</option>"; }}
	$text='<div><select name="ddata2'.$add.'">'.$opt1.'</select></div> : ';
	$text.='<div><select name="ddata3'.$add.'">'.$opt2.'</select></div>'; return $text;
}

function GetSelected($ar, $id) {
	$text=""; foreach ($ar as $key=>$val) { if ($key==$id) { $text.="<option value='$key' selected style='color:#FFF; background:#036;'>$val</option>"; } else { $text.="<option value='$key'>$val</option>"; }} return $text;
}
?>