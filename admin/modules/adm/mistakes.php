<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg;
	$table="_mistakes";
	$limit=50;
	$from=($pg - 1) * $limit;		
	
	// ЭЛЕМЕНТЫ
	$AdminText.='<h2 style="float:left;">Уведомления об ошибках</h2>'
	.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете просматривать и удалять ошибки</div>";
	
	$data=DB("SELECT * FROM `".$table."` ORDER BY `data` DESC LIMIT $from, $limit"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $datan=ToRusData($ar["data"]);
		$dir=explode("/", str_replace(array('http://', 'www.'), '', $ar["link"]));
		$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';			
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';		
		$text.="<td class='BigText'><a href='".$ar["link"]."' target='_blank'>".$ar["link"]."</a></td>";
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px;" ><i>'.$datan[4].'</i></td>';
		$text.='<td class="Act"><a href="?cat=adm_mistakesshow&id='.$ar["id"].'">'.AIco('49').'</a></td>';
		$text.='<td class="Act"><a href="?cat='.$dir[1].'_text&id='.$dir[3].'" title="Редактировать материал">'.AIco('28').'</a></td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete('.$ar["id"].', \''.$pg.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor; $AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";

	$data=DB("SELECT `id` FROM `".$table."`"); $total=ceil($data["total"] / $limit); $AdminText.= Pager($pg, $limit, $total);
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='C20'></div>В данном списке отображаются ошибки, замеченные пользователями сайта. Здесь можно просмотреть текст ошибки, удалить ошибку или перейти к редактированию страницы с ошибкой.<div class='C20'></div>
	<div class='SecondMenu'><a href='?cat=adm_razdelsets&id=2'>Настройки раздела</a></div>";
	
}

//=============================================
$_SESSION["Msg"]="";
?>