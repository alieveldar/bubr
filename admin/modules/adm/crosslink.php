<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

	$table2="_crosslink";

	// ЭЛЕМЕНТЫ
	$AdminText.='<h2 style="float:left;">Контекстные ссылки сайта</h2><div style="float:right;" class="LinkG"><a href="javascript:void(0);" onclick="AddNewCross(\''.(int)$id.'\', \'0\');">Добавить ссылку</a></div>'
	.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете изменять контекстные ссылки</div>";
	
	$data=DB("SELECT * FROM `".$table2."` ORDER BY `name` ASC"); $text="";
	for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1)); $ar=@mysql_fetch_array($data["result"]); 
		$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';	
		$text.="<td class='BigText'><a href='".$ar["link"]."' target='_blank'>".$ar["name"]."</a> <i>".$ar["link"]."</i></td>";
		$edit="ItemEdit('".$ar["name"]."', '".$ar["link"]."', '".$ar["id"]."')";
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act"> </td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor; $AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight=ATextReplace('Crosslink-Module', $menu["link"]);
	
}

//=============================================
$_SESSION["Msg"]="";
?>