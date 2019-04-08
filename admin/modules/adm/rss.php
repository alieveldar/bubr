<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table="_rss";
// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$query=""; foreach ($P["Int"] as $key=>$val) { $query.="('".$key."','".DBcut($val)."'),"; }
		$res=DB("INSERT INTO `$table` (`id`, `virtlink`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `virtlink`=values(`virtlink`)");
		$query=""; foreach ($P["Inp"] as $key=>$val) { $query.="('".$key."','".DBcut($val)."'),"; }
		$res=DB("INSERT INTO `$table` (`id`, `reallink`) VALUE ".trim($query,",")." ON DUPLICATE KEY UPDATE `reallink`=values(`reallink`)");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно сохранены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}

	if (isset($P["addbutton"])) {
		$res=DB("INSERT INTO `$table` (`virtlink`,`reallink`) VALUES ('".DBcut($P["Int0"])."', '".DBcut($P["Inp0"])."')");
		$_SESSION["Msg"]="<div class='C20'></div><div class='SuccessDiv'>Данные успешно добавлены!</div>";
		@header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
	
	

// ФОРМА ДОБАВЛЕНИЯ
	$AdminText='<h2 style="float:left;">Список rss-лент</h2>';
	$AdminText.="<div class='LinkG' style='float:right;'><a href='javascript:void(0);' onclick='ToggleBlock(\"#Tgg\");'>Добавить ленту</a></div>".$C5.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
	$AdminText.="<div class='RoundTextHide' id='Tgg'><table><tr class='TRLineC'><td>Ссылка получения ленты</td><td>Ссылка на файл, генерирующий ленту (PHP)</td></tr>";
	$AdminText.='<tr class="TRLine" id="Line0"><td class="LongInput" style="width:50%;"><input type="text" name="Int0" placeholder="/rss.xml"></td>
				<td class="LongInput" style="width:50%;"><input name="Inp0" type="text" class="JsVerify" placeholder="/modules/rss.php"></td></tr>'; 
	$AdminText.="</table><div class='C5'></div><div class='CenterText'><input type='submit' name='addbutton' id='addbutton' class='SaveButton' value='Добавить данные'></div></div></form>";
	
// ВЫВОД ПОЛЕЙ И ФОРМ	
    $data=DB("SELECT `id`,`virtlink`,`reallink`,`lasttime` FROM `$table` ORDER BY `id` ASC");
    if($data["total"]){
    	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post' onsubmit='return JsVerify();'>";
    	$AdminText.="<div class='RoundText'><table>";
    	$AdminText.="<tr class='TRLineC'><td>Ссылка получения ленты</td><td>Ссылка на файл-генератор (PHP)</td><td>Дата обновления</td><td></td><td></td></tr>";
    	### Переменные пользователей	
    	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
            $lasttime=$ar["lasttime"] ? date("d.m.Y H:i", $ar["lasttime"]) : '';        
    		$AdminText.='<tr class="TRLine'.($i%2).'" id="Line'.$ar["id"].'">
    			<td class="LongInput" style="width:40%;"><input name="Int['.$ar["id"].']" value="'.$ar["virtlink"].'"></td>
    			<td class="LongInput" style="width:40%;"><input name="Inp['.$ar["id"].']" value="'.$ar["reallink"].'"></td>
                <td class="LongInput" style="width:15%;" align="center">'.$lasttime.'</td>
                <td class="Act2"><a href="'.$ar["virtlink"].'" target="_blank">'.AIco('play', 'Смотреть файл').'</a></td>'; 
    		$AdminText.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="DeleteVarItem(\''.$ar["id"].'\')" title="Удалить">'.AIco('exit').'</a></td>';
    		$AdminText.='</tr>';
    	endfor;
    	$AdminText.="</table></div>";
    	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
    	$AdminText.="</form>";
    }

// ПРАВАЯ КОЛОНКА	
	$AdminRight="";
}

$_SESSION["Msg"]="";
?>