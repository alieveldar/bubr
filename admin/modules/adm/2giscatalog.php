<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$sets = implode('|', $P["sets"]);
		DB("UPDATE `_pages` SET `name`='".trim($P["name"])."', `sets`='".$sets."', `stat`='".(int)$P['stat']."', `link`='".trim($P["link"])."' WHERE (`module`='".$file."')");

		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно сохранены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	
// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `name`,`shortname`,`sets`,`stat`, `link` FROM `_pages` WHERE (`module`='".$file."')");
	@mysql_data_seek($data["result"],0); $ar=@mysql_fetch_array($data["result"]);
	if ($ar["stat"]==1) { $chk="checked"; }
	$sets = explode('|', $ar["sets"]);
	$AdminText='<h2 style="float:left;">'.$ar["shortname"].'</h2>'.$C5.$_SESSION["Msg"];	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText'><table>";
	$AdminText.='<tr class="TRLine0"><td class="VarText">Название раздела</td><td class="NormalInput"><input name="name" type="text" value=\''.$ar["name"].'\'></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Город для поиска</td><td class="NormalInput"><input name="sets[0]" type="text" value="'.$sets[0].'"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Ключ 2GIS</td><td class="NormalInput"><input name="sets[1]" type="text" value="'.$sets[1].'"></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Адрес страницы</td><td class="NormalInput"><input name="link" type="text" value="'.$ar["link"].'"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Включен</td><td class="NormalInput"><input name="stat" type="checkbox" value="1" '.$chk.'></td></tr>';
	$AdminText.="</table></div>";
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА	
	$AdminRight="";
}
$_SESSION["Msg"]="";
?>