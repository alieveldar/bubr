<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

	$table="_cron";

	// ЭЛЕМЕНТЫ
	$AdminText.='<h2 style="float:left;">Задания по расписанию</h2><div style="float:right;" class="LinkG"><a href="javascript:void(0);" onclick="AddNewTask(\''.(int)$id.'\', \'0\');">Добавить задание</a></div>'
	.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете изменять файлы для запуска и время запуска заданий</div>";
	
	$data=DB("SELECT * FROM `".$table."` ORDER BY `rate` DESC"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; }
		$info='ItemInfo(\''.$ar["name"].'\', \''.date("d.m.Y H:i:s", $ar["lasttime"]).'\', \''.htmlspecialchars($ar["log"], ENT_QUOTES).'\');';
		$edit="ItemEdit( '".$ar["id"]."', '".$ar["name"]."', '".$ar["link"]."', '".$ar["runtime"]."')";
		$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';	
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';
		$text.="<td class='BigText'><a href='".$ar["link"]."' target='_blank'>".$ar["name"]."</a> <i>".$ar["link"]."</i></td>";
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$info.'" title="Информация">'.AIco('49').'</a></td>';		
		if ($i!=0) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\')" title="Поднять">'.AIco('3').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
		if ($i<($data["total"]-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\')" title="Опустить">'.AIco('4').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act"> </td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor; $AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight=ATextReplace('Cron-Module', $menu["link"])."<div class='C30'></div><div class='LinkR'><a target='_blank' href='/modules/Cron.php'>Запустить CRON сейчас</a></div>";
	
}

//=============================================
$_SESSION["Msg"]="";
?>