<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table="_menulist";

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;

	if (isset($P["addbutton"])) {
		$res=DB("INSERT INTO `".$table."` (`name`,`link`,`stat`) VALUES ('".DBcut($P["Inp0"])."', '".DBcut($P["Inn0"])."', '".(int)DBcut($P["Inc0"])."')");
		$last=DBL(); $_SESSION["Msg"]="<div class='SuccessDiv'>Раздел навигации создан! <a href='?cat=adm_menuedit&id=".$last."'>Добавить пункты меню</a></div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2>Навигация сайта</h2>';
	$AdminText.=$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundText' id='Tgg'><table><tr class='TRLineC'><td>Переменная</td><td>Название нового меню</td><td>Включено</td></tr>";
	$AdminText.='<tr class="TRLine" id="Line0">
		<td class="SmallInput"><input name="Inn0" type="text" class="JsVerify"></td>
		<td class="LongInput"><input name="Inp0" type="text"></td>
		<td class="CheckInput"><input type="checkbox" id="Inc0" name="Inc0" data-on="да " data-off="нет" value="1" /></td>
	</tr>'; 
	$AdminText.="</table><div class='C5'></div><div class='CenterText'><input type='submit' name='addbutton' id='addbutton' class='SaveButton' value='Добавить меню'></div></div></form>";
	
	### Существующие занятые имена переменных
	$data=DB("SELECT `link` FROM `".$table."`"); $AdminText.="<script type='text/javascript'>var NotAvaliable=new Array("; for ($i=0; $i<$data["total"]; $i++):
	@mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $AdminText.="'".$ar["link"]."',"; endfor; $AdminText.="'error');</script>";

// ПРАВАЯ КОЛОНКА	
	$AdminRight=ATextReplace('Menu-Module');
}
$_SESSION["Msg"]="";
?>