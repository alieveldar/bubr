<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $lastr="";
	$table="_users";
	$limit=50;
	$from=($pg - 1) * $limit;		
	
	// ЭЛЕМЕНТЫ
	$AdminText.='<h2 style="float:left;">Администраторы сайта</h2>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете редактировать и удалять пользователей</div>";
	
	$data=DB("SELECT * FROM `".$table."` WHERE `role`>=1 ORDER BY `role` DESC, `nick` ASC LIMIT $from, $limit"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; }
		$lasttime=$ar["lasttime"] ? date("d.m.Y H:i:s", $ar["lasttime"]) : 'Не производился';
		$avatar=$ar["avatar"] ? "/".$ar["avatar"] : "/userfiles/avatar/no_photo.png";
		$info='ItemInfo('.$ar["id"].', "'.$ar["ip"].'", "'.$GLOBAL["roles"][$ar["role"]].'", "'.$ar["login"].'", "'.$ar["vkontakte"].'", "'.$ar["mailru"].'", "'.$ar["twitter"].'", "'.$ar["facebook"].'", "'.$ar["odnoklas"].'", "'.$ar["google"].'", "'.$ar["yandex"].'", "'.$ar["mail"].'", "'.$ar["spectitle"].'", "'.str_replace('"', '\"', $ar["signature"]).'", "'.date("d.m.Y H:i:s", $ar["created"]).'", "'.$lasttime.'", "'.$avatar.'");';
		$edit="ItemEdit( '".$ar["id"]."', '".$ar["login"]."', '".$ar["link"]."', '".$ar["runtime"]."')";
		$rname=$GLOBAL["roles"][$ar["role"]];
		
		if ($rname!=$lastr) { $lastr=$rname; $text.='<tr class="TRLine0" id="Line0"><td colspan="7"><h2 style="padding:0; margin:10px 0;">'.$rname.'</h2></td></tr>'; }
		
		$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';
		if ($ar["role"]>$_SESSION['userrole']) {
			$text.='<td class="Act"></td>';
		} else {
			$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';
		}			
		$text.="<td class='BigText'><a href='/users/view/".$ar["id"]."' target='_blank'>".$ar["nick"]."</a> <i>".$ar["mail"]."</i></td>";
		if ($ar["role"]>$_SESSION['userrole']) {
			$text.='<td class="Act"></td>';
		} else {
			$text.='<td class="Act"><a href="?cat=adm_userschange&id='.$ar["id"].'" title="Взять этот логин">'.AIco('56').'</a></td>';
		}
		$text.='<td class="Act"><a href="javascript:void(0);" onclick=\''.$info.'\' title="Информация">'.AIco('49').'</a></td>';		
		$text.='<td class="Act"><a href="?cat=adm_usersedit&id='.$ar["id"].'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act"> </td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$pg.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.='<td class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></td>';
		$text.="</tr>";
	endfor; 
    if($text) $AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>".$text."</table></div>";
    else $AdminText .= "<div class='RoundText' id='Tgg'>Пользователей с правами нет</div>";


	$data=DB("SELECT `id` FROM `".$table."` WHERE `role`>1"); $total=ceil($data["total"] / $limit);
	
	$AdminText.= Pager($pg, $limit, $total);
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='C20'></div>В данном списке отображаются пользователи, имеющие права на выполнение различных действий над пользователями, создание страниц и статей, управление меню сайта, операции с модулями, настройки сайта.";
	
}

//=============================================
$_SESSION["Msg"]="";
?>