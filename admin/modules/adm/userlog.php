<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg; $onpage=200; $from=($pg-1)*$onpage; $where=""; $dp="";
	
	if ($it=="user") { $where=" WHERE (`_lentalog`.`uid`='".(int)$id."')"; }
	if ($it=="item") { $where=" WHERE (`_lentalog`.`link`='".$pid."' && `_lentalog`.`id`='".(int)$id."')"; }
	if ($where!="")  { $dp="<a href='?cat=".$cat."' style='float:right; background:#E8F3FF; padding:5px 10px; color:#51758C; border:1px solid #4FC6FF; border-radius:6px;'>Сбросить все фильтры</a>"; }

	$q1="SELECT `_lentalog`.*, `_users`.`login`, `_users`.`nick`, `_users`.`role`, `_pages`.`name` FROM `_lentalog` LEFT JOIN `_users` ON `_users`.`id`=`_lentalog`.`uid` LEFT JOIN `_pages` ON `_pages`.`link`=`_lentalog`.`link` ".$where." ORDER BY `_lentalog`.`data` DESC  LIMIT $from, $onpage"; $data=DB($q1);
	$q2="SELECT `_lentalog`.`id` FROM `_lentalog` LEFT JOIN `_users` ON `_users`.`id`=`_lentalog`.`uid` LEFT JOIN `_pages` ON `_pages`.`link`=`_lentalog`.`link` ".$where; $data2=DB($q2);
	
	$pager=Pager($pg, $onpage, ceil($data2["total"]/$onpage));  $AdminText='<h2 style="float:left;">Лог активности</h2>'.$dp."<div class='C10'></div>";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $datan=ToRusData($ar["data"]);
	$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:11px; padding:8px; text-align:left;" ><i>'.$datan[4].'<br><span style="font-size:9px; color:#AAA;">IP: '.$ar["ip"].'</span></i></td>';
		$text.='<td class="Act" width="1%" style="white-space:nowrap; font-size:11px; padding:8px; text-align:left;" ><i><a href="?cat=adm_usersedit&id='.$ar["uid"].'" title="Настройки пользователя">'.$ar["nick"].'</a><br><span style="font-size:9px; color:#AAA;">логин: '.$ar["login"].' ['.$ar["role"].']<br><a href="?cat='.$cat.'&id='.$ar["uid"].'&it=user">отследить</a></span></i></td>';
		if ($ar["id"]!=0) { $text.="<td class='BigText' style='padding:8px; text-align:left; color:#666; font-size:11px;'><i style='margin:0;'><a href='?cat=".$ar["link"]."_list' title='Перейти в раздел'>".$ar["name"]."</a>, 
		<a href='?cat=".$ar["link"]."_edit&id=".$ar["id"]."' title='Перейти к записи'>запись #".$ar["id"]."</a> — <a href='?cat=".$cat."&id=".$ar["id"]."&it=item&pid=".$ar["link"]."'>отследить запись</a></i><div class='C5'></div>".$ar["text"]."</td>";
		} else { $text.="<td class='BigText' style='padding:8px; text-align:left; color:#C00; font-size:11px;'>".$ar["text"]."</td>"; }
	$text.="</tr>";
	endfor; 
	$AdminText.=$pager."<div class='C15'></div><div class='RoundText' id='Tgg'><table><tr class='TRLineC'><td>Дата и время</td><td>Пользователь</td><td>Действие</td></tr>".$text."</table></div>".$pager;

	// ПРАВАЯ КОЛОНКА
	$AdminRight="";
}
$_SESSION["Msg"]="";
?>