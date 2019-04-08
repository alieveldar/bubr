<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"] == 1 && $GLOBAL["database"] == 1) {

	// РАЗДЕЛ
	$data = DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='" . $alias . "') LIMIT 1");
	if ($data["total"] != 1) { $AdminText = ATextReplace('Item-Module-Error', $id, "_pages");
		$GLOBAL["error"] = 1;
	} else {
		@mysql_data_seek($data["result"], 0); $raz = @mysql_fetch_array($data["result"]);

		// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
		$P = $_POST;
		if (isset($P["savebutton"])) {
			foreach ($P["Inp"] as $key => $val) {
				$ch = (int)$P["Inb"]["$key"];
				$cm = (int)$P["Inm"]["$key"];
				$sname=(int)$P["Inn"][$key];
				$name = str_replace("'","\'",$val);
				$text = str_replace("'","\'",$P["PostText"][$key]);
				$auth=$P["Inpa"][$key];
				$authl=$P["Inpl"][$key];
				$q = "UPDATE `_widget_pics` SET `name`='" . $name . "', `showname`='" . $sname . "', `text`='" . $text . "', `author`='" . $auth . "', `authorlink`='" . $authl . "', `sets`='" . $ch . "', `mark`='" . $cm . "' WHERE (`id`='" . (int)$key . "' && pid='" . (int)$id . "')";
				DB($q);
			}
			DB("INSERT INTO `_lentalog` (`link`, `id`, `uid`, `data`, `ip`, `text`) VALUES ('" . $alias . "', '" . $id . "', '" . $_SESSION['userid'] . "', '" . time() . "', '" . $_SERVER['REMOTE_ADDR'] . "', 'Редактирование фотоотчета (report)')");
			$_SESSION["Msg"] = "<div class='SuccessDiv'>Настройки успешно сохранены</div>"; @header("location: " . $_SERVER["REQUEST_URI"]); exit();
		}

		// ВЫВОД ПОЛЕЙ И ФОРМ
		$data = DB("SELECT `name`, `stat` FROM `" . $alias . "_lenta` WHERE (`id`='" . (int)$id . "') LIMIT 1");
		if ($data["total"] != 1) { $AdminText = ATextReplace('ItemError', $raz["shortname"] . " (" . $alias . ")", $id);
			$GLOBAL["error"] = 1;
		} else {
			@mysql_data_seek($data["result"], 0);
			$node = @mysql_fetch_array($data["result"]);
			if ($node["stat"] == 1) { $chk = "checked";
			}

			$AdminText = '<h2>Редактирование: &laquo' . $node["name"] . '&raquo;</h2>' . $_SESSION["Msg"];
			$AdminText .= "<form action='" . $_SERVER["REQUEST_URI"] . "' enctype='multipart/form-data' method='post'><div class='RoundText'>" . '<div id="uploader" class="align-center"></div>';
			$AdminText .= "<div class='Info' align='center'>Вы можете загружать файлы jpg, png, gif до 10М и размером не более 10.000px на 10.000px</div>" . '</div></form>';

			$data = DB("SELECT * FROM `_widget_pics` WHERE (`pid`='" . (int)$id . "' && `link`='" . $alias . "' && `point`='report') ORDER BY rate ASC");
			if ($data["total"] > 0) {
				$AdminText .= "<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/adapters/jquery.js'></script>";
				$AdminText .= "<form action='" . $_SERVER["REQUEST_URI"] . "' enctype='multipart/form-data' method='post'><div class='RoundText'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>";
				for ($i = 0; $i < $data["total"]; $i++) : @mysql_data_seek($data["result"], $i);
					$ar = @mysql_fetch_array($data["result"]);
					if ($ar["stat"] == 1) { $chk0 = "checked"; } else {$chk0 = ""; }
					$img = "<img src='/userfiles/picsquare/" . $ar["pic"] . "' width='140' />";
					$chb = $ar["sets"] == 1 ? "checked" : "";
					$chm = $ar["mark"] == 1 ? "checked" : "";
					$chn = $ar["showname"] == 1 ? "checked" : "";
					
					$AdminText .= '<tr class="TRLine" id="Line' . $ar["id"] . '" style="border-bottom:2px dotted #999;"><td style="padding:15px 0;">
					<div style="float:left; width:150px;">'.$img.'</div>
					<div style="float:left; width:550px;">'.$C5.'
						<span style="display:block; float:left; margin:0 5px 0 0;"><input type="checkbox" id="RS-' . $ar["id"] . '-_widget_pics" value="1" '.$chk0.' /></span><span style="line-height:22px; display:block; float:left;">Показ</span>
						<span style="display:block; float:left; margin:0 5px 0 32px;"><input type="checkbox" name="Inn[' . $ar["id"] . ']" value="1" ' . $chn . '/></span><span style="line-height:22px; display:block; float:left;">Вывод названия</span>
						<span style="display:block; float:left; margin:0 5px 0 32px;"><input type="checkbox" name="Inb[' . $ar["id"] . ']" value="1" ' . $chb . '/></span><span style="line-height:22px; display:block; float:left;">Во всю ширину</span>
						<span style="display:block; float:left; margin:0 5px 0 32px;"><input type="checkbox" name="Inm[' . $ar["id"] . ']" value="1" ' . $chm . '/></span><span style="line-height:22px; display:block; float:left;">Маркер</span>
						'.$C10.'<div class="LongInput"><input name="Inp[' . $ar["id"] . ']" value="' . $ar["name"] . '" placeholder="Название фотографии">'.$C5.'<input name="Inpa[' . $ar["id"] . ']" value="' . $ar["author"] . '" placeholder="Автор фотографии">'.$C5.'<input name="Inpl[' . $ar["id"] . ']" value="' . $ar["authorlink"] . '" placeholder="Источник фотографии">'.$C.'</div>
					</div>
					<div style="float:right; padding-top:35px !important; width:20px;">
						<div class="Act"><input type="checkbox" id="' . $ar["id"] . '" class="selectItem" style="margin-left:3px;"></div>' . $C10 . '
						<div id="Act' . $ar["id"] . '" class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\'' . $ar["id"] . '\', \'' . $ar["pic"] . '\')">' . AIco('exit') . '</a></div>'.$C15.'
						<div class="Act"><a href="javascript:void(0);" onclick="ItemUp(\'' . $ar["id"] . '\')" title="Поднять">' . AIco(3) . '</a></div>'.$C10.'
						<div class="Act"><a href="javascript:void(0);" onclick="ItemDown(\'' . $ar["id"] . '\')" title="Опустить">' . AIco(4) . '</a></div>
					</div>
					<div>'.$C15."<textarea name='PostText[" . $ar["id"] . "]' id='textedit" . $ar["id"] . "' style='outline:none;' class='texteditors'>" . $ar["text"] . "</textarea>".$C.'</div>
					</td></tr>';
				endfor;
			$AdminText .= "</table>".$C15."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить настройки'></div></div>";
			}

			// ПРАВАЯ КОЛОНКА
			$AdminRight = "<br><br>
	<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-" . $id . "-" . $alias . "_lenta' " . $chk . "></td><td><b>Опубликовано</b></td></tr></table></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_photo&id=".$id."'>Основная фотография</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu2'><a href='?cat=".$alias."_report&id=".$id."'>Виджет: Фото-отчет</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_album&id=".$id."'>Виджет: Фото-альбом</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_film&id=".$id."'>Виджет: Видео-вставка</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_voting&id=".$id."'>Виджет: Голосование</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pretext&id=".$id."'>Виджет: Постскриптум</a></div>
	<br><div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div><br><br>
	<div class='SecondMenu2'><a href='/$alias/view/$id/' target='_blank'>Просмотр на сайте</a></div></form>";
			if ($_SESSION['userrole'] > 2) { $AdminRight .= "<div class='SecondMenu'><a href='?cat=" . $alias . "_log&id=" . $id . "'>Лог редактирования записи</a></div>";
			}

		}
	}
}
$_SESSION["Msg"] = "";
?>