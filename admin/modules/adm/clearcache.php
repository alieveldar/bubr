<?
### УДАЛЕНИЕ КЭША
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$AdminRight=ATextReplace('Cache-Module'); $AdminText='<h2>Очистка кэша сайта</h2>'; $tf=0; $tw=0; $er=0;
	$tstart=GetMicroTime(); RemoveDir("../cache"); mkdir("../cache"); chmod("../cache", 0777); $tend=GetMicroTime();
	if ($er==0) {
		$AdminText.="<div class='SuccessDiv'>Кэш сайта очищен</div><div class='C10'></div>";
		$AdminText.="<div class='InfoDiv'>Файлов удалено: <b>".$tf."</b></div>";
		$AdminText.="<div class='InfoDiv'>Объем очистки: <b>".round($tw/1024/1024, 3)." МБ</b></div>";
		$AdminText.="<div class='InfoDiv'>Затрачено: <b>".round($tend-$tstart, 3)." сек.</b></div><div class='C15'></div>";
		$AdminText.=AIco(45)."Вернуться: <a href='?cat=adm_cache'>Настройки кэша сайта</a>";
	}
}

### Рекурсивноге удаление директории
function RemoveDir($path) { global $tp, $tf, $AdminText, $tw, $er; if(file_exists($path) && is_dir($path)) { $dirHandle=opendir($path); while (false !== ($file=readdir($dirHandle))) { if ($file!='.' && $file!='..') { $tmpPath=$path.'/'.$file; chmod($tmpPath, 0777); if (is_dir($tmpPath)) { RemoveDir($tmpPath); } else { if(file_exists($tmpPath)) { $tw+=filesize($tmpPath); unlink($tmpPath); $tf++; }}}} closedir($dirHandle); if(file_exists($path)){ rmdir($path); }} else { $AdminText=ATextReplace('ClearCache-Error'); $er=1; }}
?>