<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table=$alias."_imports";
	
	// ЭЛЕМЕНТЫ
	$AdminText.='<h2 style="float:left;">Импорт материалов с других сайтов системы ProCMS</h2><div class="LinkG" style="float:right;"><a href="?cat='.$alias.'_add">Добавить импорт</a></div>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете редактировать и удалять записи</div>";

	$data=DB("SELECT * FROM `$table` ORDER BY `site` ASC"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $datan=ToRusData($ar["last"]);
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';				
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';		
		$text.="<td class='BigText'><a href='/".$alias."/view/".$ar["id"]."' target='_blank'>".$ar["name"]."</a> <i>$ar[catn]</i></td>";	
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px;" ><i>'.$datan[4].'</i></td>';
		$text.='<td class="Act"><a href="?cat='.$alias.'_edit&id='.$ar["id"].'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor;
	$AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='C20'></div>В данном разделе сайта вы можете выбрать сайты и ленты откуда будут экспортироваться материалы, а так же назначить существующие ленты, куда будет производиться импорт."; 
	$AdminRight.="<div class='C20'></div>Внимание! Версии модулей 'lenta' а так же наборы полей в таблицах БД должны совпадать.";
}

//=============================================
$_SESSION["Msg"]="";
?>