<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST; $F=$_FILES; $error=0; $types=''; $fonts=''; $ROOT=$_SERVER['DOCUMENT_ROOT'];
	$testtext=""; $Pos=array("l"=>"Слева", "r"=>"Справа", "u"=>"Вверху", "d"=>"Внизу", "c"=>"По центру");
	$AdminText.="<style>.HavePicPre { text-align:center; } .HavePicPre img { max-width:730px; } </style>";
	if (isset($P["savebutton"])) {
		$_SESSION["Msg"]="<div class='ErrorDiv'>Не удалось сохранить шаблон! Свяжитесь с администратором.</div>";
		if (isset($F["userpic"]) && $F["userpic"]["tmp_name"]!="") { @require_once($ROOT."/admin/modules/adm/imagemaster-UPL.php"); } else { $vpic=$P["oldpic"]; }
		
		if ($P["font"]=="") { $_SESSION["Msg"]="<div class='ErrorDiv'>Вы обязаны назначить шрифт текста!</div>"; $error=1; }
		if ((int)$P["txts"]<10) { $_SESSION["Msg"]="<div class='ErrorDiv'>Размер шрифта не может быть иеньше 10 пикселей</div>"; $error=1; }
		
		if ($error==0) { 
			$q="UPDATE `_imagemaster` SET `name`='".$P["dname"]."',`podlogpic`='".$vpic."',`typepic`='".$P["pictype"]."',`posit`='".$P["rasp"]."',`typeform`='".$P["mashtab"]."',`posx`='".(int)$P["inpx"]."',`posy`='".(int)$P["inpy"]."',
			`round`='".(int)$P["inpb"]."', `fontsize`='".(int)$P["txts"]."',`fontcolor`='".$P["txtr"]."',`fontfamily`='".$P["font"]."',`fontchars`='".$P["txtl"]."' WHERE `id`='".(int)$_GET["id"]."' LIMIT 1";
			$data=DB($q); $_SESSION["Msg"]="<div class='SuccessDiv'>Шаблон успешно сохранен!</div>";
		}
		@header("location: ?cat=adm_imagemasteredit&id=".$id); exit();
	}
	

	// ВЫВОД ПОЛЕЙ И ФОРМ
	$AdminText.='<h2>Редактирование шаблона иллюстраций для социальных сетей</h2>'.$_SESSION["Msg"].$C5."<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";
	$data=DB("SELECT * FROM `_imagemaster` WHERE `id`='".(int)$_GET["id"]."' LIMIT 1"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	foreach($GLOBAL['AutoPicPaths'] as $k=>$v){$a=explode("-",$v); if((int)$a[0]!=0 && (int)$a[1]!=0){if($ar["typepic"]==$k){$ch='selected';}else{$ch='';}$types.="<option value='$k' $ch>Шаблон: $k, размеры: $v пикселей</option>";}}
	$dir=opendir($ROOT."/userfiles/imagemaster/fonts"); while($file=readdir($dir)){ if ($file!="." && $file!=".."){if($ar["fontfamily"]==$file){$ch='selected';}else{$ch='';}$fonts.="<option value='".$file."' $ch>".$file."</option>";}}
	foreach($Pos as $k=>$v){ if($ar["posit"]==$k){$ch='selected';}else{$ch='';}$rasps.="<option value='$k' $ch>".$v."</option>";}
	
	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'
	<tr class="TRLine0"><td class="VarText" style="width:160px !important;">Название шаблона<star>*</star></td><td class="LongInput"><input name="dname" type="text" value="'.$ar["name"].'"></td></tr>
	<tr class="TRLine0"><td colspan="2">&nbsp;</td></tr><tr class="TRLine1"><td class="VarText">Шаблон кадрирования</td><td class="LongInput"><div class="sdiv"><select name="pictype">'.$types.'</select></div></td></tr>
	<tr class="TRLine0"><td class="VarText">Параметры наложения      фотографии новости</td><td>
		<div class="SmallInput" style="float:left; width:143px;">Смещение слева (px)<br><input name="inpx" type="text" value="'.$ar["posx"].'" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:143px;">Смещение сверху (px)<br><input name="inpy" type="text" value="'.$ar["posy"].'" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:143px;">Круглые углы (px)<br><input name="inpb" type="text" value="'.$ar["round"].'" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:133px;">Масштаб (от 0.1 до 2)<br><input name="mashtab" type="text" value="'.$ar["typeform"].'" style="margin:3px 0 0 0; width:132px;"></div>
	</td></tr><tr class="TRLine0"><td colspan="2">&nbsp;</td></tr><tr class="TRLine1"><td class="VarText">Шрифт текста</td><td class="LongInput"><div class="sdiv"><select name="font">'.$fonts.'</select></div></td></tr>
	<tr class="TRLine0"><td class="VarText">Параметры наложения заголовка новости</td><td>
		<div class="SmallInput" style="float:left; width:143px;">Размер шрифта (px)<br><input name="txts" type="text" value="'.$ar["fontsize"].'" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:143px;">Сиволов в строке (шт.)<br><input name="txtl" type="text" value="'.$ar["fontchars"].'" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:143px;">Цвет надписи (#HEX)<br><input name="txtr" type="text" value="'.$ar["fontcolor"].'" style="margin:3px 0 0 0; width:132px;"></div>
		<div class="SmallInput" style="float:left; width:133px;">Расположение<br><select name="rasp" style="margin:7px 0 0 0; width:132px;">'.$rasps.'</select></div>
	</td></tr><tr class="TRLine0"><td colspan="2">&nbsp;</td></tr><tr class="TRLine1"><td class="Vartext">Файл подложки</td><td><input type="file" name="userpic" style="border-radius:5px;"></td></tr>'.
	"<input type='hidden' name='oldpic' value='".$ar["podlogpic"]."'></table></div><div class='CenterText'><input type='submit' name='savebutton' class='SaveButton' value='Сохранить шаблон'></div></form>".$C20;
	
	
	// ТЕСТОВАЯ ПРОВЕРКА
	$AdminText.="<h2>Проверить работу шаблона</h2>"; if (isset($P["testbutton"])){  if(isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"]!='' && $P["testname"]!='') { @require("modules/UploadPhoto.php");
	@require("../modules/standart/ImageMaster.php"); list($_SESSION["TestPic"], $_SESSION["TestMsg"], $_SESSION["TestLog"])=CreateImageMaster($id, $picname, $P["testname"]); @header("location: ?cat=adm_imagemasteredit&id=".$id); exit(); }}

	//$AdminText.="<h2>Проверить работу шаблона</h2>"; @require("../modules/standart/ImageMaster.php"); list($_SESSION["TestPic"], $_SESSION["TestMsg"], $_SESSION["TestLog"])=CreateImageMaster($id, $picname, $P["testname"]); 

	
	if ($_SESSION["TestPic"]!="" || $_SESSION["TestMsg"]!=""){
		$AdminText.=$_SESSION["TestMsg"]."<div class='RoundText HavePicPre'><img src='/userfiles/imagemaster/".$_SESSION["TestPic"]."'></div>".$C10; 
		if ($_SESSION["TestLog"]!="") { $AdminText.=$C10."<div class='RoundText' style='font:11px/14px Tahoma; color:#999;'>".$_SESSION["TestLog"]."</div>".$C10;  }
	}
	
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>"; $AdminText.='<div class="RoundText"><table><tr class="TRLine0">
	<td class="VarText">Текст надписи </td><td class="LongInput"><input name="testname" type="text" value="'.$testtext.'"></td></tr><tr class="TRLine0"><td class="Vartext">Фотография</td><td><input type="file" name="photo"></td></tr>'.
	"</table></div><div class='CenterText'><input type='submit' name='testbutton' class='SaveButton' value='Проверить шаблон'></div></form>"; $_SESSION["TestPic"]=''; $_SESSION["TestMsg"]='';
	
	
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<h2>Существующие шаблоны</h2>"; $data=DB("SELECT * FROM `_imagemaster`"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
	if ($id==$ar["id"]) { $AdminRight.="<div class='SecondMenu2'><a href='?cat=adm_imagemasteredit&id=".$ar["id"]."'>".$ar["name"]."</a></div>".$C10;
	} else { $AdminRight.="<div class='SecondMenu'><a href='?cat=adm_imagemasteredit&id=".$ar["id"]."'>".$ar["name"]."</a></div>".$C10; } endfor;
}
$_SESSION["Msg"]="";
?>