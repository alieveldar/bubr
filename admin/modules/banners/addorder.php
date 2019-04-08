<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	
if (!is_file("banners-sets.dat")) { $AdminText="Не найден файл &laquo;<b>/admin/banners-sets.dat</b>&raquo; создайте его вручную и поставьте права 0777"; $GLOBAL["error"]=1; } else {
$sets=explode("|", @file_get_contents("banners-sets.dat")); $table3=$sets[0]."_items";

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		if ((int)$P["zay"]==0) { $_SESSION["Msg"]="<div class='ErrorDiv'>Обязательно введите номер заявки!</div>"; @header("location: ?cat=banners_addorder"); exit();
		} elseif ((int)$P["type"]==0) { $_SESSION["Msg"]="<div class='ErrorDiv'>Обязательно выберите форму рекламы!</div>"; @header("location: ?cat=banners_addorder"); exit();
		} elseif ((int)$P["comp"]==0) { $_SESSION["Msg"]="<div class='ErrorDiv'>Обязательно выберите компанию!</div>"; @header("location: ?cat=banners_addorder"); exit();
		} else {
			$ar=explode(".", $P["ddata1"]); $sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
			$ar=explode(".", $P["ddata11"]); $sdata2=mktime($P["ddata21"], $P["ddata31"], $P["ddata41"], $ar[1], $ar[0], $ar[2]);
			$pls=","; foreach ($P["pl"] as $key=>$val) { $pls.=$key.","; } if (in_array(9999, explode(",", trim($pls,",")))!==false || $pls==",") { $pls=",9999,"; }
			$q="INSERT INTO `_banners_orders` (`zid`, `pid`, `cid`, `did`, `datafrom`, `datato`, `text`, `stat`) VALUES ('".(int)$P["zay"]."', '".(int)$P["type"]."', '".(int)$P["comp"]."', '".$pls."', '".$sdata1."', '".$sdata2."', '".$P["atext"]."', '1');";
			$data=DB($q); $last=DBL(); $_SESSION["Msg"]="<div class='SuccessDiv'>Новая заявка успешно добавлена!</div>"; @header("location: ?cat=banners_addorder"); exit();
		}
	}
	

// ВЫВОД ПОЛЕЙ И ФОРМ
	$places="<div class='BannerPlace'><input type='checkbox' name='pl[9999]' id='pl[9999]'> <u>Сквозное размещение</u></div><div class='BannerPlace'><input type='checkbox' name='pl[0]' id='pl[0]'> <u>Основной домен</u></div>"; 
		$data=DB("SELECT `id`, `name` FROM `_domains` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); 
		$places.="<div class='BannerPlace'><input type='checkbox' name='pl[$ar[id]]' id='pl[$ar[id]]'> $ar[name]</div>"; endfor;  
	$type[0]="- Выберите положение -"; $data=DB("SELECT `id`, `name`, `width`, `height` FROM `_banners_pos` WHERE (`stat`='1') ORDER BY `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $type[$ar["id"]]=$ar["name"].", $ar[width] на $ar[height]";	endfor;
	$comp[0]="- Выберите компанию -"; $data=DB("SELECT `id`, `name` FROM `$table3` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $comp[$ar["id"]]=$ar["name"]; endfor;

	$AdminText='<h2>Добавление заявки на размещение рекламных форм</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine1"><td class="VarText">Номер заявки<star>*</star></td><td class="LongInput"><input name="zay" id="zay" type="text" class="JsVerify2"></td><tr>
	<tr class="TRLine0"><td class="VarText">Размещение</td><td class="CheckInput">'.$places.'</td><tr>
	<tr class="TRLine1"><td class="VarText">Форма рекламы</td><td class="LongInput"><div class="sdiv"><select name="type">'.GetSelected($type, 0).'</select></div></td><tr>
	<tr class="TRLine0"><td class="VarText">Компания</td><td class="LongInput"><div class="sdiv"><select name="comp">'.GetSelected($comp, 0).'</select></div></td><tr>
	<tr class="TRLine1"><td class="VarText">Начало показов</td><td class="DateInput">'.GetDataSet().'</td><tr>
	<tr class="TRLine0"><td class="Vartext">Конец показов</td><td class="DateInput">'.GetDataSet(0, 1).'</td><tr>
	<tr class="TRLine1"><td class="VarText">Примечания</td><td class="LongInput"><textarea name="atext"></textarea></td><tr>
	'."</table></div>";
	
	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Добавить заявку'></div></form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="";
}
}

$_SESSION["Msg"]="";
?>