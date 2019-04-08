<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="post_users"; $AdminRight="";
	
	// ЭЛЕМЕНТЫ
	$AdminText.='<h2>Статьи народных авторов</h2>'; $orderby="ORDER BY `".$table."`.`data` ASC"; $text="";
	$q="SELECT `".$table."`.*, `_users`.`nick` FROM `".$table."` LEFT JOIN `_users` ON `_users`.`id`=`".$table."`.`uid` WHERE (`".$table."`.`stat`=1)".$orderby; $data=DB($q);
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		$datan=ToRusData($ar["data"]); $stat="<span><b style='color:red;'>ОЖИДАЕТ</b></span>";
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'"><td class="CheckInput" style="white-space:nowrap;">'.$stat.'</td>';
		$text.="<td class='BigText'><a href='?cat=moderate_moder&id=".$ar["id"]."' target='_blank'>".$ar["name"]."</a></div></td>";
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="?cat='.$alias.'_moder&id='.$ar["id"].'" target="_blank">Модерация</a></td>';
		$text.='<td class="Act" width="1%" style="white-space:nowrap;" ><a href="?cat=moderate_author&id='.$ar["uid"].'">'.$ar["nick"].'</a></td>';
		$text.='<td class="Act" width="1%" style="white-space:nowrap;" >'.$datan[4].'</td>';
		$text.="</tr>";
	endfor;
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
}

//=============================================
$_SESSION["Msg"]="";
?>