<?
### Генерация всех меню сайта ============================================================================================================================================================
### Для генерации своего меню создайте файл "site_menu_NAME.php" в папке "page_mods", где NAME - "Переменная" из "Панели администрирования / Навигация сайта"

$MENU=array();
if (!isset($VARS["mdomain"]) || $VARS["mdomain"]=="") { $VARS["mdomain"]="prokazan.ru"; }
$datam=DB("SELECT `id`,`name`,`link` FROM `_menulist` WHERE (`stat`='1')");
if ($datam["total"]==0) {
	$GLOBAL["log"].="<u>Навигация</u>: не найдено активных навигационных цепочек.<hr>";	
} else {
	for ($m=0; $m<$datam["total"]; $m++): @mysql_data_seek($datam["result"],$m); $am=@mysql_fetch_array($datam["result"]);
	if (is_file("modules/page_mods/site_menu_".$am["link"].".php")) {
		@require("modules/page_mods/site_menu_".$am["link"].".php");
	} else {
		$file="site_all_menus-menu".$am["id"]; $type="cachemenu";
		if (RetCache($file, $type)=="true") { list($MENU[$am["link"]], $cap)=GetCache($file, 0); } else {		
		// ---- получение списка пунктов  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----
		$itext=""; $ul=0; $mvl=array(); $items=array(); $prlvl=0; $data=DB("SELECT * FROM `_menuitem` WHERE (`nid`='".$am["id"]."' && `stat`='1') ORDER BY `rate` DESC"); $items[0]["id"]=0; $items[0]["pid"]=0; for ($i=1; $i<=$data["total"]; $i++):
		@mysql_data_seek($data["result"], ($i-1)); $ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"]; $items[$idr]["link"]=$ar["link"];
		$items[$idr]["class"]=$ar["class"]; endfor; GetChild(0, -1); $mtext="<div class='MenuDiv MenuDiv-".$am["link"]."' id='MenuDiv-".$am["link"]."'><ul class='MenuUl MenuUl-".$am["link"]."' id='MenuUl-".$am["link"]."'>".$itext."</ul></div>";
		$MENU[$am["link"]]=$mtext; #echo nl2br(htmlspecialchars($mtext))."<hr>"; echo $mtext."<hr>";
		// ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ----  ---- 
		$MENU[$am["link"]]=str_replace("[mdomain]", $VARS["mdomain"], $MENU[$am["link"]]); SetCache($file, $MENU[$am["link"]], $cap, $type); }
	}
	if ($am["link"]=="mainmenu"){ $MENU["secmainmenu"]=strip_tags($MENU[$am["link"]],"<div><a>"); }
	endfor;
}





function GetChild($i, $lvl=-1) { global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild($lvl, $i); }
foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild($key, $lvl); }}} } 

function HtmlChild($lvl, $idi) {
	global $items, $prid, $prpid, $prlvl, $ul, $r, $am; $pid=$items[$idi]["pid"]; if ($prlvl>$lvl) { for ($k=0; $k<($prlvl-$lvl); $k++) { $text.="<li class='liclose'></li></ul></li>"; }}
	#$text.="[ lvl=$lvl, pid=$pid, prid=$prid, prpid=$prpid, prlvl=$prlvl ]"; $sp="<img src='/admin/images/icons/sp.png' style='width:".($lvl*15)."px;' class='spacer' />";
	$rel=""; if (trim($items[$idi]["class"])=="nofollow") { $rel=' rel="nofollow"'; } if (trim($items[$idi]["class"])=="blank") { $rel=' target="_blank"'; } 
	$text.="<li class='Menu-li-".$am["link"]." ".$items[$idi]["class"]."' id='Menu-li-".$am["link"]."-".$idi."'><a href='".$items[$idi]["link"]."' title='".trim($items[$idi]["name"])."'".$rel." id='Menu-a-".$am["link"]."-".$idi."' class='menu-".$am["link"]."-level-".$lvl." a-".$items[$idi]["class"]."'>".trim($items[$idi]["name"])."</a>"; if (HaveChild($idi)==1) { $text.=$r."<ul>".$r; } else { $text.="</li> ".$r; } $prid=$idi; $prpid=$pid; $prlvl=$lvl; return $text;
}

function HaveChild($id) { global $items; foreach ($items as $key=>$item) {  if ($item["pid"]==$id) { return 1; }} return 0; }
?>