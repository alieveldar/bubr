<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

	// Настройки сайта из панели администрирования (stat = users-0, main-1, cache-2)
	$tmp=array(); $BasVars=array(); $data=DB("SELECT `name`,`value`, `stat`,`type` FROM `_settings`"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $key=$ar["name"];
	$VARS[$key]=$ar["value"]; $BasVARS[]=$ar; $tmp[]=$key; endfor; $tmp[]="socialgroups"; $VARS["socialgroups"]=SocialGroups(); $GLOBAL["log"].="<i>Настройки ".'$VARS[_name_]'."</i>: ".implode(', ', $tmp)." ".$query." - всего: <b>".sizeof($tmp)."</b><hr>"; $r="\r\n";

	// Серверные данные
	$GLOBAL["ip"] = $_SERVER["REMOTE_ADDR"];
	$GLOBAL["host"] = $_SERVER["SERVER_NAME"];
	$GLOBAL["page"] = trim($_SERVER["REQUEST_URI"], "/");
	$GLOBAL["browser"] = $_SERVER['HTTP_USER_AGENT'];
	$GLOBAL["tonight"] = (int)strtotime(date('Y-m-d'));
	$GLOBAL["userfrom"] = $_SERVER['HTTP_REFERER'];
	$GLOBAL["now"] = time();
	$ROOT=$_SERVER['DOCUMENT_ROOT'];

	// Настройки обработки фотографий ===============================
	$GLOBAL["pic"] = "img-".date("YmdHis")."-".round(rand(111,999));
	$GLOBAL["file"] = "file-".date("YmdHis")."-".round(rand(111,999));
	$GLOBAL['userfilesPath'] = $_SERVER['DOCUMENT_ROOT'].'/userfiles/';
	$GLOBAL['AutoPicPaths']=array(
		### Основные, не удалять!
		### "folder"=>"W-Y-[selectarea 1 or 0]"
		"picoriginal"=>$VARS["picoriginal"]."-0", 
		"picpreview"=>$VARS["picpreview"]."-0",
		"picsquare"=>$VARS["picsquare"]."-".$VARS["picsquare"]."-1",
		### Пользовательские
		### "folder"=>"W-Y-[selectarea 1 or 0]"
		"picbig"=>"970-320-1",
		"picmiddle"=>"470-300-1",
		"picright"=>"300-120-1",
		"picmail"=>"140-100-0",
	);
	
	// Запрещенные фразы в комментариях =============================
	/* слабые цепочки */ $StopWords=array("службы Путина", "базу данных", "открытом доступе", "о каждом из нас", "все и о каждом", "посмотрите если не верите", "удалить свои данные", "открытым доступом", "слили в сеть", "базу пользователей");
	/* строгий запрет */ $StopWords2=array("Ловев", "мудер", "хавно", "Лосев", "смотреть любую информацию", "слили всю базу", "хуй", "пизда", "пиздой", "пидор", "хуйн", "пизду", "уеб", "уёб", "блядь", "блять");
	/* проверка на спам */
	function checkSpam($t) {
		global $StopWords, $StopWords2; $cnt=0; $t=mb_strtolower(preg_replace('/[^a-zA-Zа-яА-ЯЁё\d]/u', '', $t), 'utf-8');
		foreach($StopWords as $word) { $word=mb_strtolower(preg_replace('/[^a-zA-Zа-яА-ЯЁё\d]/u', '', $word), 'utf-8'); $pos=mb_strpos($t, $word, 0, 'utf-8'); if ($pos!==false) { $cnt++; }}
		foreach($StopWords2 as $word) { $word=mb_strtolower(preg_replace('/[^a-zA-Zа-яА-ЯЁё\d]/u', '', $word), 'utf-8'); $pos=mb_strpos($t, $word, 0, 'utf-8'); if ($pos!==false) { $cnt=$cnt+10; }} 
		if ($_SESSION["userrole"]>0) { return(0); } else { return($cnt); }
	}
	// ==============================================================

	$GLOBAL["mothb"]=array("Месяц", "Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
	$GLOBAL["moths"]=array("месяц", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
	$GLOBAL["mothi"]=array("Месяц", "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
    $GLOBAL["rss_items_qty"]=array("1"=>1, "2"=>2, "3"=>3, "4"=>4, "5"=>5, "6"=>6, "7"=>7, "8"=>8, "9"=>9, "10"=>10, "15"=>15, "20"=>20, "25"=>25, "30"=>30);
    $GLOBAL["rss_views"]=array("Только заголовок", "Заголовок и тизер", "Полный текст");
	
    
	// Роли пользователей
    $GLOBAL["roles"] = array(0=>"Пользователь", 1=>"Модератор", 2=>"Редактор", 3=>"Главный редактор", 4=>"Администратор");	

	// Для удобства использования в модулях определяется в /template/standart/standart.php
	$C="<div class='C'></div>"; $C5="<div class='C5'></div>";$C10="<div class='C10'></div>"; $C15="<div class='C15'></div>"; $C20="<div class='C20'></div>"; $C25="<div class='C25'></div>"; $C30="<div class='C30'></div>";
	
	// Стандартные сортировки данных
	$ORDERS=array(
		0=>"",
		1=>"ORDER BY `id` DESC",
		2=>"ORDER BY `id` ASC",
		3=>"ORDER BY `data` DESC",
		4=>"ORDER BY `data` ASC",
		5=>"ORDER BY `rate` DESC",
		6=>"ORDER BY `rate` ASC",
		7=>"ORDER BY `name` DESC",
		8=>"ORDER BY `name` ASC",
	);
	$ORDERN=array(
		0=>"- Сортировка по умолчанию -",
		1=>"Сортировать по номеру, убывание (9-1)",
		2=>"Сортировать по номеру, возрастание (1-9)",
		3=>"Сортировать по дате, убывание (9-1)",
		4=>"Сортировать по дате, возрастание (1-9)",
		5=>"Сортировать по рейтингу, убывание (9-1)",
		6=>"Сортировать по рейтингу, возрастание (1-9)",
		7=>"Сортировать по названию, убывание (Я-А)",
		8=>"Сортировать по названию, возрастание (А-Я)",
	);
	
	// Знаки Зодиака
	$GLOBAL["zodiac"] = array(
		'aries' => array('name' => 'Овен', 'date' => '21.03 - 20.04'),
		'taurus' => array('name' => 'Телец', 'date' => '21.04 - 20.05'),
		'gemini' => array('name' => 'Близнецы', 'date' => '21.05 - 21.06'),
		'cancer' => array('name' => 'Рак', 'date' => '22.06 - 22.07'),
		'leo' => array('name' => 'Лев', 'date' => '23.07 - 23.08'),
		'virgo' => array('name' => 'Дева', 'date' => '24.08 - 23.09'),
		'libra' => array('name' => 'Весы', 'date' => '24.09 - 23.10'),
		'scorpio' => array('name' => 'Скорпион', 'date' => '24.10 - 22.11'),
		'sagittarius' => array('name' => 'Стрелец', 'date' => '23.11 - 21.12'),
		'capricorn' => array('name' => 'Козерог', 'date' => '22.12 - 20.01'),
		'aquarius' => array('name' => 'Водолей', 'date' => '21.01 - 20.02'),
		'pisces' => array('name' => 'Рыбы', 'date' => '21.02 - 20.03')
	);
}

//== ### Likes ===============================================================================================================================================
function Likes($LikeCAP, $LikeURL="", $LikePIC="", $LikeDESC="") { global $VARS, $RealPage, $RealHost; if ($LikeURL=="") { $LikeURL="http://".$RealHost ."/".$RealPage; } if ($LikePIC=="") { $LikePIC="http://".$RealHost ."/template/logo.png"; } if ($LikeDESC=="") { $LikeDESC=$VARS["sitename"]; } $LikeCAP=str_replace(array("'", '"', "&", "%", "?"), "", $LikeCAP); $LikeURL3=rawurlencode($LikeURL); $LikeCAP3=rawurlencode($LikeCAP); $LikeDESC3=rawurlencode(space($LikeDESC, 200)); $LikeDESC=str_replace("\r", "", $LikeDESC); $LikeDESC=str_replace("\n", "", $LikeDESC); 
$text='<script type="text/javascript">(function(w,doc) { if (!w.__utlWdgt ) { w.__utlWdgt=true; var d=doc, s=d.createElement("script"), g="getElementsByTagName"; s.type="text/javascript"; s.charset="UTF-8";
s.async=true; s.src=("https:"==w.location.protocol?"https":"http")+"://w.uptolike.com/widgets/v1/uptolike.js"; var h=d[g]("body")[0]; h.appendChild(s); }})(window,document);</script>
<div data-background-alpha="0.0" data-orientation="horizontal" data-text-color="000000" data-share-shape="rectangle" data-buttons-color="ff9300" data-sn-ids="vk.ok.fb.my.tw.mr.gp." data-counter-background-color="ffffff"
data-share-counter-size="11" data-share-size="30" data-background-color="ededed" data-top-button="false" data-share-counter-type="separate" data-pid="1262973" data-counter-background-alpha="1.0" data-share-style="1" data-mode="share"
data-following-enable="false" data-like-text-enable="false" data-selection-enable="false" data-icon-color="ffffff" class="uptolike-buttons" >
</div>'; return "<noindex>".$text."</noindex>"; }

function OLD_Likes($LikeCAP, $LikeURL="", $LikePIC="", $LikeDESC="") { global $VARS, $RealPage, $RealHost; if ($LikeURL=="") { $LikeURL="http://".$RealHost ."/".$RealPage; }
$text.='<div class="NewLike"><div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));</script>
<div class="fb-share-button" data-layout="button_count" data-href="'.$LikeURL.'"></div></div>';

$text.='<div class="NewLike"><script type="text/javascript" src="http://vk.com/js/api/share.js?90" charset="windows-1251"></script>
<script type="text/javascript">document.write(VK.Share.button(false,{type: "round", text: "Поделиться"}));</script></div>';

$text.='<div class="NewLike"><div id="ok_shareWidget"></div></div><script>
!function (d, id, did, st) {  var js = d.createElement("script");  js.src = "http://connect.ok.ru/connect.js";
  js.onload = js.onreadystatechange = function () {
  if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
    if (!this.executed) {
      this.executed = true;
      setTimeout(function () {
        OK.CONNECT.insertShareWidget(id,did,st);
      }, 0); }}};
d.documentElement.appendChild(js); }(document,"ok_shareWidget",document.URL,"{width:170,height:30,st:\'rounded\',sz:20,ck:3}");</script>';

$text.='<div class="NewLike"><a href="https://twitter.com/share" class="twitter-share-button">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script></div>';
	
return "<noindex>".$text."<div class='C10'></div></noindex>"; }



