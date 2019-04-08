<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST; $F=$_FILES; $error=0; $types=''; $fonts=''; $Pos=array("l"=>"Слева", "r"=>"Справа", "u"=>"Вверху", "d"=>"Внизу", "c"=>"По центру");
	
	if (isset($P["savebutton"])) {
		$_SESSION["Msg"]="<div class='ErrorDiv'>Не удалось создать новый шаблон! Свяжитесь с администратором.</div>";
		if (isset($F["userpic"]) && $F["userpic"]["tmp_name"]!="") { @require_once($ROOT."/admin/modules/adm/imagemaster-UPL.php"); } else { $vpic=""; }
		
		if ($P["font"]=="") { $_SESSION["Msg"]="<div class='ErrorDiv'>Вы обязаны назначить шрифт текста!</div>"; $error=1; }
		if ((int)$P["txts"]<10) { $_SESSION["Msg"]="<div class='ErrorDiv'>Размер шрифта не может быть иеньше 10 пикселей</div>"; $error=1; }
		
		if ($error==0) { 
			$q="INSERT INTO  `_imagemaster` (`name`,`podlogpic`,`typepic`,`typeform`,`posit`,`posx`,`posy`,`round`,`fontsize`,`fontcolor`,`fontfamily`,`fontchars`) VALUES
			('".$P["dname"]."','".$vpic."','".$P["pictype"]."','".$P["mashtab"]."','".$P["rasp"]."','".(int)$P["inpx"]."','".(int)$P["inpy"]."','".(int)$P["inpb"]."','".(int)$P["txts"]."','".$P["txtr"]."','".$P["font"]."','".$P["txtl"]."')";
			$data=DB($q); $last=DBL(); $_SESSION["Msg"]="<div class='SuccessDiv'>Новый шаблон успешно добавлен! <a href='?cat=adm_imagemasteredit&id=$last'><b>Перейти</b></a></div>";
		}
		@header("location: ?cat=adm_imagemasteradd"); exit();
	}
	

	// ВЫВОД ПОЛЕЙ И ФОРМ
	$AdminText='<h2>Добавление шаблона иллюстраций для социальных сетей</h2>'.$_SESSION["Msg"].$C5."<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";
	foreach($GLOBAL['AutoPicPaths'] as $k=>$v) { $a=explode("-", $v); if ((int)$a[0]!=0 && (int)$a[1]!=0) { $types.="<option value='$k'>Шаблон: $k, размеры: $v пикселей</option>"; }}
	$dir = opendir($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster/fonts"); while ($file = readdir($dir)){ if ($file!="." && $file!="..") { $fonts.="<option value='".$file."'>".$file."</option>"; }}
	foreach($Pos as $k=>$v){ $rasps.="<option value='$k' $ch>".$v."</option>";}
	
	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'
	<tr class="TRLine0"><td class="VarText" style="width:160px !important;">Название шаблона<star>*</star></td><td class="LongInput"><input name="dname" type="text"></td></tr><tr class="TRLine0"><td colspan="2">&nbsp;</td></tr>
	<tr class="TRLine1"><td class="VarText">Шаблон кадрирования</td><td class="LongInput"><div class="sdiv"><select name="pictype">'.$types.'</select></div></td></tr>
	<tr class="TRLine0"><td class="VarText">Параметры наложения      фотографии новости</td><td>
		<div class="SmallInput" style="float:left; width:143px;">Смещение слева (px)<br><input name="inpx" type="text" value="0" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:143px;">Смещение сверху (px)<br><input name="inpy" type="text" value="0" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:143px;">Круглые углы (px)<br><input name="inpb" type="text" value="0" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:133px;">Масштаб (от 0.1 до 2)<br><input name="mashtab" type="text" value="1" style="margin:3px 0 0 0; width:132px;"></div>
	</td></tr><tr class="TRLine0"><td colspan="2">&nbsp;</td></tr>
	<tr class="TRLine1"><td class="VarText">Шрифт текста</td><td class="LongInput"><div class="sdiv"><select name="font">'.$fonts.'</select></div></td></tr>
	<tr class="TRLine0"><td class="VarText">Параметры наложения заголовка новости</td><td>
		<div class="SmallInput" style="float:left; width:143px;">Размер шрифта (px)<br><input name="txts" type="text" value="20" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:143px;">Сиволов в строке (шт.)<br><input name="txtl" type="text" value="20" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:143px;">Цвет надписи (#HEX)<br><input name="txtr" type="text" value="000000" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:133px;">Расположение<br><select name="rasp" style="margin:7px 0 0 0; width:132px;">'.$rasps.'</select></div>
	</td></tr><tr class="TRLine0"><td colspan="2">&nbsp;</td></tr>
	<tr class="TRLine1"><td class="Vartext">Файл подложки</td><td><input type="file" name="userpic" style="border:2px solid #FFF; border-radius:5px;"></td></tr>'."</table></div>";
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Добавить шаблон'></div>";

	// ПРАВАЯ КОЛОНКА
	$AdminRight="<h2>Существующие шаблоны</h2>"; $data=DB("SELECT * FROM `_imagemaster`"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
	$AdminRight.="<div class='SecondMenu'><a href='?cat=adm_imagemasteredit&id=".$ar["id"]."'>".$ar["name"]."</a></div>".$C10; endfor;
}
$_SESSION["Msg"]="";
?>