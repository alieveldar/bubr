<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$query=""; foreach ($P["Inp"] as $key=>$val) { $query.="('".$key."','".DBcut($val)."'),"; }
		$res=DB("INSERT INTO `_domains` (`id`, `prefix`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `prefix`=values(`prefix`)");
		$query=""; foreach ($P["Int"] as $key=>$val) { $query.="('".$key."','".DBcut($val)."'),"; }
		$res=DB("INSERT INTO `_domains` (`id`, `name`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `name`=values(`name`)");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно сохранены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	if (isset($P["addbutton"])) {
		$res=DB("INSERT INTO `_domains` (`name`,`prefix`) VALUES ('".DBcut($P["Int0"])."', '".DBcut($P["Inp0"])."')");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно добавлены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	
	

// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2 style="float:left;">Список поддоменов сайта</h2>';
	$AdminText.="<div class='LinkG' style='float:right;'><a href='javascript:void(0);' onclick='ToggleBlock(\"#Tgg\");'>Добавить поддомен</a></div>".$C5.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundTextHide' id='Tgg'><table><tr class='TRLineC'><td>Описание поддомена</td><td>Имя поддомена</td></tr>";
	$AdminText.='<tr class="TRLine" id="Line0"><td class="LongInput" style="width:50%;"><input type="text" name="Int0" value="Новый поддомен"></td>
				<td class="LongInput" style="width:50%;"><input name="Inp0" type="text" class="JsVerify" value="domain"></td></tr>'; 
	$AdminText.="</table><div class='C5'></div><div class='CenterText'><input type='submit' name='addbutton' id='addbutton' class='SaveButton' value='Добавить данные'></div></div></form>";
	
// ВЫВОД ПОЛЕЙ И ФОРМ	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText'><table>";
	$AdminText.="<tr class='TRLineC'><td>Описание поддомена</td><td>Имя поддомена</td><td width='1%'></td></tr>";
	### Переменные пользователей
	$data=DB("SELECT `id`,`prefix`,`name` FROM `_domains` ORDER BY `name` ASC");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$ar["id"].'">
			<td class="LongInput" style="width:50%;"><input name="Int['.$ar["id"].']" value="'.$ar["name"].'"></td>
			<td class="LongInput" style="width:50%;"><input name="Inp['.$ar["id"].']" value="'.$ar["prefix"].'" class="JsVerify"></td>'; 
		$AdminText.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="DeleteVarItem(\''.$ar["id"].'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$AdminText.='</tr>';
	endfor;
	$AdminText.="</table></div>";
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА	
	$AdminRight=ATextReplace('Domains-Module')."<div class='C30'></div><div class='LinkR'><a href='?cat=adm_settings'>Основные настройки сайта</a></div>";
}

$_SESSION["Msg"]="";
?>