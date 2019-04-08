<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	global $pg;
	$table="_comments";
	$table2="_users";
	$table3="_commentf";
	$limit=30;
	$from=($pg - 1) * $limit;		
	
	// ЭЛЕМЕНТЫ
	$AdminText.='<h2 style="float:left;">Комментарии материалов</h2>'
	.$_SESSION["Msg"].$C5."<div id='Msg2' class='InfoDiv'>Вы можете просматривать и удалять комментарии</div>";
	
	$data=DB("SELECT `".$table."`.`id`, `".$table."`.`uid`, `".$table."`.`link`, `".$table."`.`uname` as `uname`, `".$table."`.`pid`, `".$table."`.`text`, `".$table."`.`ip`, `".$table."`.`data`, `".$table2."`.`nick`, `_pages`.`shortname` as `lname`
	FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`id`=`".$table."`.`uid` LEFT JOIN `_pages` ON `_pages`.`link`=`".$table."`.`link` ORDER BY `".$table."`.`data` DESC LIMIT $from, $limit"); $text="";

	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
		if ((int)$ar["uid"]==0) { $user="<span style='color:red;'>Гость: ".$ar["uname"]."</span>"; } else { $user="<a href='/users/view/$ar[uid]' target='_blank' style='color:green;'>$ar[nick]</a>"; }
		$text.='<tr id="Line'.$ar["id"].'">'."<td class='BigText' style='padding:4px;'>
		<i style='margin:0;'>".$d[4]." : $user (IP=$ar[ip]), <a href='/$ar[link]/view/$ar[pid]#comment".$ar["id"]."' target='_blank'>$ar[lname] #".$ar["pid"]."</a></i>
		<div class='C10'></div><div style='font:11px/16px Tahoma; color:#000;'>".nl2br($ar["text"])."</div><div class='C10'></div>";
			// Прикрепленные фотографии <!-- 
				$pics=DB("SELECT `pic` FROM `".$table3."` WHERE (`pid`='".$ar["id"]."')"); if ($pics["total"]>0) { $text.="<b>Прикрепленные файлы:</b><div class='C5'></div>";
				for ($j=0; $j<$pics["total"]; $j++): @mysql_data_seek($pics["result"], $j); $ap=@mysql_fetch_array($pics["result"]);
				$text.="<a href='/userfiles/comoriginal/$ap[pic]' target='_blank'><img src='/userfiles/compreview/$ap[pic]' style='margin:7px 7px 0 0; height:100px !important; width:auto !important; float:left;'></a>";
				endfor; $text.="<div class='C10'></div>"; }
			// Прикрепленные фотографии -->
		$text.="</td>";
		$text.='<td class="Act" valign="top"><a href="javascript:void(0);" onclick="ActionAndUpdate('.$ar["id"].', \'DEL\', '.$pg.');" title="Удалить">'.AIco('exit').'</a></td>';
		$text.='<td class="Act" valign="top"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></td>';
		$text.="</tr><tr><td colspan='3'><hr style='border-collapse:collapse; border:none; border-bottom:1px dashed #999;'></td>";
	endfor; 
	
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>".$text."</table></div>";


	$data=DB("SELECT `id` FROM `".$table."`"); $total=ceil($data["total"] / $limit); $AdminText.= Pager($pg, $limit, $total);
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="";
	
}

//=============================================
$_SESSION["Msg"]="";
?>