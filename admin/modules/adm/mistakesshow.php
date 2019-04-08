<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table="_mistakes";

### Получаем данные страницы в $mistake;
$data=DB("SELECT * FROM `$table` WHERE `id`=$id LIMIT 1"); $text="";
if ($data["total"]!=1) { 
	### Запись не найдена	
	$AdminText=ATextReplace('Item-Module-Error', $id, $table); $GLOBAL["error"]=1;
} else {
	### Запись найдена
	@mysql_data_seek($data["result"], 0); $mistake=@mysql_fetch_array($data["result"]);
	$dir=explode("/", str_replace(array('http://', 'www.'), '', $mistake["link"]));	
	
// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2>Просмотр уведомления об ошибке</h2>'.$_SESSION["Msg"];
	
	$AdminText.='<div class="RoundText" id="Tgg"><table><tr class="TRLine0"><td style="width:20%;"></td><td style="width:80%;"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">URL</td><td class="LongInput"><input id="to" type="text" value="'.$mistake["link"].'"></td></tr>';	
	$AdminText.='<tr class="TRLine1"><td class="VarText">Ошибка</td><td class="LongInput"><textarea id="text" style="outline:none; height:150px;">'.$mistake["text"].'</textarea></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Комментарий</td><td class="LongInput"><textarea id="text" style="outline:none; height:150px;">'.$mistake["comment"].'</textarea></td></tr>';
	$AdminText.='</table></div>';
	
	$AdminRight=$C30.'<div style="float:left;"><a href="'.($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '?cat=adm_mistakes').'">Вернуться назад</a></div>';
	$AdminRight.='<div style="float:right;"><a href="javascript:void(0);" onclick="ItemDelete(\''.$id.'\', \''.($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '?cat=adm_mistakes').'\')">Удалить</a></div>';
	$AdminRight.=$C10.'<div style="float:left;"><a href="?cat='.$dir[1].'_text&id='.$dir[3].'">Редактировать материал</a></div>';
	
}}
$_SESSION["Msg"]="";

?>