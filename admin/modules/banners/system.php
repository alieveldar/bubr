<?
### НАСТРОЙКИ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	


// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$dat=$P["Inps"]."|"; 
		
		chmod("banners-sets.dat", 0777); @file_put_contents("banners-sets.dat", $dat);
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно сохранены!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	if (is_file("banners-sets.dat")) { $sets=explode("|", @file_get_contents("banners-sets.dat")); } else { @file_put_contents("banners-sets.dat", ""); chmod("banners-sets.dat", 0777); }
	if (!is_file("banners-sets.dat")) { $AdminText="Не найден файл &laquo;<b>/admin/banners-sets.dat</b>&raquo; создайте его вручную и поставьте права 0777"; $GLOBAL["error"]=1; } else {
	if ($sets[0]=="") { $_SESSION["Msg"]="<div class='ErrorDiv'>Не выбрана необходимая таблица компаний баннерной системы!</div>"; }

// ВЫВОД ПОЛЕЙ И ФОРМ
	$AdminText='<h2>Настройки баннерной системы</h2>'.$_SESSION["Msg"];	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'><div class='RoundText'><table>";
		
	/* Таблица компаний */
	$tar[""]="Не выбрано"; $data=DB("SELECT name, link FROM `_pages` WHERE (`module`='companies') ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $tar[$ar["link"]]=$ar["name"]; endfor;
	$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$ar["id"].'"><td class="LongInput" style="width:50%;">Список компаний для баннерной системы<br>(необходим модуль &laquo;<b>Компании</b>&raquo;)</td><td class="LongInput" style="width:50%;"><div class="sdiv"><select name="Inps">'.GetSelected($tar, $sets[0]).'</select></div></td></tr>';
	
	
	
	
	$AdminText.="</table></div><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div></form>"; $AdminRight="";
}}

$_SESSION["Msg"]="";
?>