function space($text, $limit, $chars='UTF-8') { $text=strip_tags($text); if (!$firstSpaceAfterLimit = mb_strpos($text, '.', $limit, $chars)) { $firstSpaceAfterLimit = $limit; } return mb_substr($text, 0, $firstSpaceAfterLimit); }
//============================================================================================================================================================
function SocialGroups(){ global $BasVARS; $text=""; foreach($BasVARS as $k=>$val){ if ($val["stat"]==3 && $val["type"]=="var" && trim($val["value"])!="") { $text.="<li><a href='".$val["value"]."' rel='nofollow' target='_blank'><img src='/template/standart/socialgray/".$val["name"].".png' /></a></li>"; }} if ($text!="") { $text="<noindex><ul id='SocialsGroupsUL'>".$text."</ul></noindex>";} return $text; }
//============================================================================================================================================================
function CutEmptyTags($text){ $text=str_replace(array("\r", "\n", "\r\n", "\t", "<p>&nbsp;</p>", "<div>&nbsp;</div>", "<p></p>", "<div></div>"), "", $text); return $text; }
//============================================================================================================================================================

function CutText( $text, $symbols = 100){
	if(mb_strlen($text, 'utf-8') <= $symbols) 
	return $text; 
	
	$pos = mb_strpos( $text, ' ', $symbols, 'utf-8'); 
	return mb_substr( $text, 0, (int)$pos, 'utf-8').' ...'; 
} 

