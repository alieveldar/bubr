<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $text=""; $module="eventmap"; $table="_widget_eventmap"; $table2="_widget_eventtype"; $onpage=100; $from=($pg-1)*$onpage; $orderby=" ORDER BY `".$table."`.`data` DESC";
	$data=DB("SELECT `link`, `stat` FROM `_pages` WHERE (`module`='$module')"); @mysql_data_seek($data["result"], 0); $page=@mysql_fetch_array($data["result"]); /* ссылка на сайте */
	
	
	// ТИПЫ СОБЫТИЙ
	$types=""; $type=array(); $data=DB("SELECT `id`, `name`, `stat` FROM `".$table2."` order by `rate` DESC"); if ($data["total"]==0) { $types="<tr><td>Нет типов событий</td></tr>";  } else {
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $type[$ar["id"]]=$ar;
	$types.="<tr class='TRLine TRLine".($i%2)."'><td class='CheckInput'><input type='checkbox' id='RS-".$ar["id"]."-".$table2."' ".$chk."></td><td>".$ar["name"]."</td></tr>"; endfor; }
	
	// СПИСОК СОБЫТИЙ
	$AdminText='<h2 style="float:left;">Список событий на карте</h2>'."<div class='LinkG' style='float:right;'><a href='?cat=adm_eventmapadd'>Добавить событие</a></div>".$C5.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете редактировать и удалять записи</div>";
	$data=DB("SELECT `".$table."`.`name`, `".$table."`.`id`, `".$table."`.`stat`, `".$table."`.`data`, `".$table."`.`pid`, `".$table."`.`link`, `".$table2."`.`name` as `tname` FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`id`=`".$table."`.`tid` ORDER BY `".$table."`.`id` DESC LIMIT $from, $onpage"); 
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $d=ToRusData($ar["data"]); 
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';			
			$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';
			$text.="<td class='BigText'><a href='/".$module."/view/$ar[id]' target='_blank'>".$ar["name"]."</a>$C5<i style='margin:0;'>Тип события: $ar[tname]</i></td>";
			$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px;" ><i>'.$d[4].'</i></td>';
		$text.='<td class="Act"><a href="?cat=adm_eventmapedit&id='.$ar["id"].'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.='<td class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>"; 
	$data=DB("SELECT `id` FROM `".$table."`"); $AdminText.= Pager($pg, $onpage, ceil($data["total"]/$onpage));

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='C10'></div><h2>Типы событий</h2><div class='SecondMenu'><a href='?cat=adm_eventmaptype'>Редактировать список</a></div>$C5<div class='RoundText' id='Tgg'><table>".$types."</table></div>";
	$AdminRight.=$C20."<div class='SecondMenu'><a href='?cat=adm_eventmapsets'>Настройки «Карты событий»</a></div><div class='SecondMenu'><a href='/".$page["link"]."' target='_blank'>Просмотр «Карты событий»</a></div>";
	
}
//=============================================
$_SESSION["Msg"]="";
?>