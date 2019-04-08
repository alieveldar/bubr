<?
### НАСТРОЙКИ КЭШИРВОАНИЯ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// ПОЛУЧЕНИЕ ВСЕХ ФИЗИЧЕСКИХ ПАПОК ДИЗАЙНА in "../template"
$reald=array(); $used=array(); $i=0; if (!is_dir("../template")) { mkdir("../template/", 0777); } $dir=opendir("../template"); while ($file=readdir($dir)){ if ($file != "." && $file != ".." && is_dir("../template/".$file) && $file!="standart" && $file!="page_mods") { $reald[$file]["folder"]=$file; if (is_file("../template/".$file."/".$file.".css")) { $reald[$file]["css"]="../template/".$file."/".$file.".css"; } if (is_file("../template/".$file."/".$file.".html")) { $reald[$file]["html"]="../template/".$file."/".$file.".html"; }}}

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$query=""; foreach ($P["des"] as $key=>$val) { $query.="('".$key."','".$val."'),"; }
		DB("TRUNCATE TABLE `_designs`"); $res=DB("INSERT INTO `_designs` (`folder`, `name`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `name`=values(`name`)");
		DB("UPDATE `_designs` SET `stat`='1' WHERE (`folder`='".$P["maindesign"]."')");
		$_SESSION["Msg"]="<div class='SuccessDiv'>Данные успешно сохранены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

// ВЫВОД ПОЛЕЙ И ФОРМ
	$AdminText='<h2>Шаблоны дизайнов сайта</h2>'.$_SESSION["Msg"]."<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><div class='RoundText'><table>";
	$AdminText.="<tr class='TRLineC'><td width='1%'>Основной</td><td>Название</td><td>Директория</td><td width='1%'>HTML</td><td width='1%'>CSS</td><td>Статус</td></tr>";
	$data=DB("SELECT `name`,`stat`,`folder` FROM `_designs` ORDER BY `folder` ASC");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$used[]=$ar["folder"]; if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } $real["html"]=""; $real["css"]="";
		if (is_file("../template/".$ar["folder"]."/".$ar["folder"].".css")) { $real["css"]="template/".$ar["folder"]."/".$ar["folder"].".css";}
		if (is_file("../template/".$ar["folder"]."/".$ar["folder"].".html")) { $real["html"]="template/".$ar["folder"]."/".$ar["folder"].".html";}
		$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'"><td class="VarName" align="center"><input type="radio" name="maindesign" '.$chk.' value="'.$ar["folder"].'"></td>
		<td class="NormalInput"><input type="text" name="des['.$ar["folder"].']" value="'.$ar["name"].'"></td><td class="VarText">'.$ar["folder"].'</td>';
		$AdminText.='<td class="VarName" align="center" title="'.$real["html"].'">'; if ($real["html"]!="") { $AdminText.=AIco('10'); } else { $AdminText.=AIco('12'); } $AdminText.='</td>';
		$AdminText.='<td class="VarName" align="center" title="'.$real["css"].'">'; if ($real["css"]!="") { $AdminText.=AIco('10'); } else { $AdminText.=AIco('12'); } $AdminText.='</td>';
		if ($real["html"]!="") { $stat='Доступен'; $statc="VarText"; } else { $stat='Требуется HTML'; $statc="VarName"; } $AdminText.='<td class="'.$statc.'">'.$stat.'</td></tr>';
	
	endfor;
	
	foreach($reald as $folder=>$real) {
		if (!in_array($folder, $used)) {
		$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">
		<td class="VarName" align="center"><input type="radio" name="maindesign" value="'.$folder.'"></td>
		<td class="NormalInput"><input type="text" name="des['.$folder.']"></td><td class="VarText">'.$folder.'</td>';
		$AdminText.='<td class="VarName" align="center" title="'.str_replace("../","",$real["html"]).'">'; if ($real["html"]!="") { $AdminText.=AIco('10'); } else { $AdminText.=AIco('12'); } $AdminText.='</td>';
		$AdminText.='<td class="VarName" align="center" title="'.str_replace("../","",$real["css"]).'">'; if ($real["css"]!="") { $AdminText.=AIco('10'); } else { $AdminText.=AIco('12'); } $AdminText.='</td>';
		$AdminText.='<td class="VarName">Не сохранен</td></tr>'; $i++;
		}
	}
	
	$AdminText.="</table></div>";
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";
	
// ПРАВАЯ КОЛОНКА	
	$AdminRight=ATextReplace('Design-Module')."<div class='C10'></div>";
}
$_SESSION["Msg"]="";
?>