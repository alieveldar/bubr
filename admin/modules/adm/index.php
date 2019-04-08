<?
### ГЛАВНАЯ АДМИНКИ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	
	#############################################################################################
	### ЦЕНТРАЛЬНАЯ КОЛОНКА #####################################################################
	#############################################################################################
	
	$statrefresh=10;
	
	$data=DB("SELECT `sets` FROM `_pages` WHERE (`link`='users') LIMIT 1"); @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $us=explode("|", $ar["sets"]);	
	if (file_exists($_SERVER['DOCUMENT_ROOT']."/admin/index-page.stat") && (time()-filemtime($_SERVER['DOCUMENT_ROOT']."/admin/index-page.stat"))<(60*$statrefresh)) {
		$AdminText="<div style='color:#888; font-size:11px; float:right;'>[загружено из кэша]</div>".@file_get_contents($_SERVER['DOCUMENT_ROOT']."/admin/index-page.stat");
	} else {
	$indays=90; $monthago=time()-60*60*24*$indays; $d1=ToRusData(time()); $d2=ToRusData($monthago); $d3=date("Y.m.d", $monthago);
	$AdminText="<h2 style='margin:0; padding:0;'><h2 style='margin:0; padding:0;'>Статистика публикаций: ".$d2[2]." - ".$d1[2]."</h2><div class='C10'></div><div class='scrollablediv'><table cellspacing='0' style='border-collapse:collapse;'>";
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$r=mysql_query("SHOW TABLES"); if (mysql_num_rows($r)>0) {
		// сбор статистики по всем разделам
		$items=array(); $names=array(); $datas=array(); $total=0; while($row = mysql_fetch_array($r, MYSQL_NUM)) { $table = $row[0]; if (strpos($table, "_lenta")!==false) { list($lnk, $tp)=explode("_", $table);
			$q="SELECT COUNT(`$table`.`id`) as `cnt`, FROM_UNIXTIME(`$table`.`data`,'%Y.%m.%d') as `datar`, `_pages`.`link`, `_pages`.`shortname` as `name` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='".$lnk."' WHERE (`$table`.`stat`='1' && `$table`.`data`>'".$monthago."') GROUP BY `datar` ORDER BY `datar` DESC"; $data=DB($q);
			for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $names[$ar["link"]]=$ar["name"]; $items[$ar["link"]][$ar["datar"]]=$ar["cnt"]; $total=$total+$ar["cnt"]; $datas[$ar["datar"]]=$datas[$ar["datar"]]+$ar["cnt"]; endfor;
		}}
		### Создание строк месяцев и дней
		$ms=array(); for($i=$monthago; $i<=time(); $i=$i+86400): $tmp=ToRusData($i); $d=explode(" ", $tmp[2]); $ms[$d[1]]++; endfor; $m=""; $d=""; $lm="";
		for($i=$monthago; $i<=time(); $i=$i+86400): $tmp=ToRusData($i); $dp=explode(" ", $tmp[2]); $cm=(date("m", $i))+0; $idd=date("Y.m.d", $i); $ind=$datas[$idd]; $tl.="<td ".hd($i).">".(int)$ind."</td>";
		if ($dp[1]!=$lm) { $lm=$dp[1]; $m.="<td colspan='".($ms[$dp[1]])."' class='Moth".$cm."'>$dp[1], $dp[2]</td>"; } $d.="<td class='Moth".$cm."' title='$tmp[2]' ".hd($i).">".(int)$dp[0]."</td>";  endfor;
		$calendar="<tr class='InfoZayMoths'><td class='Moth1' rowspan='2'>Всего за период, по всем разделам: <b>".$total."</b></td>".$m."<td class='Moth1' rowspan='2'><b>Сумма</b></td></tr><tr class='InfoZayDays'>".$d."</tr>";
		$itog="<tr class='InfoZayMoths Moth1'><td style='padding:8px !important; text-align:left !important;'>Сумма по дням периода:</td>".$tl."<td>".$total."</td></tr>";
		// вывод статистики по всем разделам
		$AdminText.=$calendar; $i=0; asort($names); foreach($names as $lnk=>$name) {
			$AdminText.='<tr class="TRLine TRLine'.($i%2).' InfoZayStat">'."<td style='white-space:nowrap; padding:8px !important; text-align:left !important;' width='1%'><a href='?cat=".$lnk."_list'>$name</a></td>";
			$AdminText.=GetDataPublishSetka($lnk); $AdminText.='</tr>'; $i++;
		} $AdminText.=$itog;
	}
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	if ($us[3]==0){ $AdminText.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">Комментарии отключены...</h2></td></tr>'; } else {
		
	$table="_comments"; $total=0; $max=0; $min=999999; $coms=array(); $text1=""; $text2=""; $q="SELECT COUNT(`$table`.`id`) as `cnt`, FROM_UNIXTIME(`$table`.`data`,'%Y.%m.%d') as `datar` FROM `$table` WHERE (`$table`.`data`>'".$monthago."') GROUP BY `datar` ORDER BY `datar` DESC"; $data=DB($q);
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $ar["cnt"]=(int)$ar["cnt"]; $coms[$ar["datar"]]=$ar["cnt"]; if ($ar["cnt"]>$max) { $max=$ar["cnt"]; } if ($ar["cnt"]<$min) { $min=$ar["cnt"]; } $total=$total+$ar["cnt"]; endfor;
	for($i=$monthago; $i<=time(); $i=$i+86400):	$d=date("Y.m.d", $i); if ($max>0) { $h=round(($coms[$d]/$max)*60)+10; } else { $h=10; } $text1.="<td align='center' valign='bottom'><div style='width:100%; height:".$h."px;' class='Moth12'></div></td>"; $text2.="<td align='center' ".hd($i).">".(int)$coms[$d]."</td>"; endfor;
	$AdminText3.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">Комментирование материалов</h2></td></tr>
	<tr class="InfoZayStat"><td style="white-space:nowrap; font-size:11px; padding:8px !important; text-align:left !important;">Максимум: <b>'.$max.'</b><br><br>Минимум: <b>'.$min.'</b><br><br>Среднее: <b>'.round($total/$indays).'</b><br><br>Всего: <b>'.$total.'</b></td>'.$text1.'<td></td></tr>
	<tr class="InfoZayMoths Moth1"><td style="white-space:nowrap; padding:8px !important; text-align:left !important;">Сумма по дням периода:</td>'.$text2.'<td><b>'.$total.'</b></td></tr>';
	if ($total!=0) { $AdminText.=$AdminText3;} else { $AdminText.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">За текущий период нет комментариев...</h2></td></tr>'; }}
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	if ($us[0]==0){ $AdminText.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">Пользователи отключены...</h2></td></tr>'; } else {
	
	$table="_users"; $total=0; $max=0; $min=999999; $coms=array(); $text1=""; $text2=""; $q="SELECT COUNT(`$table`.`id`) as `cnt`, FROM_UNIXTIME(`$table`.`created`,'%Y.%m.%d') as `datar` FROM `$table` WHERE (`$table`.`created`>'".$monthago."' && `$table`.`stat`='1') GROUP BY `datar` ORDER BY `datar` DESC"; $data=DB($q);
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $ar["cnt"]=(int)$ar["cnt"]; $coms[$ar["datar"]]=$ar["cnt"]; if ($ar["cnt"]>$max) { $max=$ar["cnt"]; } if ($ar["cnt"]<$min) { $min=$ar["cnt"]; } $total=$total+$ar["cnt"]; endfor;
	for($i=$monthago; $i<=time(); $i=$i+86400):	$d=date("Y.m.d", $i); if ($max>0) { $h=round(($coms[$d]/$max)*60)+10; } else { $h=10; } $text1.="<td align='center' valign='bottom'><div style='width:100%; height:".$h."px;' class='Moth2'></div></td>"; $text2.="<td align='center' ".hd($i).">".(int)$coms[$d]."</td>"; endfor;
	$AdminText1.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">Количество пользователей</h2></td></tr>
	<tr class="InfoZayStat"><td style="white-space:nowrap; font-size:11px; padding:8px !important; text-align:left !important;">Максимум: <b>'.$max.'</b><br><br>Минимум: <b>'.$min.'</b><br><br>Среднее: <b>'.round($total/$indays).'</b><br><br>Всего: <b>'.$total.'</b></td>'.$text1.'<td></td></tr>
	<tr class="InfoZayMoths Moth1"><td style="white-space:nowrap; padding:8px !important; text-align:left !important;">Сумма по дням периода:</td>'.$text2.'<td><b>'.$total.'</b></td></tr>';
	if ($total!=0) { $AdminText.=$AdminText1;} else { $AdminText.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">За текущий период нет новых пользователей...</h2></td></tr>'; }}
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	$table="_banners_stat"; $total=0; $max=0; $min=999999999999999; $coms=array(); $text1=""; $text2=""; $q="SELECT SUM(`$table`.`s`) as `cnt`, `$table`.`data` as `datar` FROM `$table` WHERE (`$table`.`data`>'".$d3."')
	GROUP BY `datar` ORDER BY `datar` DESC"; $data=DB($q); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $ar["cnt"]=(int)$ar["cnt"]; $coms[$ar["datar"]]=$ar["cnt"];
	if ($ar["cnt"]>$max) { $max=$ar["cnt"]; } if ($ar["cnt"]<$min) { $min=$ar["cnt"]; } $total=$total+$ar["cnt"]; endfor;
	if ($total==0) { $AdminText.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">Баннерная система не задействована...</h2></td></tr>'; } else {
	$nm=""; $nmt=""; if ($max>1000) { $nm="T"; $nmt="*Тысяч показов"; } if ($max>500000) { $nm="M"; $nmt="*Миллионов показов"; }
	for($i=$monthago; $i<=time(); $i=$i+86400):	$d=date("Y.m.d", $i); if ($max>0) { $h=round(($coms[$d]/$max)*60)+10; } else { $h=10; } $text1.="<td align='center' valign='bottom'><div style='width:100%; height:".$h."px;' class='Moth7'></div></td>"; $text2.="<td align='center' ".hd($i).">".dm($coms[$d])."</td>"; endfor;
	$AdminText2.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">Количество просмотров баннеров</h2></td></tr>
	<tr class="InfoZayStat"><td style="white-space:nowrap; font-size:11px; padding:8px !important; text-align:left !important;">Максимум: <b>'.dm($max).$nm.'</b><br><br>Минимум: <b>'.dm($min).$nm.'</b><br><br>Среднее: <b>'.dm(round($total/$indays)).$nm.'</b><br><br>Всего: <b>'.dm($total).$nm.'</b><br><br><br><div style="color:#999; font-size:9px;">'.$nmt.'</div></td>'.$text1.'<td></td></tr>
	<tr class="InfoZayMoths Moth1"><td style="white-space:nowrap; padding:8px !important; text-align:left !important;">Сумма по дням периода:</td>'.$text2.'<td><b>'.dm($total).'</b></td></tr>';
	if ($total!=0) { $AdminText.=$AdminText2;} else { $AdminText.='<tr class="InfoZayStat"><td style="padding:8px !important; text-align:left !important;" colspan="'.($indays+3).'"><h2 style="margin:0; padding:0;">За текущий период нет показов баннеров...</h2></td></tr>'; }}
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$AdminText.="</table><div class='C10'></div>Прокрутка статистики >>><div class='C10'></div></div><div style='font-size:11px; color:#888; float:left;'>Актуальность статистики: ".$d1[1]." Обновление происходит раз в ".$statrefresh." минут.</div><div class='C20'></div>";
	@file_put_contents($_SERVER['DOCUMENT_ROOT']."/admin/index-page.stat", $AdminText);	}


	##################################################################################################################################################################
	##################################################################################################################################################################
	##################################################################################################################################################################
	$df=$_GET["df"]; $dt=$_GET["dt"]; $P=$_POST; $d1=ToRusData($df); $d2=ToRusData($dt); $tmp=ToRusData(time()); $today=$tmp[2];
	if (isset($P["savebutton"])) {
		$ar=explode(".", $P["ddata1"]); $sdata1=mktime($P["ddata2"], $P["ddata3"], $P["ddata4"], $ar[1], $ar[0], $ar[2]); 
		$ar=explode(".", $P["ddata11"]); $sdata2=mktime($P["ddata21"], $P["ddata31"], $P["ddata41"], $ar[1], $ar[0], $ar[2]); 
		@header("location: ?df=".($sdata1)."&dt=".($sdata2)); exit();
	}
	if (!isset($dt) || !isset($df)) { @header("location: ?df=".(time()-30*24*60*60)."&dt=".time()); exit(); } $days=round(($dt-$df)/60/60/24)+1; 
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$AdminText.="<h2>Лучшие комментаторы</h2><form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'><b>Отчетный период: $d1[2] - $d2[2], дней: $days</b><div class='C10'></div>";
	$AdminText.="<div class='RoundText'><table>".'<tr class="TRLine1"><td class="VarName">Начало отчетного периода</td><td class="VarName">Конец отчетного периода</td><td></td><tr><tr class="TRLine0"><td class="DateInput">'.str_replace('"><div><select','"><br><br><div><select', GetDataSet($df)).'</td><td class="DateInput">'.str_replace('"><div><select','"><br><br><div><select', GetDataSet($dt, 1)).'</td><td width="1%" valign="bottom"><input type="submit" name="savebutton" id="savebutton" class="SaveButton" value="Определить период"></td><tr>'."</table></div><div class='C'></div></form>";
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	if ($us[0]==0){
		$AdminText.='<h2 style="margin:0; padding:0;">Форум и блоги отключены (требуется модуль пользователей)...</h2>';
	} else {
		$frm=array("'default'"); $blg=array("'default'"); $table="_comments"; $limit=20; $text1=""; $text2=""; $text3=""; $text11=""; $text21=""; $text31=""; 
		/* определить таблицы для форума и блогов */
		$data=DB("SELECT `link` FROM `_pages` WHERE (`module`='forum')"); for($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $frm[]="'".$ar["link"]."'"; endfor;
		$data=DB("SELECT `link` FROM `_pages` WHERE (`module`='blog')"); for($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $blg[]="'".$ar["link"]."'"; endfor;
		/* комменты в новостях */
		$q="SELECT `$table`.`uid`, COUNT(`$table`.`id`) as `cnt`, `_users`.`nick` FROM `$table` LEFT JOIN `_users` ON `$table`.`uid`=`_users`.`id` WHERE (`$table`.`data`>'".$df."' && `$table`.`data`<'".$dt."' && `_users`.`stat`='1' && `$table`.`link` NOT IN (".implode(",", array_merge($frm, $blg)).")) GROUP BY `$table`.`uid` ORDER BY `cnt` DESC LIMIT ".$limit; #echo $q."<hr>";
		$data=DB($q); $text1="<table cellspacing='0' style='border-collapse:collapse;'>"; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$text1.='<tr class="TRLine TRLine'.($i%2).' InfoZayStat">'."<td style='padding:8px !important; text-align:left !important;'>".($i+1).". <a href='/users/view/$ar[uid]'>$ar[nick]</a></td><td width='1%' align='center'> ".(int)$ar["cnt"]." </td>".'</tr>';
		endfor; $text1.="</table>"; if ($data["total"]==0) { $text1="<i>Нет информации</i>"; }	
		$q="SELECT `$table`.`uid`, COUNT(`$table`.`id`) as `cnt`, `_users`.`nick` FROM `$table` LEFT JOIN `_users` ON `$table`.`uid`=`_users`.`id` WHERE (`_users`.`stat`=1 && `$table`.`link` NOT IN (".implode(",", array_merge($frm, $blg)).")) GROUP BY `$table`.`uid` ORDER BY `cnt` DESC LIMIT ".$limit; #echo $q."<hr>"; 
		$data=DB($q); $text11="<table cellspacing='0' style='border-collapse:collapse;'>";  for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$text11.='<tr class="TRLine TRLine'.($i%2).' InfoZayStat">'."<td style='padding:8px !important; text-align:left !important;'>".($i+1).". <a href='/users/view/$ar[uid]'>$ar[nick]</a></td><td width='1%' align='center'> ".(int)$ar["cnt"]." </td>".'</tr>';
		endfor; $text11.="</table>"; if ($data["total"]==0) { $text11="<i>Нет информации</i>"; }		
		/* комменты на форуме */
		$q="SELECT `$table`.`uid`, COUNT(`$table`.`id`) as `cnt`, `_users`.`nick` FROM `$table` LEFT JOIN `_users` ON `$table`.`uid`=`_users`.`id` WHERE (`$table`.`data`>'".$df."' && `$table`.`data`<'".$dt."' && `_users`.`stat`='1' && `$table`.`link` IN (".implode(",", $frm).")) GROUP BY `$table`.`uid` ORDER BY `cnt` DESC LIMIT ".$limit; #echo $q."<hr>";
		$data=DB($q); $text2="<table cellspacing='0' style='border-collapse:collapse;'>"; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$text2.='<tr class="TRLine TRLine'.($i%2).' InfoZayStat">'."<td style='padding:8px !important; text-align:left !important;'>".($i+1).". <a href='/users/view/$ar[uid]'>$ar[nick]</a></td><td width='1%' align='center'> ".(int)$ar["cnt"]." </td>".'</tr>';
		endfor; $text2.="</table>"; if ($data["total"]==0) { $text2="<i>Нет информации</i>"; }
		$q="SELECT `$table`.`uid`, COUNT(`$table`.`id`) as `cnt`, `_users`.`nick` FROM `$table` LEFT JOIN `_users` ON `$table`.`uid`=`_users`.`id`WHERE (`_users`.`stat`=1 && `$table`.`link` IN (".implode(",", $frm).")) GROUP BY `$table`.`uid` ORDER BY `cnt` DESC LIMIT ".$limit; #echo $q."<hr>"; 
		$data=DB($q); $text21="<table cellspacing='0' style='border-collapse:collapse;'>"; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$text21.='<tr class="TRLine TRLine'.($i%2).' InfoZayStat">'."<td style='padding:8px !important; text-align:left !important;'>".($i+1).". <a href='/users/view/$ar[uid]'>$ar[nick]</a></td><td width='1%' align='center'> ".(int)$ar["cnt"]." </td>".'</tr>';
		endfor; $text21.="</table>"; if ($data["total"]==0) { $text21="<i>Нет информации</i>"; }		
		/* комменты в блогах */
		$q="SELECT `$table`.`uid`, COUNT(`$table`.`id`) as `cnt`, `_users`.`nick` FROM `$table` LEFT JOIN `_users` ON `$table`.`uid`=`_users`.`id` WHERE (`$table`.`data`>'".$df."' && `$table`.`data`<'".$dt."' && `_users`.`stat`='1' && `$table`.`link` IN (".implode(",", $blg).")) GROUP BY `$table`.`uid` ORDER BY `cnt` DESC LIMIT ".$limit; #echo $q."<hr>";
		$data=DB($q);$text3="<table cellspacing='0' style='border-collapse:collapse;'>";  for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$text3.='<tr class="TRLine TRLine'.($i%2).' InfoZayStat">'."<td style='padding:8px !important; text-align:left !important;'>".($i+1).". <a href='/users/view/$ar[uid]'>$ar[nick]</a></td><td width='1%' align='center'> ".(int)$ar["cnt"]." </td>".'</tr>';
		endfor; $text3.="</table>"; if ($data["total"]==0) { $text3="<i>Нет информации</i>"; }	
		$q="SELECT `$table`.`uid`, COUNT(`$table`.`id`) as `cnt`, `_users`.`nick` FROM `$table` LEFT JOIN `_users` ON `$table`.`uid`=`_users`.`id` WHERE (`_users`.`stat`=1 && `$table`.`link` IN (".implode(",", $blg).")) GROUP BY `$table`.`uid` ORDER BY `cnt` DESC LIMIT ".$limit; #echo $q."<hr>"; 
		$data=DB($q);$text31="<table cellspacing='0' style='border-collapse:collapse;'>";  for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
		$text31.='<tr class="TRLine TRLine'.($i%2).' InfoZayStat">'."<td style='padding:8px !important; text-align:left !important;'>".($i+1).". <a href='/users/view/$ar[uid]'>$ar[nick]</a></td><td width='1%' align='center'> ".(int)$ar["cnt"]." </td>".'</tr>';
		endfor; $text31.="</table>"; if ($data["total"]==0) { $text31="<i>Нет информации</i>"; }
		
	$AdminText.='<div style="width:240px; margin-right:13px; float:left;"><h2 style="margin:10px 0; padding:0;">Новости, за период</h2>'.$text1.'</div>
	<div style="width:240px; margin-right:12px; float:left;"><h2 style="margin:10px 0; padding:0;">Форум, за период</h2>'.$text2.'</div>
	<div style="width:240px; float:left;"><h2 style="margin:10px 0; padding:0;">Блоги, за период</h2>'.$text3.'</div><div class="C10"></div>	 
	<div style="width:240px; margin-right:13px; float:left;"><h2 style="margin:10px 0; padding:0;">Новости, весь срок</h2>'.$text11.'</div>
	<div style="width:240px; margin-right:12px; float:left;"><h2 style="margin:10px 0; padding:0;">Форум, весь срок</h2>'.$text21.'</div>
	<div style="width:240px; float:left;"><h2 style="margin:10px 0; padding:0;">Блоги, весь срок</h2>'.$text31.'</div>'; }
	# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$AdminText.="<div class='C'></div>";	

	#############################################################################################
	### ПРАВАЯ КОЛОНКА ##########################################################################
	#############################################################################################

	### Народные новости
	$AdminRight.="<div align='center' style='font-size:13px; font-weight:bold;'><a href='?cat=moderate_list'>Статьи пользователей</a></div>"; $data=DB("SELECT `id` FROM `post_users` WHERE (`stat`='1')");
	if ($data["total"]==0) { $AdminRight.=$C5."<div style='text-align:center;'><i>Нет ожидающих проверки</i></div>"; } else { $AdminRight.="<div class='Wait'></div><div style='text-align:center;'><b><a href='?cat=moderate_new' style='color:red;'>Ожидают модерацию</a>: ".$data["total"]."</b></div><div class='Wait'></div>"; } $AdminRight.=$C20;

	$AdminRight.="<div align='center' style='font-size:13px; font-weight:bold;'><a href='?cat=moderate_msg'>Сообщения пользователей</a></div>"; $data=DB("SELECT `id` FROM `_usersmess` WHERE (`readed`='0' && `fromorto`='1')");
	if ($data["total"]==0) { $AdminRight.=$C5."<div style='text-align:center;'><i>Нет новых сообщений</i></div>"; } else { $AdminRight.="<div class='Wait'></div><div style='text-align:center;'><b><a href='?cat=moderate_msg' style='color:red;'>Новых сообщений</a>: ".$data["total"]."</b></div><div class='Wait'></div>"; } $AdminRight.=$C20;

	$AdminRight.="<div align='center' style='font-size:13px; font-weight:bold;'><a href='?cat=moderate_pays'>Заявки на вывод</a></div>"; $data=DB("SELECT `id` FROM `_userspays` WHERE (`money`<0 && `pid`='0')");
	if ($data["total"]==0) { $AdminRight.=$C5."<div style='text-align:center;'><i>Нет новых заявок</i></div>"; } else { $AdminRight.="<div class='Wait'></div><div style='text-align:center;'><b><a href='?cat=moderate_pays' style='color:red;'>Новых заявок</a>: ".$data["total"]."</b></div><div class='Wait'></div>"; } $AdminRight.=$C20;

	### Быстрое добавление Материалов
	$AdminRight.="<h2>Добавить материал</h2>"; $data=DB("SELECT `shortname` as `name`,`link`,`id` FROM `_pages` WHERE (`module`!='' && main!='1' && `hidden`!='1') ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
	$AdminRight.='<div class="AdminFastIcon"><a href="?cat='.$ar["link"].'_add" title="Добавить материал">'.AIco('plus').'</a><a href="?cat='.$ar["link"].'_add" title="Добавить материал" style="font-size:12px;">'.$ar["name"].'</a></div>'.$C5; endfor; $AdminRight.=$C20;
	### Уведомления об ошибках
	$data=DB("select `id` from `_mistakes` WHERE (`stat`='1')"); $AdminRight.="<h2 style='margin-bottom:5px;'>Уведомления об ошибках</h2>"; $AdminRight.='<div class="AdminFastIcon"><a href="?cat=adm_mistakes" style="font-size:11px;">'.AIco('error').'Новых сообщений: '.$data["total"].'</a></div>'.$C20;
	
}
$_SESSION["Msg1"]="";

function clear($t){ return trim(strip_tags($t)); }
function GetDataPublishSetka($lnk) { global $items, $monthago; $text=""; $t=0; for($i=$monthago; $i<=time(); $i=$i+86400):	$d=date("Y.m.d", $i); $text.="<td align='center' style='font-size:10px; color:#999;'><b>".$items[$lnk][$d]."</b></td>"; $t=$t+$items[$lnk][$d];	endfor; $text.="<td align='center' width='1%' style='font-size:10px;'><b><a href='?cat=".$lnk."_list'>".(int)$t."</a></b></td>"; return $text; }
function dm($m) { global $nm; $k=$m; if ($nm=="T") { $k=round($m/1000, 1); } if ($nm=="M") { $k=round($m/1000000, 1); } return $k; }
function hd($data) { $bg=''; $dday=(int)date("w", $data); if ($dday==0 || $dday==6) { $bg="style='background:#8ebbe6;' title='Выходной день'"; } return($bg); }
?>