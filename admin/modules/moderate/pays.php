<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="_usersmess"; $onpage=100; $from=($pg-1)*$onpage; $AdminRight="";
	// ЭЛЕМЕНТЫ
	$q="SELECT `_userspays`.*, `_users`.`nick` FROM `_userspays` LEFT JOIN `_users` ON `_users`.`id`=`_userspays`.`uid` WHERE (`_userspays`.`money`<0 && `_userspays`.`pid`='0') ORDER BY `_userspays`.`data` ASC LIMIT $from, $onpage";
	
	$AdminText.='<h2 style="float:left;">Заявки на вывод денег</h2>';
	$data=DB($q); $text=""; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $datan=ToRusData($ar["data"]);
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';				
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:11px;" >'.$datan[4].'</td>';
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:11px;" ><a href="?cat='.$alias.'_pay&id='.$ar["id"].'">Сумма вывода</a>: <b>'.(0-$ar["money"]).'</b></td>';
		$text.="<td class='BigText'>Страница автора: <a href='?cat=".$alias."_author&id=".$ar["uid"]."' target='_blank'>".$ar["nick"]."</a></div></td>";
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:11px;" ><a href="?cat='.$alias.'_pay&id='.$ar["id"].'">Обработать заявку</a></td>';		
		$text.="</tr>";
	endfor;
	$AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";
	$data=DB("SELECT `id` FROM `_userspays` WHERE (`money`<0 && `pid`='0')"); $AdminText.= Pager($pg, $onpage, ceil($data["total"]/$onpage));
}

//=============================================
$_SESSION["Msg"]="";
?>