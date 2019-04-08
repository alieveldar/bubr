<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	
if (!is_file("banners-sets.dat")) { $AdminText="Не найден файл &laquo;<b>/admin/banners-sets.dat</b>&raquo; создайте его вручную и поставьте права 0777"; $GLOBAL["error"]=1; } else {
$sets=explode("|", @file_get_contents("banners-sets.dat")); $table3=$sets[0]."_items";

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST; $F=$_FILES; $error=0;
	if (isset($P["savebutton"])) {
		$ar=explode(".", $P["ddata1"]); $sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
		$ar=explode(".", $P["ddata11"]); $sdata2=mktime($P["ddata21"], $P["ddata31"], $P["ddata41"], $ar[1], $ar[0], $ar[2]);
		$P["btext"]=str_replace(array("\r\n", "\r", "\n" ,"'"), array("", "", "", "\'"), $P["btext"]);
		$P["outer"]=str_replace(array("\r\n", "\r", "\n" ,"'"), array("", "", "", "\'"), $P["outer"]);
		$data=DB("SELECT * FROM `_banners_orders` WHERE (`zid`='".$P["zay"]."')"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
		if ($data["total"]!=1) { $error=1; $_SESSION["Msg"]="<div class='ErrorDiv'>Заявка не найдена на сервере $P[zay]</div>";  }
		
		if ($P["deluserflash"]=="1") { @unlink($ROOT."/advert/files/flash/".$P["oflash"]); $P["oflash"]=''; } if (isset($F["userflash"]) && $F["userflash"]["tmp_name"]!="") { @require_once($ROOT."/admin/modules/banners/UPL-flash.php"); } else { $vflash=$P["oflash"]; }
		if ($P["deluserpic"]=="1") { @unlink($ROOT."/advert/files/image/".$P["opic"]); $P["opic"]=''; } if (isset($F["userpic"]) && $F["userpic"]["tmp_name"]!="") { @require_once($ROOT."/admin/modules/banners/UPL-image.php"); } else { $vpic=$P["opic"]; }
		if ($P["delusermobile"]=="1") { @unlink($ROOT."/advert/files/mobile/".$P["omobile"]); $P["omobile"]=''; } if (isset($F["usermobile"]) && $F["usermobile"]["tmp_name"]!="") { @require_once($ROOT."/admin/modules/banners/UPL-mobile.php"); } else { $vmobile=$P["omobile"]; }
		
		if ($error==0) { 
			$q="UPDATE `_banners_items` SET
			`zid`='$P[zay]',
			`pid`='$ar[pid]',
			`cid`='$ar[cid]',
			`did`='$ar[did]',
			`datafrom`='$sdata1',
			`datato`='$sdata2',
			`name`='$P[dname]',
			`text`='$P[btext]',
			`pic`='$vpic',
			`flash`='$vflash',
			`mobile`='$vmobile',
			`link`='$P[link]',
			`link2`='$P[link2]',
			`link3`='$P[link3]',
			`outer`='$P[outer]'
			WHERE (`id`='".(int)$id."');";
			
			$data=DB($q); if ($_SESSION["Msg"]=="" || !isset($_SESSION["Msg"])) { $_SESSION["Msg"]="<div class='SuccessDiv'>Материал успешно сохранен! <a target='_blank' href='/advert/preBanner.php?id=$id'><b>Просмотр</b></a></div>"; }
		}
		@header("location: ?cat=banners_edit&id=".$id); exit();
}
	

// ВЫВОД ПОЛЕЙ И ФОРМ
	
	$data=DB("SELECT * FROM `_banners_items` WHERE (`id`='".(int)$id."')"); @mysql_data_seek($data["result"], 0); $n=@mysql_fetch_array($data["result"]);
	
	$AdminText='<h2>Редактирование материала</h2>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете редактировать рекламный материал, используя номер заявки на размещение</div>";
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";

	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'
	<tr class="TRLine0"><td class="VarText">Номер заявки<star>*</star></td><td class="SmallInput"><input name="zay" id="zay" type="text" style="margin-right:5px;" onfocus="FocusZay();" value="'.$n["zid"].'">
	<input id="zayok" type="hidden" value="0"><span class="LinkB"><a href="javascript:void(0);" onclick="LoadZayavka();">Загрузить данные</a></span><div id="Loader" style="float:right;"></div></td></tr>
	<tr class="TRLine1"><td class="VarText">Заголовок баннера<star>*</star></td><td class="LongInput"><input name="dname" id="dname" type="text" onfocus="$(this).toggleClass(\'ErrorInput\', false);" value=\''.$n["name"].'\'></td></tr>
	<tr class="TRLine0"><td class="VarText">Ссылка с баннера</td><td class="LongInput"><input name="link" type="text" placeholder="http://yandex.ru" value=\''.$n["link"].'\'></td></tr>
	<tr class="TRLine1"><td class="VarText">2 Ссылка с баннера</td><td class="LongInput"><input name="link2" type="text" placeholder="http://mail.ru" value=\''.$n["link2"].'\'></td></tr>
	<tr class="TRLine0"><td class="VarText">3 Ссылка с баннера</td><td class="LongInput"><input name="link3" type="text" placeholder="http://google.com" value=\''.$n["link3"].'\'></td></tr>
	<tr class="TRLine1"><td class="Vartext">Внешний счетчик</td><td class="LongInput"><textarea name="outer" style="height:50px;">'.$n["outer"].'</textarea></td></tr>
	<tr class="TRLine0"><td class="VarText">Начало показов</td><td class="DateInput">'.GetDataSet($n["datafrom"]).'</td></tr>	<tr class="TRLine0"><td class="Vartext">Конец показов</td><td class="DateInput">'.GetDataSet($n["datato"], 1).'</td></tr>
	<tr class="TRLine1"><td class="Vartext">Flash ролик</td><td><input type="file" name="userflash" style="border:2px solid #FFF; border-radius:5px;"><div style="float:right; padding:5px 0 0 0;">удалить: <input type="checkbox" name="deluserflash" value="1"></div></td></tr>
	<tr class="TRLine0"><td class="Vartext">Картинка</td><td><input type="file" name="userpic" style="border:2px solid #FFF; border-radius:5px;"><div style="float:right; padding:5px 0 0 0;">удалить: <input type="checkbox" name="deluserpic" value="1"></div></td></tr>
	<tr class="TRLine1"><td class="Vartext">Мобильный вид</td><td><input type="file" name="usermobile" style="border:2px solid #FFF; border-radius:5px;"><div style="float:right; padding:5px 0 0 0;">удалить: <input type="checkbox" name="delusermobile" value="1"></div></td></tr>
	<tr class="TRLine0"><td class="Vartext">Текст</td><td class="LongInput"><textarea name="btext" style="height:150px;">'.$n["text"].'</textarea></td></tr>
	<input type="hidden" name="oflash" value="'.$n["flash"].'" /><input type="hidden" name="opic" value="'.$n["pic"].'" /><input type="hidden" name="omobile" value="'.$n["mobile"].'" />'."</table><script>LoadZayavka();</script></div>";
	
	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить материал'></div></form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='C5'></div><div class='C30'></div><h2 class='RoundText' style='font-size:20px; text-align:center; padding:10px;'>BID = ".$id."</h2><div class='C20'></div>";

	if ($n["stat"]==1) { $chkt="checked"; } else { $chkt=""; } $AdminRight.="<h2 id='zcap'>Заявка</h2><div class='RoundText' id='Info'>Укажите номер заявки</div>".$C5.
	"<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-_banners_items' ".$chkt."></td><td><b>Включить показы</b></td></tr></table></div>".$C10;
	
	$AdminRight.="<h2>Файлы:</h2><div class='RoundText'>";
		if ($n['flash']!="") { $AdminRight.="<b>Flash</b>: <a target='_blank' href='/advert/files/flash/$n[flash]'>$n[flash]</a>"; } else { $AdminRight.="<b>Flash</b>: НЕТ"; } $AdminRight.="<hr>";
		if ($n['pic']!="") { $AdminRight.="<b>Фото</b>: <a target='_blank' href='/advert/files/image/$n[pic]'>$n[pic]</a>"; } else { $AdminRight.="<b>Фото</b>: НЕТ"; } $AdminRight.="<hr>";
		if ($n['mobile']!="") { $AdminRight.="<b>Моб.</b>: <a target='_blank' href='/advert/files/mobile/$n[mobile]'>$n[mobile]</a>"; } else { $AdminRight.="<b>Моб.</b>: НЕТ"; }
	$AdminRight.="</div>".$C10;
	
	$AdminRight.="<div class='LinkR'><a href='/advert/preBanner.php?id=$id' target='_blank'>Предварительный просмотр</a></div>";
		
}
}

$_SESSION["Msg"]="";
?>