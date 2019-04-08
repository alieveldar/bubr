<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg;
	$table=$alias."_cats";
	
	// ЭЛЕМЕНТЫ
	$data=DB("SELECT name FROM `_pages` WHERE (`link`='".$alias."')"); @mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	$AdminText.='
	<h2 style="float:left;">'.$raz["name"].': категории раздела</h2>
	<div style="float:right;" class="LinkG"><a href="javascript:void(0);" onclick="AddNewCat(\''.$table.'\');">Добавить категорию</a></div>'.$C5.
	"<div id='Msg2' class='InfoDiv'>Вы можете редактировать и удалять записи</div>";
	
	$data=DB("SELECT * FROM `".$table."` ORDER BY `rate` DESC"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } 
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';			
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';		
		$text.="<td class='BigText'><a href='?cat=".$alias."_list&cid=".$ar["id"]."'>".$ar["name"]."</a> <i>$ar[catn]</i></td>";	
		$edit="ItemEdit('".$ar["id"]."', '".$table."', '".$ar["name"]."', '".$ar["email"]."');";	
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\', \''.$table.'\', \'5\')" title="Поднять">'.AIco(3).'</a></td>
		<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\', \''.$table.'\', \'5\')" title="Опустить">'.AIco(4).'</a></td>';
		$text.='<td class="Act"> </td>';
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="";
	
}

//=============================================
$_SESSION["Msg"]="";
?>