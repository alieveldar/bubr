<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $table="post_users"; $AdminRight="";
	
	// ЭЛЕМЕНТЫ
	
	$AdminText.='<h2>Статьи народных авторов</h2>';
	
	$onpage=50; $from=($pg-1)*$onpage; $orderby="ORDER BY `".$table."`.`stat` ASC, `".$table."`.`data` DESC";
	$q="SELECT `".$table."`.*, `_users`.`nick` FROM `".$table."` LEFT JOIN `_users` ON `_users`.`id`=`".$table."`.`uid` ".$orderby." LIMIT $from, $onpage";
	$data=DB($q); $text=""; echo $q;
	
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $datan=ToRusData($ar["data"]);


		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';				
		$text.='<td class="CheckInput">'.$stat.'</td>';
		$text.='<td class="Act" width="1%" style="white-space:nowrap;" title="Просмотры"> <b>'.(int)$ar["seen"].'</b> </td>';
		$text.="<td class='BigText'><a href='/".$alias."/view/".$ar["id"]."' target='_blank'>".$ar["name"]."</a></div></td>";
		$text.='<td class="Act" width="1%" style="white-space:nowrap;" ><a href="?cat=".$alias."_author&id=".$ar["uid"]."">'.$ar["nick"].'</a></td>';
		$text.='<td class="Act" width="1%" style="white-space:nowrap;" >'.$datan[4].'</td>';
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="?cat='.$alias.'_edit&id='.$ar["id"].'" target="_blank">Модерация</a></td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
	
	$data=DB("SELECT `id` FROM `".$table."`"); $AdminText.= Pager($pg, $onpage, ceil($data["total"]/$onpage));


}

//=============================================
$_SESSION["Msg"]="";
?>