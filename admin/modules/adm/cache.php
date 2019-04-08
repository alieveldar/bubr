<?
### НАСТРОЙКИ КЭШИРВОАНИЯ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$query=""; foreach ($P["Inp"] as $key=>$val) { $query.="('".(int)$key."','".(int)$val."'),"; }
		$res=DB("INSERT INTO `_settings` (`id`, `value`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `value`=values(`value`)");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Данные успешно сохранены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

// ВЫВОД ПОЛЕЙ И ФОРМ
	$AdminText='<h2>Кэширование сайта</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";
	$AdminText.="<div class='RoundText'><table>";
	$AdminText.="<tr class='TRLineC'><td>Переменная</td><td>Описание значения</td><td width='1%'>Значение</td></tr>";
	
	$data=DB("SELECT `id`,`name`,`text`,`value`,`type` FROM `_settings` WHERE (`stat`='2') ORDER BY `id` ASC");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$ar["id"].'">
			<td class="VarName">$VARS["'.$ar["name"].'"]</td>
			<td class="VarText">'.$ar["text"].'</td>
			<td class="SmallInput">';
			if ($ar["type"]=="var") { $AdminText.='<input name="Inp['.$ar["id"].']" type="text" value="'.$ar["value"].'" maxlenght="5">';
			} else { $AdminText.='<textarea name="Inp['.$ar["id"].']">'.$ar["value"].'</textarea>'; }
		$AdminText.='</td></tr>';
	endfor;
	
	$AdminText.="</table></div>";
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";
	
// ПРАВАЯ КОЛОНКА	
	$AdminRight=ATextReplace('Cache-Module')."<div class='C30'></div><div class='LinkR'>
	<a href='javascript:void(0);' onclick='LinkBlank(\"Очистить весь кэш сайта?\",\"Это может привести к повышению нагрузки на сервер.\", \"?cat=adm_clearcache\")'>Полная очистка кэша сайта</a></div>";
}
$_SESSION["Msg"]="";
?>