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
			
		if (isset($F["userflash"]) && $F["userflash"]["tmp_name"]!="") { @require_once($ROOT."/admin/modules/banners/UPL-flash.php"); } else { $vflash=""; }
		if (isset($F["userpic"]) && $F["userpic"]["tmp_name"]!="") { @require_once($ROOT."/admin/modules/banners/UPL-image.php"); } else { $vpic=""; }
		if (isset($F["usermobile"]) && $F["usermobile"]["tmp_name"]!="") { @require_once($ROOT."/admin/modules/banners/UPL-mobile.php"); } else { $vmobile=""; }
		
		if ($error==0) { 
			$q="INSERT INTO `_banners_items` (`zid`, `stat`, `prior`, `pid`, `cid`, `did`, `datafrom`, `datato`, `name`, `text`, `pic`, `flash`, `mobile`, `link`, `link2`, `link3`, `outer`) VALUES
			('$P[zay]', '$P[st]', '1', '$ar[pid]', '$ar[cid]', '$ar[did]', '$sdata1', '$sdata2', '$P[dname]', '$P[btext]', '$vpic', '$vflash', '$vmobile', '$P[link]', '$P[link2]', '$P[link3]', '$P[outer]');"; $data=DB($q); $last=DBL();
			if ($_SESSION["Msg"]=="" || !isset($_SESSION["Msg"])) { $_SESSION["Msg"]="<div class='SuccessDiv'>Новый материал успешно добавлен! <a target='_blank' href='/advert/preBanner.php?id=$last'><b>Просмотр</b></a></div>"; }
		}
		@header("location: ?cat=banners_add"); exit();
	}
	

// ВЫВОД ПОЛЕЙ И ФОРМ

	$AdminText='<h2>Добавление материала</h2>'.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете добавить рекламный материал, используя номер заявки на размещение</div>";
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";

	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'
	<tr class="TRLine0"><td class="VarText">Номер заявки<star>*</star></td><td class="SmallInput"><input name="zay" id="zay" type="text" value="0" style="margin-right:5px;" onfocus="FocusZay();"><input id="zayok" type="hidden" value="0">
	<span class="LinkB"><a href="javascript:void(0);" onclick="LoadZayavka();">Загрузить данные</a></span><div id="Loader" style="float:right;"></div></td></tr>
	<tr class="TRLine1"><td class="VarText">Заголовок баннера<star>*</star></td><td class="LongInput"><input name="dname" id="dname" type="text" onfocus="$(this).toggleClass(\'ErrorInput\', false);"></td></tr>
	<tr class="TRLine0"><td class="VarText">Ссылка с баннера</td><td class="LongInput"><input name="link" type="text" placeholder="http://yandex.ru"></td></tr>
	<tr class="TRLine1"><td class="VarText">2 Ссылка с баннера</td><td class="LongInput"><input name="link2" type="text" placeholder="http://mail.ru"></td></tr>
	<tr class="TRLine0"><td class="VarText">3 Ссылка с баннера</td><td class="LongInput"><input name="link3" type="text" placeholder="http://google.com"></td></tr>
	<tr class="TRLine1"><td class="Vartext">Внешний счетчик</td><td class="LongInput"><textarea name="outer" style="height:50px;">'.$n["outer"].'</textarea></td></tr>	
	<tr class="TRLine0"><td class="VarText">Начало показов</td><td class="DateInput">'.GetDataSet().'</td></tr>
	<tr class="TRLine1"><td class="Vartext">Конец показов</td><td class="DateInput">'.GetDataSet(time()+60*60*24*30, 1).'</td></tr>
	<tr class="TRLine0"><td class="Vartext">Flash ролик</td><td><input type="file" name="userflash" style="border:2px solid #FFF; border-radius:5px;"></td></tr>
	<tr class="TRLine1"><td class="Vartext">Картинка</td><td><input type="file" name="userpic" style="border:2px solid #FFF; border-radius:5px;"></td></tr>
	<tr class="TRLine0"><td class="Vartext">Мобильный вид</td><td><input type="file" name="usermobile" style="border:2px solid #FFF; border-radius:5px;"></td></tr>
	<tr class="TRLine1"><td class="Vartext">Текст</td><td class="LongInput"><textarea name="btext" style="height:150px;"></textarea></td></tr>
	'."</table></div>";
	
	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Добавить материал'></div>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='C5'></div><div class='C30'></div><h2 id='zcap'>Заявка</h2><div class='RoundText' id='Info'>Укажите номер заявки</div>".$C5.
	"<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' name='st' value='1'></td><td><b>Включить показы</b></td></tr></table></div></form>";
}
}

$_SESSION["Msg"]="";
?>