<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg;
	$table=$alias."_items";
	$table2=$alias."_cats";
	
	// ЭЛЕМЕНТЫ
	$data=DB("SELECT name, orderby, onpage FROM `_pages` WHERE (`link`='".$alias."')"); @mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	$AdminText.='<h2 style="float:left;">'.$raz["name"].'</h2><div class="LinkG" style="float:right;"><a href="?cat='.$alias.'_add">Добавить материал</a></div>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете редактировать и удалять записи</div>";
	
	$onpage=$raz["onpage"]; $orderby=$ORDERS[$raz["orderby"]]; $from=($pg-1)*$onpage;
	$data=DB("SELECT `".$table."`.* FROM `".$table."` ".$orderby." LIMIT $from, $onpage"); $text="";
	
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $datan=ToRusData($ar["data"]);
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';			
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';		
		$text.="<td class='BigText'><a href='/".$alias."/view/".$ar["id"]."' target='_blank'>".$ar["name"]."</a> <i>$ar[catn]</i></td>";	
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px;" ><i>'.$datan[4].'</i></td>';
		$text.='<td class="Act"><a href="?cat='.$alias.'_edit&id='.$ar["id"].'" title="Править">'.AIco('28').'</a></td>';

		if ($raz["orderby"]==5 || $raz["orderby"]==6) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\', \''.$table.'\', \''.$raz["orderby"].'\')" title="Поднять">'.AIco(3).'</a></td>
		<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\', \''.$table.'\', \''.$raz["orderby"].'\')" title="Опустить">'.AIco(4).'</a></td>'; }
		
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.='<td class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
	$data=DB("SELECT `id` FROM `".$table."`"); $AdminText.= Pager($pg, $onpage, ceil($data["total"] / $onpage));
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight='<div class="C20"></div><div class="C20"></div><div class="SecondMenu"><a href="?cat='.$alias.'_cats">Рубрикатор компаний</a></div><div class="C"></div><div class="SecondMenu"><a href="?cat='.$alias.'_consultscats">Рубрикатор консультаций</a></div>';
	
}

//=============================================
$_SESSION["Msg"]="";
?>