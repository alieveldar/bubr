<?
$ROOT = $_SERVER['DOCUMENT_ROOT'];

### Очищаем временную папку от файлов старше 10 дней
$i=0; $now=time(); $dh = opendir($ROOT.'/userfiles/temp'); while ($file=readdir($dh)): if ($file!="." && $file!="..") { $r=$now-filemtime($ROOT."/userfiles/temp/".$file);
if ($r>60*60*24*10) { $i++; @unlink($ROOT."/userfiles/temp/".$file); }} endwhile; closedir($dh); $cronlog.="Очистка папки с временными файлами. Удалено файлов: <b>".$i."</b><br>"; 
?>