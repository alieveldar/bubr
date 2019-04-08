<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) { $id=(int)$id;
	
	$stavka=0.2; 
	
	global $pg; $table="post_users"; $AdminRight=""; $P=$_POST;
	$q="SELECT * FROM `_users` WHERE (`id`=".$id.")"; $data=DB($q);
	if ($data["total"]==0) { $AdminText="Автор не найден. <a href='?cat=moderate_auths'>Вернуться к списку</a>"; } else { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	$bal=DB("SELECT SUM(`money`) as `balance` FROM `_userspays` WHERE (`uid`='$id')"); @mysql_data_seek($bal["result"], $i); $us=@mysql_fetch_array($bal["result"]);
	
	$AdminText="<h2 style='font-size:20px;'>Автор: ".$ar["nick"].", баланс: ".$us["balance"]." рублей</h2>
	• <a href='?cat=adm_usersedit&id=".$id."' target='_blank'><b>Основные настройки пользователя</b></a><div class='C5'></div>
	• <a href='?cat=moderate_read&id=".$id."' target='_blank'><b>Отправить пользователю сообщение</b></a><div class='C5'></div><hr><div class='C5'></div>";

	$AdminText.="<h2>Статьи на наборе проcмотров</h2><div class='RoundText' id='Tgg'><table>"; $data=DB("SELECT `post_users`.`id` as `oldid`, `post_lenta`.`id`, `post_lenta`.`data`, `post_lenta`.`name`, `post_lenta`.`seen` FROM `post_users` LEFT JOIN `post_lenta` ON `post_users`.`lentaid`=`post_lenta`.`id` WHERE (`post_users`.`stat`='2' && `post_users`.`uid`='".$id."' && `post_lenta`.`data`>'".(time()-60*60*24*7)."') ORDER BY `data` DESC");
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $articlesok.="<tr>"; $d=ToRusData($ar["data"]); $articlesok.="<td class='name'><a href='/post/view/".$ar["id"]."' target='_blank'>".$ar["name"]."</a><br><data>ID=".$ar["id"]." / Одобрено: $d[0] / Статус: идет заработок</data></td>";
	$articlesok.="<td class='Act' width='1%' style='white-space:nowrap; font-size:11px;'>Показов: ".$ar["seen"]."</td><td class='Act' width='1%' style='white-space:nowrap; font-size:11px;'>Сумма: ".round($ar["seen"]*$stavka, 1)." руб.</td>"; $articlesok.="</tr>"; endfor; $AdminText.=$articlesok."</table></div>";
	
	$AdminText.="<div class='C15'></div>";
	
	$AdminText.="<h2>Список финансовых операций</h2><div class='RoundText' id='Tgg'><table>"; $data=DB("SELECT * FROM `_userspays` WHERE (`uid`='$id') order by `data` desc"); $mon="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $mon.="<tr>"; $d=ToRusData($ar["data"]); if ((int)$ar["money"]>0) { $ar["money"]="<b style='color:green;'>+".$ar["money"]."</b>"; } else { $ar["money"]="<b style='color:red;'>".$ar["money"]."</b>"; }  
	$mon.="<td class='Act' width='1%' style='white-space:nowrap; font-size:11px;'><data>".$d[4]."</data></td><td class='Act' width='1%' style='white-space:nowrap; font-size:11px;'><data>".$ar["money"]." руб.</data></td><td class='name'>".nl2br($ar["text"])."</td></tr>"; endfor; $AdminText.=$mon."</table></div>";

	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	} 
}

//=============================================
$_SESSION["Msg"]="";
?>