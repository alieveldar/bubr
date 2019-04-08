<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg;
	global $cid;
	$table=$alias."_nodes";
	$table2=$alias."_cats";
	
	// ЭЛЕМЕНТЫ
	$where=$cid ? "WHERE `cat`=".$cid : "";
	if($cid) { $data=DB("SELECT name FROM `$table2` WHERE (`id`='$cid')"); @mysql_data_seek($data["result"], 0); $cat_=@mysql_fetch_array($data["result"]); $catname=$cat_["name"]; }
	else $catname='все категории';
	
	$data=DB("SELECT name, orderby, onpage FROM `_pages` WHERE (`link`='".$alias."')"); @mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);
	$AdminText.='<h2 style="float:left;">'.$raz["name"].' <span style="font-size:14px;">('.$catname.')</span></h2>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете просматривать и удалять записи</div>";
	
	$onpage=$raz["onpage"]; $orderby=$ORDERS[$raz["orderby"]]; $from=($pg-1)*$onpage;
	$data=DB("SELECT `$table`.*, `$table2`.`name` as catn FROM `$table` LEFT JOIN `$table2` ON `$table2`.`id`=`$table`.`cat` $where $orderby LIMIT $from, $onpage"); $text="";
	
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		$datan=ToRusData($ar["data"]);
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';				
		$text.="<td class='BigText'>".$ar["name"]." <i>$ar[catn]</i></td>";	
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px;" ><i>'.$datan[4].'</i></td>';
		$text.='<td class="Act"><a href="?cat='.$alias.'_show&id='.$ar["id"].'" title="Посмотреть">'.AIco('49').'</a></td>';

		if ($raz["orderby"]==5 || $raz["orderby"]==6) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\', \''.$table.'\', \''.$raz["orderby"].'\')" title="Поднять">'.AIco(3).'</a></td>
		<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\', \''.$table.'\', \''.$raz["orderby"].'\')" title="Опустить">'.AIco(4).'</a></td>'; }
		
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.='<td class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></td>';
		$text.="</tr>";
	endfor;
	
	if($data["total"]) $AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
	else $AdminText.="<div class='RoundText' id='Tgg'>Материалов нет</div>";
	$data=DB("SELECT `id` FROM `".$table."` $where"); $AdminText.= Pager($pg, $onpage, ceil($data["total"]/$onpage));
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<h2>Категории раздела</h2>"; $text=""; $data=DB("SELECT * FROM `".$table2."` order by `rate` DESC");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
	if($cid == $ar["id"]) $text.="<div class='SecondMenu2'>";
	else $text.="<div class='SecondMenu'>";
	$text.="<a href='?cat=".$alias."_list&cid=".$ar["id"]."'>".$ar["name"]."</a></div>"; endfor;
	$AdminRight.="<div class='RoundText' id='Tgg'>".$text."</table></div>".$C10."<div class='LinkR' align='center'><a href='?cat=".$alias."_cats'>Редактировать список</a></div>";
	
}

//=============================================
$_SESSION["Msg"]="";
?>