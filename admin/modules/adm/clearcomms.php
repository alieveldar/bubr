<?
### УДАЛЕНИЕ Комментариев и файлов вложения
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$AdminText='<h2>Удаление всех комментариев пользователя</h2>'; $AdminRight="";
	$table="_comments"; $idp=array(); $ids=array(); $tstart=GetMicroTime(); $tf=0;
	
	
	### ЗАПИСИ САМОГО ПОЛЬЗОВАТЕЛЯ
	$data=DB("SELECT `id` FROM `".$table."` WHERE (`uid`='".$id."')"); $tf=$tf+$data["total"];
	
	if ((int)$data["total"]>0):
	
	for ($i=0; $i<$data["total"]; $i++):
		@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $ids[]=$ar["id"]; $idp[]=$ar["id"];
	endfor;
	DB("DELETE FROM `".$table."` WHERE (`uid`='".$id."')");

	### ЗАПИСИ ДЛЯ КОТОРЫХ РОДИТЕЛЬ ПОТЕРЯН
/*	for ($i=0; $i<7; $i++):
		$data=DB("SELECT `id` FROM `".$table."` WHERE `pid` NOT IN (SELECT `id` FROM `".$table."`)"); $tf=$tf+$data["total"];
		if ((int)$data["total"]!=0) {
			$ids=array();
			for ($j=0; $j<$data["total"]; $j++):
				@mysql_data_seek($data["result"], $j); $ar=@mysql_fetch_array($data["result"]); $ids[]=$ar["id"]; $idp[]=$ar["id"];
			endfor;
			DB("DELETE FROM `".$table."` WHERE (`id` IN (".implode(",", $ids)."))");
		} else {
			break;	
		}
	endfor;
 */
	
	
	### ПО МАССИВУ $idp УДАЛЯЕМ ВСЕ ПРИЛОЖЕННЫЕ ФАЙЛЫ
	if ($tf>0) {
	$data=DB("SELECT `pic` FROM `_commentf` WHERE (`id` IN (".implode(",", $idp)."))");
	for ($i=0; $i<$data["total"]; $i++):
		@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		$tw=$tw+filesize("../../usefiles/picoriginal/".$ar["pic"]);
		@unlink("../../usefiles/picsquare/".$ar["pic"]);
		@unlink("../../usefiles/picpreview/".$ar["pic"]);
		@unlink("../../usefiles/picoriginal/".$ar["pic"]);
	endfor;
	DB("DELETE FROM `_commentf` WHERE (`id` IN (".implode(",", $idp)."))");
	}
	
	endif;
	
	$tend=GetMicroTime();
	$AdminText.="<div class='SuccessDiv'>Комментарии и материалы пользователя удалены</div><div class='C10'></div>";
	$AdminText.="<div class='InfoDiv'>Записей удалено: <b>".$tf."</b></div>";
	$AdminText.="<div class='InfoDiv'>Объем очистки: <b>".round($tw/1024/1024, 3)." МБ</b></div>";
	$AdminText.="<div class='InfoDiv'>Затрачено: <b>".round($tend-$tstart, 3)." сек.</b></div><div class='C15'></div>";
	$AdminText.=AIco(45)."Вернуться: <a href='?cat=adm_usersedit&id=".$id."'>Страница пользователя</a>";
	
}



/*
function GetChild($i, $lvl=-1) {
	global $items, $mvl, $ids; 
	foreach ($items as $key=>$item) { 
		if ($item["pid"]==$items[$i]["id"]) { 
			$pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; }
			if ($key!=0) { GetChild($key, $lvl); }
		}
	}
}
*/
?>