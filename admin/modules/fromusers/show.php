<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table=$alias."_nodes";
$table2=$alias."_cats";

### Получаем данные страницы в $node;
$data=DB("SELECT `$table`.*, `$table2`.`name` as catn, `$table2`.`id` as cid FROM `$table` LEFT JOIN `$table2` ON `$table2`.`id`=`$table`.`cat` WHERE `$table`.`id`=$id LIMIT 1"); $text="";
if ($data["total"]!=1) { 
	### Запись не найдена	
	$AdminText=ATextReplace('Item-Module-Error', $id, $table); $GLOBAL["error"]=1;
} else {
	### Запись найдена
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);	
	
// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2>Просмотр заявки от пользователя</h2>'.$_SESSION["Msg"];
	
	$AdminText.='<div class="RoundText" id="Tgg"><table><tr class="TRLine0"><td style="width:20%;"></td><td style="width:80%;"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Название</td><td class="LongInput"><input id="to" type="text" value="'.$node["name"].'"></td></tr>';	
	$AdminText.='<tr class="TRLine1"><td class="VarText">Текст</td><td class="LongInput"><textarea id="text" style="outline:none; height:400px;">'.$node["text"].'</textarea></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Автор</td><td class="LongInput"><input id="subject" type="text" value="'.$node["author"].'"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Контакты</td><td class="LongInput"><input id="subject" type="text" value="'.$node["contacts"].'"></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Категория</td><td class="LongInput"><input id="subject" type="text" value="'.$node["catn"].'"></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Изображения</td><td class="LongInput">';
	if($node["pics"]){
		$AdminText.='<ol class="fromusersPics">';
		$pics=explode('|', $node["pics"]);
		foreach($pics as $pic){
			$AdminText.='<li><a href="/userfiles/temp/'.$pic.'" target="_blank">'.$pic.'</a></li>';
		}
		$AdminText.='</ol>';
	}
	$AdminText.='</td></tr>';
	$AdminText.='</table></div>';
	
	$AdminRight=$C20.'<div style="float:left;"><a href="'.$_SERVER['HTTP_REFERER'].'">Вернуться назад</a></div>';
	$AdminRight.='<div style="float:right;"><a href="javascript:void(0);" onclick="ItemDelete(\''.$id.'\', \''.$table.'\', \''.$_SERVER['HTTP_REFERER'].'\')">Удалить</a></div>';
	
}}
$_SESSION["Msg"]="";

?>