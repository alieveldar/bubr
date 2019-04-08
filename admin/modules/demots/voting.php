<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$table=$alias."_lenta"; $table2="_widget_pics"; $table3="_widget_votes";
	@require_once 'demot-create.php';

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		$ar=explode(".", $P["ddata1"]); $sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
		
		$q="UPDATE `".$alias."_lenta` SET 
		`voting`=".$P["voting"].",
		`elemsstyle`=".$P["elemsstyle"].",
		`votingend`='".$sdata1."',
		`winnerscount`=".$P["winnerscount"]." 
		WHERE (id='".(int)$id."')";
		
		///echo $q;
		DB($q); 
		foreach ($P["PostPic"] as $key=>$val) {			
			$name=$P["PostName"][$key]; $text=$P["PostText"][$key]; $author=$P["PostAuthor"][$key]; $point=$P["PostPoint"][$key]; $pic=$val;
			$q=$q="UPDATE `_widget_pics` SET `name`='".$name."', `text`='".$text."', `author`='".$author."', `point`='".$point."' WHERE (`id`='".(int)$key."' && pid='".(int)$id."')"; DB($q);
			$name=$name ? $name : 'Заголовок';
			$text=$text ? $text : 'Описание'; 
			$author=$author ? 'Автор: '.$author : 'Автор';  
			demotivator($ROOT.'/userfiles/picoriginal/'.$pic, $ROOT.'/userfiles/demots/'.$pic.'.jpg', $name, $text, $author, $VARS["mdomain"]);
		}
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}


	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT * FROM `$table` WHERE (`id`=".(int)$id.") LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	if (!$node["votingend"]) $node["votingend"] = time() + 7 * 24 * 60 * 60;
	if ($node["stat"]==1) { $chk="checked"; }
	if ($node["voting"]==0) { $v1="selected"; } elseif ($node["voting"]==1) { $v2="selected"; }
	if ($node["elemsstyle"]==3) { $vc1="selected"; } elseif ($node["elemsstyle"]==2) { $vc2="selected"; } else { $vc3="selected"; }
	if ($node["winnerscount"]==1) { $vw1="selected"; } elseif ($node["winnerscount"]==2) { $vw2="selected"; } else { $vw3="selected"; }	
		
	$AdminText='<h2>Голосование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.='<form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post">';

	### Основные данные
	$AdminText.='<div class="RoundText"><table><tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Могут голосовать</td><td class="LongInput"><div class="sdiv"><select name="voting"><option value="0" '.$v1.'>Все пользователи</option><option value="1" '.$v2.'>Только зарегистрированные</option></select></div></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Кол-во элементов в ряду</td><td class="LongInput"><div class="sdiv"><select name="elemsstyle"><option value="3" '.$vc1.'>3</option><option value="2" '.$vc2.'>2</option><option value="1" '.$vc3.'>1</option></select></div></td><tr>';
	$AdminText.='<tr class="TRLine0"><td class="VarText">Кол-во победителей</td><td class="LongInput"><div class="sdiv"><select name="winnerscount"><option value="1" '.$vw1.'>1</option><option value="2" '.$vw2.'>2</option><option value="3" '.$vw3.'>3</option></select></div></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Дата окончания</td><td class="DateInput">'.GetDataSet($node["votingend"]).'</td><tr>';
	$AdminText.='</table>';	
	$AdminText.='</div>';
	
	if($GLOBAL["now"] > $node["votingend"]) {
		$data=DB("SELECT `".$table2."`.*, COUNT(`".$table3."`.id) as `cnt` FROM `".$table2."` LEFT JOIN `".$table3."` ON `".$table3."`.`vid`=`".$table2."`.`id` WHERE (`".$table2."`.`link`='".$alias."' AND `".$table2."`.`pid`=".$id.") GROUP BY 1 ORDER BY `cnt` DESC LIMIT ".$node['winnerscount']);		
		$AdminText.='<div class="C30"></div><h2 class="CenterText">Голосование окончено. ';
		$AdminText.=$node['winnerscount'] == 1 ? 'Победитель:</h2>' : 'Победители:</h2>';		
		$AdminText.='<div class="CBG"></div>';	
		if($data['total']){
			$items = '';
			for ($i=0, $j=0; $i<$data["total"]; $i++){
				@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
				if($ar["cnt"] == 0) continue;
				
				$items.='<li>';
				$items.='<div class="votingAuthor">Автор: '.$ar["author"].'</div>';
				$items.='<div class="votingImg"><a href="javascript:void(0);" title=\''.$ar["name"].'\' onclick=\'ViewBlank("", "<img src=/userfiles/demots/'.$ar["pic"].'.jpg />");\'><img title=\''.$ar["name"].'\'  src="/userfiles/demots/'.$ar['pic'].'.jpg" border="0" /></a></div>';
				$items.='<div class="votingButton">';												
				$items.='<strong>Голосов: <span class="votes">'.$ar["cnt"].'</span></strong>';			
				$items.='</div></li>';
				$j++;
			}
			
			$AdminText.='<ul class="voting count'.$j.'">'.$items.'</ul>'.$C;
		}
	}

	$AdminText.='<h2>Фото номинантов</h2><div class="RoundText"><div id="uploader" class="align-center"></div><div class="Info" align="center">Вы можете загружать файлы jpg, png, gif до 10М и размером не более 10.000px на 10.000px</div></div>';
	
	$data=DB("SELECT * FROM `_widget_pics` WHERE (`pid`='".(int)$id."' && `link`='".$alias."') ORDER BY rate ASC");
	if ($data["total"]>0) {
		$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
		$AdminText.="<div class='RoundText'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>";
		for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); if ($ar["stat"]==1) { $chk0="checked"; }
			
			if (!is_dir($ROOT.'/userfiles/demots')) { mkdir($ROOT.'/userfiles/demots', 0777); }
			if(!file_exists($ROOT.'/userfiles/demots/'.$ar['pic'].'.jpg')) demotivator($ROOT.'/userfiles/picoriginal/'.$ar['pic'], $ROOT.'/userfiles/demots/'.$ar['pic'].'.jpg', '', '', 'Автор', $VARS["mdomain"]);				
			
			$img='<a href="javascript:void(0);" onclick=\'ViewBlank("", "<img src=/userfiles/demots/'.$ar["pic"].'.jpg />");\' title="Посмотреть"><img src="/userfiles/demots/'.$ar['pic'].'.jpg" width="150" /></a>';
			
			$AdminText.='<tr class="TRLine" id="Line'.$ar["id"].'" style="border-bottom:2px dotted #CCC;">
			<td class="LongInput" style="width:10%;" valign="top" align="center">'.$img.$C10.'<input type="checkbox" id="RS-'.$ar["id"].'-_widget_pics" value="1" '.$chk0.' /></td>
			<td class="LongInput" style="width:80%;" valign="top">';
			$AdminText.='<input name="PostName['.$ar["id"].']" value="'.$ar["name"].'" placeholder="Заголовок">'.$C5;
			$AdminText.='<input name="PostText['.$ar["id"].']" value="'.$ar["text"].'" placeholder="Описание">'.$C5;
			$AdminText.='<input name="PostAuthor['.$ar["id"].']" value="'.$ar["author"].'" placeholder="Автор">'.$C5;
			$AdminText.='<input name="PostPoint['.$ar["id"].']" value="'.$ar["point"].'" style="width:100px;"> Баллы при голосовании'.$C5;
			$AdminText.='<input name="PostPic['.$ar["id"].']" value="'.$ar["pic"].'" type="hidden">';
			$AdminText.='</td><td style="padding-top:10px !important;" valign="top">
				<div class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></div>'.$C15.'
				<div id="Act'.$ar["id"].'" class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$ar["pic"].'\')">'.AIco('exit').'</a></div>'.$C25.'
				<div  class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\')" title="Поднять">'.AIco(3).'</a></div>'.$C15.'
				<div  class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\')" title="Опустить">'.AIco(4).'</a></div>
			</td>';
			$AdminText.='</tr>';
		endfor;
		$AdminText.="</table>".$C10."</div>";
	}
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить'></div></form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_lenta' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}
$_SESSION["Msg"]="";
?>