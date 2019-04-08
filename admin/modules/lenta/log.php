<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]); $bst="";

	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT * FROM `".$alias."_lenta` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]); $AdminText='<h2>Лог: &laquo'.$node["name"].'&raquo;</h2>';

	$q="SELECT `_lentalog`.*, `_users`.`login`, `_users`.`nick`, `_users`.`role` FROM `_lentalog` LEFT JOIN `_users` ON `_users`.`id`=`_lentalog`.`uid` WHERE (`_lentalog`.`id`='".(int)$id."' && `_lentalog`.`link`='".$alias."') ORDER BY `_lentalog`.`data` ASC"; $data=DB($q);
	if ($data["total"]==0) { $text="Лог для этой публикации недоступен."; } else { $text=""; } for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
	
	$datan=ToRusData($ar["data"]);
	$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';				
	$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px; padding:7px;" ><i>'.$datan[4].'<br>IP: '.$ar["ip"].'</i></td>';
	$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:10px; padding:7px;" ><i><a href="?cat=adm_usersedit&id='.$ar["uid"].'">'.$ar["nick"].'</a><br>логин: '.$ar["login"].' ['.$ar["role"].']</i></td>';
	$text.="<td class='BigText' style='padding:7px;'><span style='color:#666; font-size:11px;'>".$ar["text"]."</span> <i>$ar[catn]</i></td>";	
	
	$text.="</tr>";
	
	endfor; $AdminText.="<div class='RoundText' id='Tgg'><table>".$text."</table></div>";

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pretext&id=".$id."'>Виджет: Текстовые поля</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_report&id=".$id."'>Виджет: Фото-отчет</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_album&id=".$id."'>Виджет: Фото-альбом</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_film&id=".$id."'>Виджет: Видео-вставка</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_contacts&id=".$id."'>Виджет: Лого и контакты</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_eventmap&id=".$id."'>Виджет: Карта событий</a></div>
	<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
	if ($_SESSION['userrole']>2) { $AdminRight.="<br><div class='SecondMenu2'><a href='?cat=".$alias."_log&id=".$id."'>Лог редактирования записи</a></div>"; }
	
	}}
}
$_SESSION["Msg"]="";
?>