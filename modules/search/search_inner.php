<?
if (isset($Data["searchword"])) { $w=$Data["searchword"]; SD(); @header("location: /search/".rawurlencode($w)); exit(); } 
if ((time()-$_SESSION["searchtime"])>10) { $_SESSION["searchtime"]=time();
	
mb_internal_encoding("UTF-8"); $table='_search'; $word=vcut(rawurldecode($start)); $words=explode(' ', $word); $pidc=array(); $res=array(); $limitp=$node["onpage"]; ### ЛИМИТ
if ($word=="") { $Page["Content"]="<h2>Задан пустой поисковый запрос</h2>"; } elseif (mb_strlen($word)<3) { $Page["Content"]="<h2>Строка поиска должна содержать минимум 3 символа</h2>"; } else {
	

### ИЩЕМ ПОЛНОЕ СОВПАДЕНИЕ В ЗАГОЛОВКЕ - 2000 очков
$recs=DB("SELECT `id` FROM `$table` WHERE (`name`='$word') ORDER BY `data` DESC LIMIT $limitp"); while ($rec=mysql_fetch_array($recs["result"])) { $res[$rec["id"]]+=1000; }
### ИЩЕМ ПОЛНОЕ СОВПАДЕНИЕ В ТЕКСТЕ - 1000 очков
$recs=DB("SELECT `id` FROM `$table` WHERE (`text`='$word') ORDER BY `data` DESC LIMIT $limitp"); while ($rec=mysql_fetch_array($recs["result"])) { $res[$rec["id"]]+=1000; }
### ИЩЕМ ВХОДЯЩЕЕ СОВПАДЕНИЕ В ЗАГОЛОВКЕ - 500 очков
$recs=DB("SELECT `id` FROM `$table` WHERE (`name` LIKE '%".$word."%') ORDER BY `data` DESC LIMIT $limitp"); while ($rec=mysql_fetch_array($recs["result"])) { $res[$rec["id"]]+=500; }
### ИЩЕМ ВХОДЯЩЕЕ СОВПАДЕНИЕ В ТЕКСТЕ - 100 очков
$recs=DB("SELECT `id` FROM `$table` WHERE (`text` LIKE '%".$word."%') ORDER BY `data` DESC LIMIT $limitp"); while ($rec=mysql_fetch_array($recs["result"])) { $res[$rec["id"]]+=100; }
### ИЩЕМ ОВПАДЕНИЯ ПО КАЖДОМУ СЛОВУ - 10 очков
foreach ($words as $item) { if (mb_strlen($item)>2) { $recs=DB("SELECT `id` FROM `$table` WHERE (`name` LIKE '%".$item."%' || `text` LIKE '%".$item."%') ORDER BY `data` DESC LIMIT $limitp"); while ($rec=mysql_fetch_array($recs["result"])) { $res[$rec["id"]]+=10; }}}
### ИЩЕМ ОВПАДЕНИЯ ПО КАЖДОМУ СЛОВУ, УДАЛИВ ОКОНЧАНИЕ - 1 очко
foreach ($words as $item) { $len=mb_strlen($item); $nlen=$len-round($len/4); $item=mb_substr($item, 0, $nlen); if (mb_strlen($item)>2) { $recs=DB("SELECT `id` FROM `$table` WHERE (`name` LIKE '%".$item."%' || `text` LIKE '%".$item."%') ORDER BY `data` DESC LIMIT $limitp"); while ($rec=mysql_fetch_array($recs["result"])) { $res[$rec["id"]]+=1; }}}


### СОРТИРУЕМ РЕЗУЛЬТАТЫ И ЗАПРАШИВАЕМ ОСТАЛЬНУЮ ИНФУ
arsort($res); $total=0; foreach ($res as $key=>$value) { $pidc[]=$key; $total++; if ($total==100) { break; }} $ids=implode(",", $pidc);
if (count($pidc)==0) {  $Page["Content"]="<h3>К нашему величайшему сожалению, ничего не найдено :(</h3>"; } else {

	### ФОРМИРУЕМ РЕЗУЛЬТАТЫ
	$recs=DB("SELECT `id`, `name`, `data`, `link`, `text` FROM `$table` WHERE (`id` IN ($ids))");
	while ($rec=mysql_fetch_array($recs["result"])) { $pid=$rec["id"]; $arr[$pid]["id"]=$rec["id"]; $arr[$pid]["link"]=$rec["link"]; $arr[$pid]["data"]=$rec["data"]; $arr[$pid]["name"]=$rec["name"]; $arr[$pid]["text"]=$rec["text"]; $arr[$pid]["point"]=$res[$pid];  }	
	$point=array(); foreach ($arr as $key=>$row) { $point[$key] = $row['point']; } array_multisort($point, SORT_DESC, $arr);
	
	$Text.='<table width="100%" border="0" cellpadding="2" cellspacing="0">';
	foreach ($arr as $id=>$row) { if ($row["point"]>=1) { $Ftotal++; $pid=$row["id"]; $d=ToRusData($row["data"]); $pretext=$row["text"];  $prename=$row["name"];
		foreach ($words as $item) { $prename=str_replace($item, "<o>".$item."</o>", $prename); $prename=str_replace(ucfirst_utf8($item), "<o>".ucfirst_utf8($item)."</o>", $prename); $prename=str_replace(mb_strtoupper($item), "<o>".mb_strtoupper($item)."</o>", $prename); }
		if (mb_strlen($row["text"])>100) { foreach ($words as $item) { $pretext=HightLightText($pretext, $item); } $pretext="<div class='C5'></div>...".$pretext."</o>...<div class='C5'></div>"; } else { $pretext=""; }
		$Text.="<tr><td width='1%' style='color:#CCC; line-height:20px; font-size:20px; padding-top:3px;' valign='top' align='center'><b>".$Ftotal."</b>.</td>
		<td width=99% style='line-height:20px;'><a href='/".$row["link"]."/view/".$pid."/' style='font-size:15px;'><u>".$prename."</u></a><div class='C'></div>
		<div style='font-size:11px; color:#888; line-height:13px;'>".$pretext."Дата изменения: <b>".$d[5]."</b>, релевантность: $row[point]</div><div class='C10'></div><div class='CB'></div><div class='C10'></div></td></tr>";
	}} $Text.="</table>";

	if ($Ftotal==0) { $Page["Content"]="<h3>К нашему величайшему сожалению, ничего не найдено :(</h3>"; }}
	
}} else { $Text="<h3>Вы отправляете запросы слишком часто.</h3>"; }



