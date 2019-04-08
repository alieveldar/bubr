<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$query=""; foreach ($P["Inp"] as $key=>$val) { $query.="('".$key."','".DBcut($val)."'),"; }
		$res=DB("INSERT INTO `_settings` (`id`, `value`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `value`=values(`value`)");
		
		DB("INSERT INTO `_lentalog` (`link`, `id`, `uid`, `data`, `ip`, `text`) VALUES ('[settings]', '0', '".$_SESSION['userid']."', '".time()."', '".$_SERVER['REMOTE_ADDR']."', 'Изменение основных настроек сайта (settings)')");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Данные успешно сохранены!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

// ВЫВОД ПОЛЕЙ И ФОРМ
	$AdminText='<h2>Основные настройки сайта</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";
	$AdminText.="<div class='RoundText'><table>";
	$AdminText.="<tr class='TRLineC'><td>Переменная</td><td>Описание значения</td><td width='1%'>Значение</td></tr>";

	$data=DB("SELECT `id`,`name`,`text`,`value`, `type` FROM `_settings` WHERE (`stat`='1') ORDER BY `id` ASC");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$ar["id"].'">
			<td class="VarName">$VARS["'.$ar["name"].'"]</td>
			<td class="VarText">'.$ar["text"].'</td>
			<td class="NormalInput">';
			if ($ar["type"]=="var") { $AdminText.='<input name="Inp['.$ar["id"].']" type="text" value="'.$ar["value"].'"></td>';
			} else { $AdminText.='<textarea name="Inp['.$ar["id"].']">'.$ar["value"].'</textarea>'; }
		$AdminText.='</tr>';
		if ($ar["name"]=="timezone") { $timezone=$ar["value"]; }
	endfor;
	
	$AdminText.="</table></div>";
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";


// ПРАВАЯ КОЛОНКА	
	$AdminRight=ATextReplace('Settings-Module')."<div class='C20'></div><b>Время сервера: ".date("d.m.Y, H:i")."<div class='C5'></div>Время на сайте: ".date("d.m.Y, H:i", time()+($timezone*3600))."</b>
	<div class='C30'></div><div class='LinkR'><a href='?cat=adm_vars'>Добавить свои параметры</a></div>";
}
$_SESSION["Msg"]="";
?>