<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg;
	$table=$alias."_queries";
	
	// ЭЛЕМЕНТЫ
	$data=DB("SELECT name FROM `".$alias."_lenta` WHERE (`id`='".$id."')"); 
	@mysql_data_seek($data["result"], 0);
	$raz=@mysql_fetch_array($data["result"]);
	$AdminText.='
	<h2 style="float:left;">'.$raz["name"].': Список вопросов </h2> 
	<div style="float:right;" class="LinkG"><a href="?cat='.$alias.'_addquery&id='.$id.'">Добавить вопрос</a></div>'.$C5.
	"<div id='Msg2' class='InfoDiv'>Вы можете редактировать и удалять записи</div>";
	
	$data=DB("SELECT * FROM `".$table."` ORDER BY `rate` ASC"); $text="";
	    for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i);
     	$ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } 
		else { $chk=""; } 
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';			
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';		
		$text.="<td class='BigText'><a href='?cat=".$alias."/".$ar["id"]."' target='_blank'>".$ar["name"]."</a> <i>$ar[catn]</i></td>";	
		
		
		///echo $ar["id"];
		//$edit="ItemEdit('".$ar["id"]."', '".$table."', '".$ar["name"]."');";	
		///$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править вопрос">'.AIco('28').'</a></td>';
		
		
		$text.='<td class="Act"><a href="?cat='.$alias.'_aedit&id='.$ar["id"].'" title="Править ответы">'.AIco('54').'</a> </td>';
		$text.='<td class="Act"><a href="?cat='.$alias.'_qedit&id='.$ar["id"].'" title="Править вопрос">'.AIco('28').'</a> </td>';
		
		
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\', \''.$table.'\', \'5\')" title="Поднять">'.AIco(3).'</a></td>
		<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\', \''.$table.'\', \'5\')" title="Опустить">'.AIco(4).'</a></td>';
		$text.='<td class="Act"> </td>';
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.='<td class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
   	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные данные</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_sets&id=".$id."'>Настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	
	<div class='SecondMenu2'><a href='?cat=".$alias."_voting&id=".$id."'>Вопросы и ответы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_results&id=".$id."'>Результаты</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	$AdminRight.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></form>";
	
}

//=============================================
$_SESSION["Msg"]="";
?>