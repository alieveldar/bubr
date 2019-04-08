<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	# save
	if (isset($P["savebutton"])) {
		$ch=(int)$P["Inb"];
		foreach ($P["Inp"] as $key=>$val) {
			$name=$val; $text=$P["PostText"][$key]; 
			$q="UPDATE `_widget_pics` SET `name`='".$name."', `text`='".$text."', `sets`='".$ch."' WHERE (`id`='".(int)$key."' && pid='".(int)$id."')"; DB($q);
		}
		$_SESSION["Msg"]="<div class='SuccessDiv'>Настройки успешно сохранены</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}	
	
	
	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT `name`, `stat` FROM `".$alias."_items` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]); if ($node["stat"]==1) { $chk="checked"; }
	
	$AdminText='<h2>Фотографии: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><div class='RoundText'>".'<div id="uploader" class="align-center"></div>';
	$AdminText.="<div class='Info' align='center'>Вы можете загружать файлы jpg, png, gif до 10М и размером не более 10.000px на 10.000px</div>".'</div></form>';
	
	$data=DB("SELECT * FROM `_widget_pics` WHERE (`pid`='".(int)$id."' && `link`='".$alias."' && `point`='pics') ORDER BY rate ASC");
	if ($data["total"]>0) {  @mysql_data_seek($data["result"],0); $ar=@mysql_fetch_array($data["result"]); $chb=$ar["sets"]==1?"checked":"";
		$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/adapters/jquery.js'></script>";
		$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><div class='RoundText'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>";
		$AdminText.='<tr class="TRLine" style="border-bottom:2px dotted #CCC;"><td colspan="3"><span style="display:block; float:left; margin-right:5px;"><input type="checkbox" name="Inb" value="1" '.$chb.'/></span><span style="line-height:22px; display:block; float:left;">Выводить фотографии с описанием</span></td></tr>';
		for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); if ($ar["stat"]==1) { $chk0="checked"; }
			$img="<img src='/userfiles/picpreview/".$ar["pic"]."' width='150' />";
			$AdminText.='<tr class="TRLine" id="Line'.$ar["id"].'" style="border-bottom:2px dotted #CCC;">
			<td class="LongInput" style="width:10%;" valign="top" align="center" >'.$img.$C10.'
			<span style="display:block; float:left; margin-right:5px;"><input type="checkbox" id="RS-'.$ar["id"].'-_widget_pics" value="1" '.$chk0.' /></span><span style="line-height:22px; display:block; float:left;">Показывать</span></td>
			<td class="LongInput" style="width:80%;" valign="top"><input name="Inp['.$ar["id"].']" value=\''.$ar["name"].'\' placeholder="Название фотографии">'.$C5;
			$AdminText.="<textarea name='PostText[".$ar["id"]."]' id='textedit".$ar["id"]."' style='outline:none;' class='texteditors'>".$ar["text"]."</textarea>";
			$AdminText.='</td><td style="padding-top:10px !important;" valign="top">
				<div class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></div>'.$C15.'
				<div id="Act'.$ar["id"].'" class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$ar["pic"].'\')">'.AIco('exit').'</a></div>'.$C25.'
				<div  class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\')" title="Поднять">'.AIco(3).'</a></div>'.$C15.'
				<div  class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\')" title="Опустить">'.AIco(4).'</a></div>
			</td>';
			$AdminText.='</tr>';
		endfor;
		$AdminText.="</table>".$C15."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить настройки'></div></div>";
	}


// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_contacts&id=".$id."'>Контакты и часы работы</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_pics&id=".$id."'>Фотографии компании</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_consults&id=".$id."'>Консультации</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_actions&id=".$id."'>Акции</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_items' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}
$_SESSION["Msg"]="";
?>