<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$query=""; foreach ($P["Inp"] as $key=>$val) { $query.="('".$key."','".DBcut($val)."'),"; }
		$res=DB("INSERT INTO `_settings` (`id`, `value`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `value`=values(`value`)");
		
		$query=""; foreach ($P["Inn"] as $key=>$val) { $query.="('".$key."','".DBcut($val)."'),"; }
		$res=DB("INSERT INTO `_settings` (`id`, `name`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `name`=values(`name`)");
		
		$query=""; foreach ($P["Int"] as $key=>$val) { $query.="('".$key."','".DBcut($val)."'),"; }
		$res=DB("INSERT INTO `_settings` (`id`, `text`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `text`=values(`text`)");

		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно сохранены!</div>";
		DB("INSERT INTO `_lentalog` (`link`, `id`, `uid`, `data`, `ip`, `text`) VALUES ('[vars]', '0', '".$_SESSION['userid']."', '".time()."', '".$_SERVER['REMOTE_ADDR']."', 'Изменение параметров сайта (vars)')");
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	if (isset($P["addbutton"])) {
		$res=DB("INSERT INTO `_settings` (`name`,`text`,`value`,`stat`) VALUES ('".DBcut($P["Inn0"])."', '".DBcut($P["Int0"])."', '".DBcut($P["Inp0"])."', '0')");		
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно добавлены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2 style="float:left;">Пользовательские параметры сайта</h2>';
	$AdminText.="<div style='float:right;' class='LinkG'><a href='javascript:void(0);' onclick='ToggleBlock(\"#Tgg\");'>Добавить параметр</a></div>".$C5.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundTextHide' id='Tgg'><table><tr class='TRLineC'><td>Переменная</td><td>Описание значения</td><td>Значение</td><td></td></tr>";
	$AdminText.='<tr class="TRLine" id="Line0"><td class="SmallInput">$VARS["______"]<div class="C5"></div><input name="Inn0" type="text" class="JsVerify" value="newvar"></td>
	<td class="SmallInput"><textarea name="Int0">'.$ar["text"].'</textarea></td><td class="SmallInput"><textarea name="Inp0">'.$ar["value"].'</textarea></td><td width=1%>'.AIco('sp').'</td></tr>'; 
	$AdminText.="</table><div class='C5'></div><div class='CenterText'><input type='submit' name='addbutton' id='addbutton' class='SaveButton' value='Добавить данные'></div></div></form>";
// ВЫВОД ПОЛЕЙ И ФОРМ	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText'><table>";
	$AdminText.="<tr class='TRLineC'><td>Переменная</td><td>Описание значения</td><td width='1%'>Значение</td><td width='1%'></td></tr>";
	### Переменные пользователей
	$data=DB("SELECT `id`,`name`,`text`,`value`, `type` FROM `_settings` WHERE (`stat`='0') ORDER BY `name` ASC");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$ar["id"].'">
			<td class="SmallInput">$VARS["______"]<div class="C5"></div>
				<input name="Inn['.$ar["id"].']" type="text" value="'.$ar["name"].'" class="JsVerify"></td>
			<td class="SmallInput"><textarea name="Int['.$ar["id"].']">'.$ar["text"].'</textarea></td>
			<td class="SmallInput"><textarea name="Inp['.$ar["id"].']">'.$ar["value"].'</textarea>'; 
		$AdminText.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="DeleteVarItem(\''.$ar["id"].'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$AdminText.='</tr>';
	endfor;
	$AdminText.="</table></div>";
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";
	### Существующие занятые имена переменных
	$data=DB("SELECT `name` FROM `_settings` WHERE (`stat`!='0')"); $AdminText.="<script type='text/javascript'>var NotAvaliable=new Array("; for ($i=0; $i<$data["total"]; $i++):
	@mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $AdminText.="'".$ar["name"]."',"; endfor; $AdminText.="'error');</script>";

// ПРАВАЯ КОЛОНКА	
	$AdminRight=ATextReplace('Vars-Module')."<div class='C30'></div><div class='LinkR'><a href='?cat=adm_settings'>Основные настройки сайта</a></div>";
}
$_SESSION["Msg"]="";
?>