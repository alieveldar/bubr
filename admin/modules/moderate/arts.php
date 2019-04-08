<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="post_users"; $AdminRight="";	
	
	// ЭЛЕМЕНТЫ
	$AdminText.='<h2>Статьи авторов</h2>'; $orderby="ORDER BY `".$table."`.`data` DESC"; $text=""; $onpage=100; $from=($pg-1)*$onpage;
	$q="SELECT `".$table."`.*, `_users`.`nick` FROM `".$table."` LEFT JOIN `_users` ON `_users`.`id`=`".$table."`.`uid`".$orderby." LIMIT $from, $onpage"; $data=DB($q);

	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $datan=ToRusData($ar["data"]);
		if ($ar["stat"]==0) { $stat="<span style='color:#666;'>ЧЕРНОВИК</span>"; } 
		if ($ar["stat"]==1) { $stat="<span style='color:red;'>ОЖИДАЕТ</span>"; } 
		if ($ar["stat"]==2) { $stat="<span style='color:green;'>ОПУБЛИКОВАНО</span>"; } 
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'"><td class="CheckInput" style="white-space:nowrap; text-align:right;">'.$stat.'</td>';
		$text.="<td class='BigText'>".$ar["name"]."</div></td>";
		if ($ar["stat"]==0) { $text.='<td class="Act" id="Act'.$ar["id"].'"><a href="/users/preview/'.$ar["id"].'" target="_blank">Превью</a></td>'; }
		if ($ar["stat"]==1) { $text.='<td class="Act" id="Act'.$ar["id"].'"><a href="?cat='.$alias.'_moder&id='.$ar["id"].'" target="_blank">Модерация</a></td>'; }
		if ($ar["stat"]==2) { $text.='<td class="Act" id="Act'.$ar["id"].'"><a href="/post/view/'.$ar["lentaid"].'" target="_blank">Просмотр</a></td>'; }
		$text.='<td class="Act" width="1%" style="white-space:nowrap;" ><a href="?cat=moderate_author&id='.$ar["uid"].'">'.$ar["nick"].'</a></td>';
		$text.='<td class="Act" width="1%" style="white-space:nowrap;" >'.$datan[4].'</td>';
		$text.="</tr>";
	endfor;
	$AdminText.="<div class='RoundText' id='Tgg'><table id='Artss'>".$text."</table></div><style>#Artss td { padding:7px !important; }</style>";
	
	$data=DB("SELECT `id` FROM `".$table."`"); $AdminText.= Pager($pg, $onpage, ceil($data["total"]/$onpage));	
}

//=============================================
$_SESSION["Msg"]="";
?>