<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

	$table="_widget_eventtype";

	// ЭЛЕМЕНТЫ	
	$AdminText.='<h2 style="float:left;">Список типов событий</h2><div style="float:right;" class="LinkG"><a href="javascript:void(0);" onclick="AddNewTask(\''.(int)$id.'\');">Добавить тип</a></div>'
	.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете изменять типы событий</div>";
	
	$data=DB("SELECT * FROM `".$table."` ORDER BY `rate` DESC"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $edit="ItemEdit( '".$ar["id"]."', '".$ar["name"]."', '".$ar["pic"]."')";
			$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';	
			$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';
			$text.="<td class='BigText'>".$ar["name"]."</td>";
			if ($i!=0) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\')" title="Поднять">'.AIco('3').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
			if ($i<($data["total"]-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\')" title="Опустить">'.AIco('4').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
			$text.='<td class="Act" width="1%"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
			$text.='<td class="Act" width="1%"> </td>';
			$text.='<td class="Act" width="1%"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\')" title="Удалить">'.AIco('exit').'</a></td>';
			$text.="</tr>";
	endfor; $AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$data=DB("SELECT `link` FROM `_pages` WHERE (`module`='eventmap')"); @mysql_data_seek($data["result"], 0); $page=@mysql_fetch_array($data["result"]); /* ссылка на сайте */
	$AdminRight=$C20.$C10."<div class='SecondMenu'><a href='?cat=adm_eventmap'>Список событий карты</a></div>$C5<div class='SecondMenu'><a href='?cat=adm_eventmapsets'>Настройки «Карты событий»</a></div><div class='SecondMenu'><a href='/".$page["link"]."' target='_blank'>Просмотр «Карты событий»</a></div>";
	
}

//=============================================
$_SESSION["Msg"]="";
?>