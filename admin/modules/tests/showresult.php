<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg;
	$table=$alias."_results";
	
	// ЭЛЕМЕНТЫ
	$data=DB("SELECT name FROM `".$alias."_lenta` WHERE (`id`='".$id."')"); 
	@mysql_data_seek($data["result"], 0);
	$raz=@mysql_fetch_array($data["result"]);
	$AdminText.='
	<h2 style="float:left;">'.$raz["name"].': Список ответов </h2> 
	'.$C5;
	
	//$data=DB("SELECT * FROM `".$table."` ORDER BY 1 ASC");
    $idd = (int)$G["id"]; 	
	$data = DB("select resultss.* from ".$table."  resultss where id=".$idd);
	

	$text="";
	 for ($i=0; $i<$data["total"]; $i++): 
		@mysql_data_seek($data["result"], $i);
        $ar=@mysql_fetch_array($data["result"]);
		
		
	    $text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';			
					
		$text.="<td class='BigText'>".$ar["text"]. "</td>";
		$text.='<td class="Act"> </td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
   	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные данные</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_sets&id=".$id."'>Настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Вопросы и ответы</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_results&id=".$id."'>Результаты</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	$AdminRight.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></form>";
	
}

//=============================================
$_SESSION["Msg"]="";
?>