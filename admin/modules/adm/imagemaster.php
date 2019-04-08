<?
### ВЫХОД ИЗ АДМИНКИ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) { $AdminRight=''; $AdminText=''; $i=0; @chmod($_SERVER['DOCUMENT_ROOT']."/userfiles", 0777);

if (!is_dir($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster")) { 
	mkdir($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster", 0777); mkdir($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster/podlog", 0777); mkdir($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster/watermark", 0777); }

$sql=@mysql_query("SHOW TABLES"); $tables=array(); while($row = mysql_fetch_array($sql)) { $tables[]=$row; } if (!in_array("_imagemaster", $tables)) { @mysql_query("CREATE TABLE `_imagemaster` (`id` MEDIUMINT NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `podlogpic` VARCHAR(255) NOT NULL, `typepic` VARCHAR(25) NOT NULL, `typeform` VARCHAR (25) NOT NULL, `posit` VARCHAR (1) NOT NULL, `posx` MEDIUMINT DEFAULT '0' NOT NULL, `posy` MEDIUMINT DEFAULT '0' NOT NULL, `round` MEDIUMINT DEFAULT '0' NOT NULL, `fontsize` MEDIUMINT DEFAULT '20' NOT NULL, `fontcolor` VARCHAR(25) DEFAULT '000000' NOT NULL, `fontfamily` VARCHAR(255) DEFAULT 'Arial' NOT NULL, `fontchars` MEDIUMINT DEFAULT '21' NOT NULL, PRIMARY KEY (`id`));"); }
	
	$AdminText.="<h2>Шаблоны обработки фотографий</h2><div class='RoundText' id='Tgg'><table><tr class='TRLineC'><td>Шаблон</td><td>Папка хранения</td><td>Размер изображения</td><td>Кадрирование</td></tr>";
	foreach($GLOBAL['AutoPicPaths'] as $k=>$v) { $a=explode("-", $v); if ((int)$a[0]==0 || (int)$a[1]==0 || (int)$a[2]==0) { $crop="<i style='color:#999;'>автоматически</i>"; } else { $crop="<i><b>выбор области</b></i>"; } 
	if ($a[0]==0){ $a[0]="<b title='ширина пропорционально высоте'>[пропорция]</b>"; }else{ $a[0]='<b>'.$a[0].'</b> px'; } if ($a[1]==0){ $a[1]="<b title='высота пропорционально ширине'>[пропорция]</b>"; }else{ $a[1]='<b>'.$a[1].'</b> px'; }
	$AdminText.='<tr class="TRLine'.($i%2).'"><td><b>'.$k.'</b></td>'.'</td><td>[ROOT]/userfiles/'.$k.'/</td><td>'.$a[0].' • '.$a[1].'</td><td>'.$crop.'</td></tr>'; $i++; } $AdminText.="</table></div><div class='C20'></div>";

	$AdminText.="<h2 style='float:left;'>Шаблоны социальных иллюстраций</h2><div style='float:right;' class='LinkG'><a href='?cat=adm_imagemasteradd'>Добавить шаблон</a></div>
	<div class='RoundText' id='Tgg'><table><tr class='TRLineC'><td>Шаблон иллюстрации</td><td>Подложка шаблона</td><td>Параметры</td><td colspan='3' width='1%'></td></tr>";
	
	$data=DB("SELECT * FROM `_imagemaster` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
	$AdminText.='<tr class="TRLine'.($i%2).'"><td width="1%"><b><a href="?cat=adm_imagemasteredit&id='.$ar["id"].'">'.$ar["name"].'</a></b>'.$C5.'фото: '.$ar["typepic"].'</td>';
	$AdminText.='<td width="1%"><img src="/userfiles/imagemaster/podlog/'.$ar["podlogpic"].'" height="100"></td>';	
	$AdminText.='<td>Шрифт: '.$ar["fontfamily"].$C5.'Размер: '.$ar["fontsize"].'px / '.$ar["fontchars"].'px'.$C5.'Цвет: <span style="color:#'.$ar["fontcolor"].'">#'.$ar["fontcolor"]."</span> [".$ar["fontcolor"]."]".$C5.'</td>';
	$AdminText.='<td class="Act"><a href="?cat=adm_imagemasteredit&id='.$ar["id"].'" title="Править">'.AIco('28').'</a></td><td class="Act"> </td>';
	$AdminText.='<td class="Act"><a href="?cat=adm_imagemasterdel&id='.$ar["id"].'" title="Удалить">'.AIco('exit').'</a></td></tr>'; endfor;
	$AdminText.="</table></div><div class='C'></div><i style='font-size:11px;'>Настоятельно рекомендуем оформить подложку качественно: нанести на неё водяные знаки, оформить место наложения основной фотографии и внести прочие необходитмые атрибуты. 
	Рекомендуемый размер: ширина - 800-900, высота - 350-450 пикселей.</i>";
	
	/* очистить созданные тестовые фотки */
	$dir = opendir($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster"); while($file = readdir($dir)){ if ($file!="." && $file!=".." && !is_dir($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster/".$file)) { 
	$timer=time()-filemtime($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster/".$file); if ($timer>60*60*24*30) { @unlink($_SERVER['DOCUMENT_ROOT']."/userfiles/imagemaster/".$file); }}}
	

$AdminRight.='<h2>Кадрирование и ресайз</h2>Настроить и добавить свои правила обработки фотографий можно в  modules/standart/Settings.php<br><br>Правила кадрирования фотографий расположены в глобальном массиве $GLOBAL["AutoPicPaths"]<br><br>Настройки стандартных форматов обработки фотографий доступны в <b>Настройки » <a href="?cat=adm_settings">Основные настройки</a></b><br><br><hr><br><h2>Стили иллюстраций</h2>Вы можете создавать стили обработки фотографий для размещения их в социальных сетях и блогах.<br><br>Технология создания социальных шаблонов иллюстраций основана на правилах «<b>Кадрирование и ресайз</b>»<br><br><hr><br><h2>Предупреждение</h2>При изменении или удалении основных стилей обработки фотографий правильная работа модуля не гарантируется.'; }
?>