<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	
	if (!is_file("banners-sets.dat")) { $AdminText="Не найден файл &laquo;<b>/admin/banners-sets.dat</b>&raquo; создайте его вручную и поставьте права 0777"; $GLOBAL["error"]=1; } else {
	$sets=explode("|", @file_get_contents("banners-sets.dat")); $table3=$sets[0]."_items";

	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT * FROM `_banners_items` WHERE (`id`='".(int)$id."')"); @mysql_data_seek($data["result"], 0); $n=@mysql_fetch_array($data["result"]);
	$AdminText='<h2>Статистика: '.$n["name"].'</h2>'.$_SESSION["Msg"].$C5;
	
	/* Выбор даты */ if ((int)$d1==0) { $d1=date("Y.m.d", time()-60*60*24*31); } if ((int)$d2==0) { $d2=date("Y.m.d"); } $hs=1; $d11=ToRusDataAlt($d1); $d22=ToRusDataAlt($d2);
	$AdminText.="<div class='RoundText'><table style='width:30%;'><tr><td width=1%>Начальная дата</td><td class='DateInput'>".'<input id="datepick" type="text" readonly value="'.$d11[5].'">'."</td><td width=1%>Конечная дата</td><td class='DateInput'>".'<input id="datepick1" type="text" readonly value="'.$d22[5].'">'."</td><td width=1%><span class='LinkB'><a href='javascript:void(0);' onclick='LoadStat();'>Обновить данные</a></span></td></tr></table></div>".$C;
	
	### Обработка статистики
	//echo "SELECT * FROM `_banners_stat` WHERE (`bid`='$id' && `data`>='$d1' && `data`<='$d2') order by `data` ASC";
	$q=DB("SELECT * FROM `_banners_stat` WHERE (`bid`='$id' && `data`>='$d1' && `data`<='$d2') order by `data` ASC");
	if ($q["total"]==0) { $stat="<tr><td><i>Нет статистики за выбранный период</i></td></tr>"; $hs=0; } else { $mc=0; $ms=0; $ss=0; $sc=0; 
		for($i=0; $i<$q["total"]; $i++) { @mysql_data_seek($q["result"], $i); $ar=@mysql_fetch_array($q["result"]); 	if ($ar["c"]>$mc) { $mc=$ar["c"]; } if ($ar["s"]>$ms) { $ms=$ar["s"]; }	$ss=$ss+$ar["s"]; $uss=$uss+$ar["us"]; $cc=$cc+$ar["c"]; $ucc=$ucc+$ar["uc"];}
		for($i=0; $i<$q["total"]; $i++)
		{
			@mysql_data_seek($q["result"], $i); $ar=@mysql_fetch_array($q["result"]); $d=ToRusDataAlt($ar["data"]); $d1=""; $d2=""; // $ws=round($ar["s"]/$ms*200)+5; $d1="<div style='height:5px; width:".$ws."px; margin:5px 0 3px 0;' class='B11'></div>"; $wc=round($ar["c"]/$mc*200)+5;$d2="<div style='height:5px; width:".$wc."px; margin:3px 0 5px 0;' class='B22'></div>";
			$stat.="<tr  class='TRLineHR VarText'><td>$d[3]</td><td class='B1'>$ar[s]</td><td class='B2'>$ar[c]</td><td class='B3'>".round($ar["c"]/($ar["s"]+1)*100, 2)."% </td><!--<td>$d1$d2</td>--></tr>"; 
		}
	}
	### Основные данные
	if ($hs==1) { $stat="<tr class='TRLineB'><td align=center width=10%>Дата</td><td align=center class='B11'>Просмотры</td><td align=center class='B22'>Переходы</td><td align=center class='B33'>CTR</td><!--<td width=20% align=center>График изменений по дате</td>--></tr>".$stat; }
	if ($hs==1) { $stat.="<tr class='TRLineB'><td align=right>ВСЕГО:</td><td width=5% class='B11'>$ss</td><td width=5% class='B22'>$cc</td><td width=5% class='B33'>".round($cc/($ss+1)*100, 2)."% </td><!--<td width=20%></td>--></tr>"; }
	$AdminText.="<div class='RoundText2'><input id='zay' type='hidden' value='$n[zid]'><table>$stat</table><script>LoadZayavka();</script></div>";
	
// ПРАВАЯ КОЛОНКА
	if ($n["stat"]==1) { $chkt="checked"; } else { $chkt=""; } $AdminRight="<h2 id='zcap'>Заявка</h2><div class='RoundText' id='Info'>Загрузка заявки</div>".$C5.
	"<div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-_banners_items' ".$chkt."></td><td><b>Включить показы</b></td></tr></table></div>".$C10;
	$AdminRight.="<h2>Файлы:</h2><div class='RoundText'>";
		if ($n['flash']!="") { $AdminRight.="<b>Flash</b>: <a target='_blank' href='/advert/files/flash/$n[flash]'>$n[flash]</a>"; } else { $AdminRight.="<b>Flash</b>: НЕТ"; } $AdminRight.="<hr>";
		if ($n['pic']!="") { $AdminRight.="<b>Фото</b>: <a target='_blank' href='/advert/files/image/$n[pic]'>$n[pic]</a>"; } else { $AdminRight.="<b>Фото</b>: НЕТ"; } $AdminRight.="<hr>";
		if ($n['mobile']!="") { $AdminRight.="<b>Моб.</b>: <a target='_blank' href='/advert/files/mobile/$n[mobile]'>$n[mobile]</a>"; } else { $AdminRight.="<b>Моб.</b>: НЕТ"; }
	$AdminRight.="</div>".$C10;
	$AdminRight.="<div class='LinkR'><a href='/advert/preBanner.php?id=$id' target='_blank'>Предварительный просмотр</a></div>";
}
}

$_SESSION["Msg"]="";
?>