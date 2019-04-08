<?
### НАСТРОЙКИ КЭШИРВОАНИЯ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// ВЫВОД ПОЛЕЙ И ФОРМ
	$AdminText='<h2>Список доступных модулей сайта</h2>';
	$AdminText.="<div class='RoundText'><table>";
	$AdminText.="<tr class='TRLineC'><td width='1%'></td><td width='30%'>Название модуля</td><td>Описание модуля</td><td width='1%'>Создать</td></tr>";
	
	$data=DB("SELECT `id`,`name`,`text`,`module` FROM `_modules` ORDER BY `name` ASC");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$ar["id"].'">
			<td>'.AIco(27).'</td><td class="BigText"><b>'.$ar["name"].'</b><div class="C" style="margin-top:2px;"></div><span class="VarName" style="font-size:9px;">Модуль: '.$ar["module"].'</span></td>
			<td class="VarName">'.$ar["text"].'</td><td class="Act2"><a href="?cat=adm_razdelinstall&pid='.$ar["id"].'">'.AIco('play', 'Создать раздел на основе этого модуля').'</a></td>
		</tr>';
	endfor;
	$AdminText.="</table></div>";
	
// ПРАВАЯ КОЛОНКА	
	$AdminRight=ATextReplace('ModuleNew-Module');
}
$_SESSION["Msg"]="";
?>