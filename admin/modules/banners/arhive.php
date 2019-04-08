<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg;
	$table="_banners_items";
	$table2="_banners_pos";
	$onpage=100;
	$from=($pg-1)*$onpage;
	$orderby=" ORDER BY `".$table."`.`zid` DESC";
	if (!is_file("banners-sets.dat")) { $AdminText="Не найден файл &laquo;<b>/admin/banners-sets.dat</b>&raquo; создайте его вручную и поставьте права 0777"; $GLOBAL["error"]=1; } else {
	$sets=explode("|", @file_get_contents("banners-sets.dat")); $table3=$sets[0]."_items";
	
	// ЭЛЕМЕНТЫ
	$types=""; $type=array(); $data=DB("SELECT `id`, `name`, `stat` FROM `".$table2."` order by `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i);
	$ar=@mysql_fetch_array($data["result"]); if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $type[$ar["id"]]=$ar;
	$types.="<tr class='TRLine TRLine".($i%2)."'><td class='CheckInput'><input type='checkbox' id='RS-".$ar["id"]."-".$table2."' ".$chk."></td><td>".$ar["name"]."</td></tr>"; endfor;
	
	
	$AdminText='<h2 style="float:left;">Список рекламных материалов [Архив]</h2>'."<div class='LinkG' style='float:right;'><a href='?cat=banners_add'>Добавить баннер</a></div>".$C5.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете редактировать и удалять записи</div>";
	$data=DB("SELECT `".$table."`.*, `".$table3."`.`name` as compname, `".$table2."`.`name` as `place`, `".$table2."`.`width` as w, `".$table2."`.`height` as h 
	FROM `".$table."`
	LEFT JOIN `".$table3."` ON `".$table3."`.`id`=`".$table."`.`cid`
	LEFT JOIN `".$table2."` ON `".$table2."`.`id`=`".$table."`.`pid`
	WHERE (`".$table."`.`stat`!='1') ".$orderby." LIMIT $from, $onpage"); $text="";
	
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $d1=ToRusData($ar["datafrom"]); $d2=ToRusData($ar["datato"]);
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';			
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';
		$text.="<td class='BigText' width='1%' title='Номер баннера' align='center'><span style='color:#999;font-size:10px;'>BID</span><br>".$ar["id"]."</td>";
		$text.="<td class='BigText' width='1%' title='Номер заявки' align='center'><span style='color:#999;font-size:10px;'>ZID</span><br>".$ar["zid"]."</td>";
		$text.='<td class="Act"><a href="?cat='.$alias.'_info&id='.$ar["id"].'" title="Статистика">'.AIco('53').'</a></td>';		
		$text.="<td class='BigText'><a href='/advert/preBanner.php?id=$ar[id]' target='_blank'>".$ar["name"]."</a>$C5<i style='margin:0;'><a href='?cat=".$sets[0]."_edit&id=$ar[cid]'>$ar[compname]</a>, $ar[place] - ".$ar['w']."x".$ar['h']."</i></td>";
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px;" ><i>'.$d1[5].'<br>'.$d2[5].'</i></td>';
		$text.='<td class="Act"><a href="?cat='.$alias.'_edit&id='.$ar["id"].'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";
	$data=DB("SELECT `id` FROM `".$table."`"); $AdminText.= Pager($pg, $onpage, ceil($data["total"]/$onpage));
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='C20'></div><h2>Типы баннеров</h2>";
	$AdminRight.="<div class='RoundText' id='Tgg'><table>".$types."</table></div>".$C10."<div class='LinkR' align='center'><a href='?cat=".$alias."_type'>Редактировать список</a></div>";
	
}}

//=============================================
$_SESSION["Msg"]="";
?>