function Hsc($m) { $m=preg_replace('/"([^"]*)"/','&laquo;$1&raquo;', $m); return($m); }
function Replace_Data_Days($text) { $today=date("d.m.Y", time()); $yesday=date("d.m.Y", time()-24*60*60); $text=str_replace(array($today, $yesday), array("Cегодня", "Bчера"), $text); return $text; }
function ToRusData($var) { 
	global $GLOBAL; 
	$var = date("Y.m.d.H.i.s", $var); list($y, $m, $d, $h, $i, $s)=explode(".", $var);
	$data = array(
		0=>$d." ".$GLOBAL["mothb"][(int)$m]." ".$y.", ".$h.":".$i,
		1=>$d." ".$GLOBAL["moths"][(int)$m]." ".$y.", ".$h.":".$i,
		2=>($d+0)." ".$GLOBAL["mothb"][(int)$m]." ".$y,
		3=>$d." ".$GLOBAL["moths"][(int)$m]." ".$y,
		4=>$d.".".$m.".".$y.", ".$h.":".$i,
		5=>$d.".".$m.".".$y,
		6=>$d."/".$m."/".$y,
		7=>$h.":".$i,
		8=>$h.":".$i."<br>".$d.".".$m.".".$y,
		9=>"<b>".$d."</b><div>".$GLOBAL["moths"][(int)$m]."</div><i>".$y."</i>",
		10=>$d."/".$m,
	);
	return($data);
}
function ToRusDataAlt($var) {
	global $GLOBAL; list($y, $m, $d, $h, $i, $s)=explode(".", $var);
	$data = array(
		0=>$d." ".$GLOBAL["mothb"][(int)$m]." ".$y.", ".$h.":".$i,
		1=>$d." ".$GLOBAL["moths"][(int)$m]." ".$y.", ".$h.":".$i,
		2=>$d." ".$GLOBAL["mothb"][(int)$m]." ".$y,
		3=>$d." ".$GLOBAL["moths"][(int)$m]." ".$y,
		4=>$d.".".$m.".".$y.", ".$h.":".$i,
		5=>$d.".".$m.".".$y,
		6=>$d."/".$m."/".$y,
		7=>$h.":".$i,
		8=>$h.":".$i."<br>".$d.".".$m.".".$y,
		9=>"<b>".$d."</b><div>".$GLOBAL["moths"][(int)$m]."</div><i>".$y."</i>",
	);
	return($data);
}

