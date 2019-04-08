<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="post_users"; $AdminRight="";
	
	// ЭЛЕМЕНТЫ
	$AdminText.='<h2>Список авторов</h2>'; $onpage=50; $from=($pg-1)*$onpage; $text="";	
	$q="SELECT `_userspays`.`uid`,SUM(`_userspays`.`money`) as `s`, `_users`.`nick` FROM `_userspays` LEFT JOIN `_users` ON `_users`.`id`=`_userspays`.`uid` GROUP BY `_userspays`.`uid` ORDER BY `nick` ASC LIMIT $from, $onpage"; $data=DB($q);
	
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $datan=ToRusData($ar["data"]);
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';
		$text.='<td><a href="?cat='.$alias.'_author&id='.$ar["uid"].'" target="_blank">'.$ar["nick"].'</a></td>';
		$text.='<td class="Act" width="1%" style="white-space:nowrap;" >Баланс: <b>'.$ar["s"].'</b> рублей</td>';
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="?cat='.$alias.'_author&id='.$ar["uid"].'" target="_blank">Просмотр</a></td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
	
	$data=DB("SELECT `uid` FROM `_userspays` GROUP BY `_userspays`.`uid`"); $AdminText.= Pager($pg, $onpage, ceil($data["total"]/$onpage));


}

//=============================================
$_SESSION["Msg"]="";
?>