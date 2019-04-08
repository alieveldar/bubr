<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="_usersmess"; $onpage=100; $from=($pg-1)*$onpage; $AdminRight="";
	// ЭЛЕМЕНТЫ
	$q="SELECT MAX(`".$table."`.`data`) as `data`, `_users`.`nick`, `".$table."`.`withuid`, COUNT(`".$table."`.`id`) as `cnt` FROM `".$table."` LEFT JOIN `_users` ON `_users`.`id`=`".$table."`.`withuid` GROUP BY `".$table."`.`withuid` ORDER BY `data` DESC LIMIT $from, $onpage";
	$AdminText.='<h2 style="float:left;">Архив сообщений пользователей</h2>';
	$data=DB($q); $text=""; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $datan=ToRusData($ar["data"]);
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';				
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:11px;" >'.$datan[4].'</td>';
		$text.="<td class='BigText'><a href='?cat=".$alias."_read&id=".$ar["withuid"]."' target='_blank'>".$ar["nick"]."</a></div></td>";
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:11px;" ><a href="?cat='.$alias.'_read&id='.$ar["withuid"].'">Сообщений</a>: '.$ar["cnt"].'</td>';
		$text.="</tr>";
	endfor;
	$AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";
	$data=DB("SELECT `id` FROM `".$table."` GROUP BY `withuid`"); $AdminText.= Pager($pg, $onpage, ceil($data["total"]/$onpage));
}

//=============================================
$_SESSION["Msg"]="";
?>