$Page["Content"]="<div class='SearchPage'>".$Text."</div>"; $Page["Caption"]=$node["name"].": ".ucfirst($word);

function HightLightText($textr, $item) {
	$st=0;
	if (mb_strpos($textr, $item)!==false) { $st=mb_strpos($textr, $item)-50; if ($st<0) { $st=0; }}
	$textr=str_replace($item, "<o>".$item."</o>", $textr);
	$textr=mb_substr($textr, $st, 250);
return $textr;
}

function ucfirst_utf8($str) { return mb_substr(mb_strtoupper($str, 'utf-8'), 0, 1, 'utf-8') . mb_substr($str, 1, mb_strlen($str)-1, 'utf-8'); }
function vcut($value) { $ar=array("|", '&&', "=", "<>", "==", "!=", "(", ")"); $value=trim($value); $value=str_replace('http://', '', $value); $value=strip_tags($value); $value=str_replace($ar,"",$value); return mb_strtolower($value,'UTF-8'); }
function strtolow($text) { $alfavitlover = array('ё','й','ц','у','к','е','н','г', 'ш','щ','з','х','ъ','ф','ы','в', 'а','п','р','о','л','д','ж','э', 'я','ч','с','м','и','т','ь','б','ю'); $alfavitupper = array('Ё','Й','Ц','У','К','Е','Н','Г', 'Ш','Щ','З','Х','Ъ','Ф','Ы','В', 'А','П','Р','О','Л','Д','Ж','Э', 'Я','Ч','С','М','И','Т','Ь','Б','Ю'); return str_replace($alfavitupper,$alfavitlover,mb_strtolower($text)); }
?>