function SD() { $_SESSION["Data"]=''; unset($_SESSION["Data"]); $_SESSION["Files"]=''; unset($_SESSION["Files"]); }
function THost($adres) { if (strpos($adres, "www.")===false) { $dop=$adres; } else { $at=explode("www.",$adres); $dop=$at[1]; } $adr="http://www.".$dop."/"; return $adr; }
function GetDomains() { $tmp=array(); $d=DB("SELECT `id`,`prefix` FROM `_domains`"); for($i=0; $i<$d["total"]; $i++): @mysql_data_seek($d["result"],$i); $ar=@mysql_fetch_array($d["result"]); $tmp[$ar["id"]]=$ar["prefix"]; endfor; return $tmp; }
function GetTagList($tags) { return $text; } function GetNormalProVideo($text) {  return GetNormalVideo($text); }

function GetNormalVideo($text) {
	 $text = preg_replace('~width="\d+"~', 'width="640"', $text); $text = preg_replace('~height="\d+"~', 'height="400"', $text);
	 return $text;
}
function GetMobileVideo($text) {
	 $text = preg_replace('~width="\d+"~', 'width="100%"', $text); $text = preg_replace('~height="\d+"~', 'height="150%"', $text);
	 return $text;
}


/* ДОЧЕРНИЕ СТРАНИЦЫ ДЛЯ СТАТИЧНЫХ СТРАНИЦ */
function ChildPages($id) { $file="child_pages-page".$id; if (RetCache($file)=="true") { list($text, $cap)=GetCache($file);	} else { list($text, $cap)=GetChildPages($id); SetCache($file, $text, 0); } return $text;}
function OutPutChild($lvl, $idi) { global $childitems; $text="<div class='ChildPages Level".$lvl."'><a href='/".trim($childitems[$idi]["link"])."'>".trim($childitems[$idi]["name"])."</a></div>"; return $text; }
function GetChildPages($id) {
	global $VARS, $childtext, $childitems; $childitems=array(); $data=DB("SELECT `id`,`pid`,`name`, `link` FROM `_pages` WHERE (`module`='' && `main`='0' && `stat`='1' && id>'$id') ORDER BY `rate` DESC");
	$childitems[0]["id"]=$id; $childitems[0]["pid"]=0; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1)); $ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $childitems[$idr]= $ar; endfor;
	FindChild(0); return array("<div class='ChildPages List'>".$childtext."</div>", ""); 
} 
function FindChild($i, $lvl=-1) {
	global $childtext, $childitems, $mvl; if ($i!=0) { $childtext.=OutPutChild($lvl, $i); } foreach ($childitems as $key=>$item) {
	if ($item["pid"]==$childitems[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { FindChild($key, $lvl); }}}
}

/* пагеры для страниц и модулей*/
function Pager($pg, $limit=30, $total, $pagesInPager=20) { global $cat, $cid, $pid, $pg, $id, $it; $a="?cat=$cat&id=$id&pid=$pid&cid=$cid&it=$it&pg="; 
if($total > 1) { $text .= '<div class="pager">'; $pstart=$pg > $pagesInPager - 3 ? $pg + 4 - $pagesInPager : 1; for ($i=$pstart; $i <= $total; $i++) {
if($i > $pg + 1 && $i > $pagesInPager - 2 && $i < $total - 1) { continue; } else if($i == $total - 1 && $total > $pagesInPager && $total - $pstart > $pagesInPager - 1) { $text .= '<span>...</span>'; } else {
if($i == $pg) { $text .= '<a href="'.$a.$i.'" class="active">'.$i.'</a>'; }	else { $text .= '<a href="'.$a.$i.'">'.$i.'</a>'; }}} $text .= '</div>'; } return $text; }

function Pager2($pg, $limit=30, $total, $txt, $pagesInPager=13) { if($total > 1) { $text .= '<div class="pager">'; $pstart=$pg > $pagesInPager - 3 ? $pg + 4 - $pagesInPager : 1; for ($i=$pstart; $i <= $total; $i++) {
if ($i > $pg + 1 && $i > $pagesInPager - 2 && $i < $total - 1) { continue; } else if($i == $total - 1 && $total > $pagesInPager && $total - $pstart > $pagesInPager - 1) { $text .= '<span>...</span>'; }
else{ $href=str_replace("[page]", $i, $txt); if($i == $pg) { $text .= '<a href="/'.$href.'/" class="active">'.$i.'</a>'; } else { $text .= '<a href="/'.$href.'/">'.$i.'</a>'; }}} $text.='</div>'; } return $text; }

?>