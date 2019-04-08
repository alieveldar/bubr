<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table="_users";	

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
$P=$_POST;

if (isset($P["searchbutton"])) {
	$_SESSION["searchtext"]=$P["searchtext"];
	@header("location: ".$_SERVER["REQUEST_URI"]); exit();
}

// ФОРМА ДОБАВЛЕНИЯ
$AdminText.='<h2 style="float:left;">Поиск пользователя</h2>'
.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Введите текст в поле ниже</div>";

$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
$AdminText.="<div class='RoundText' id='Tgg'><table>".'<tr class="TRLine0"><td style="width:75%;"></td><td style="width:25%;"></td></tr>
<tr class="TRLine0"><td class="LongInput"><input name="searchtext" id="searchtext" type="text"></td><td class="VarText"><input type="submit" name="searchbutton" id="searchbutton" class="SaveButton" value="Искать"></td></tr>                       
'."</table><div class='C5'></div>
<div class='C15'></div></div></form>";

if (isset($_SESSION["searchtext"])) {
	$data=DB("SELECT * FROM `".$table."` WHERE `login` LIKE '%{$_SESSION["searchtext"]}%' OR `nick` LIKE '%{$_SESSION["searchtext"]}%' LIMIT 100"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; }
		$lasttime=$ar["lasttime"] ? date("d.m.Y H:i:s", $ar["lasttime"]) : 'Не производился';
		$avatar=$ar["avatar"] ? "/".$ar["avatar"] : "/userfiles/avatar/no_photo.png";
		$info='ItemInfo('.$ar["id"].', "'.$ar["ip"].'", "'.$GLOBAL["roles"][$ar["role"]].'", "'.$ar["login"].'", "'.$ar["vkontakte"].'", "'.$ar["mailru"].'", "'.$ar["twitter"].'", "'.$ar["facebook"].'", "'.$ar["odnoklas"].'", "'.$ar["google"].'", "'.$ar["yandex"].'", "'.$ar["mail"].'", "'.$ar["spectitle"].'", "'.str_replace('"', '\"', $ar["signature"]).'", "'.date("d.m.Y H:i:s", $ar["created"]).'", "'.$lasttime.'", "'.$avatar.'");';
		$edit="ItemEdit( '".$ar["id"]."', '".$ar["login"]."', '".$ar["link"]."', '".$ar["runtime"]."')";
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
    if($text) $AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$table."\")'>Удалить выбранные</a></div><table>".$text."</table></div>";
    
    unset($_SESSION["searchtext"]);
}

$AdminRight="<div class='C20'></div>Достаточно ввести только часть имени искомого пользователя, после чего отобразится список пользователей, в именах которых присутствует введёный текст.";
	
}
$_SESSION["Msg"]="";

?>