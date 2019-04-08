<?
session_start();
if ($_SESSION['userrole']>1) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$id=(int)$R["id"];
	$ids=explode(',', $R["id"]);
	$fid=(int)$R["fid"];
	$fids=explode(',', $R["fid"]);
	$alias=$R["alias"];
	$act=$R["act"];
	$table1=$alias."_forum"; $table2=$alias."_cat"; $items=array();
		
	// операции =========================================================
	if ($R["act"]=="DEL"){
		for($j = 0; $j < sizeof($ids); $j++){
			if ($fids[$j]!=0) {
				$coms=array(); $log="Category delete...";
				$q="SELECT `id` FROM `_comments` WHERE (`pid`='".$ids[$j]."' && `link`='$alias')"; $data=DB($q); $log.=$q." -> $data[total]";  
				for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $coms[]=$ar["id"]; endfor;
				if (count($coms)>0) {
					$q="DELETE FROM `_comments` WHERE (`id` IN (".implode(",", $coms)."))"; $log.=$q; DB($q);
					$q="DELETE FROM `_commentf` WHERE (`pid` IN (".implode(",", $coms)."))";  $log.=$q; DB($q);
				}
				$q="DELETE FROM `$table2` WHERE (`id`='".$ids[$j]."')"; $log.=$q; DB($q);
			}
			
			else {
				$cats=array(); $coms=array(); $log="Forum delete...";
				$q="SELECT `id` FROM `$table2` WHERE (`fid`='".$ids[$j]."')"; $data=DB($q); $log.=$q." -> $data[total]"; 
				for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $cats[]=$ar["id"]; endfor;
				if (count($cats)>0) {
					$q="SELECT `id` FROM `_comments` WHERE (`pid` IN ('".implode(",", $cats)."') && `link`='$alias')"; $data=DB($q); $log.=$q." -> $data[total]";
					for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $coms[]=$ar["id"]; endfor;
					if (count($coms)>0) {
						$q="DELETE FROM `_comments` WHERE (`id` IN (".implode(",", $coms)."))"; $log.=$q; DB($q);
						$q="DELETE FROM `_commentf` WHERE (`pid` IN (".implode(",", $coms)."))";  $log.=$q; DB($q);
					}
				}
				$q="DELETE FROM `$table2` WHERE (`fid`='".$ids[$j]."')"; $log.=$q; DB($q);
				$q="DELETE FROM `$table1` WHERE (`id`='".$ids[$j]."')"; $log.=$q; DB($q);
			}
		}
	}
	
	// операции =========================================================
	if ($R["act"]=="UP") {
	 	if ($fid==0) { $table=$table1; $wad=""; } else { $table=$table2; $wad=" AND `fid`='$fid'"; }
		$q="SELECT id, rate FROM `".$table."` WHERE (`rate`>=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$id."')) $wad) ORDER BY `rate` ASC LIMIT 2"; $log.=$q; $data=DB($q);
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)"); }
	}	
	
	// операции =========================================================
	if ($R["act"]=="DOWN") {
		if ($fid==0) { $table=$table1; $wad=""; } else { $table=$table2; $wad=" AND `fid`='$fid'"; }
		$q="SELECT id, rate FROM `".$table."` WHERE (`rate`<=(SELECT `rate` FROM `".$table."` WHERE (`id`='".$id."')) $wad) ORDER BY `rate` DESC LIMIT 2"; $log.=$q; $data=DB($q);
		if ($data["total"]==2) { @mysql_data_seek($data["result"], 0); $a1=@mysql_fetch_array($data["result"]); @mysql_data_seek($data["result"], 1); $a2=@mysql_fetch_array($data["result"]);
		$res=DB("INSERT INTO `".$table."` (`id`, `rate`) VALUE ('".$a1["id"]."','".$a2["rate"]."'), ('".$a2["id"]."','".$a1["rate"]."') ON DUPLICATE KEY UPDATE `rate`=values(`rate`)"); }
	}

	
	// отправляемые данные ==============================================
	// СПИСОК КАТЕГОРИЙ (ВЕТОК)
	$data=DB("SELECT * FROM `".$table2."` ORDER BY `lock` DESC, `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $fid=$ar["fid"]; $items[$fid][]=$ar; endfor;
	// СПИСОК ФОРУМОВ (ИНТЕРЕСОВ)
	$data=DB("SELECT * FROM `".$table1."` ORDER BY `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $razs.="<option value=".$ar["id"]." id=pt".$ar["id"].">".str_replace(array('"',"'"), "&quot;", $ar["name"])."</option>"; endfor; 
	$itext="<div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$alias."\")'>Удалить выбранные</a></div><table>";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		$fid=$ar["id"]; if ($ar["stat"]==1) { $chk1="checked"; } else { $chk1=""; } if ($ar["add"]==1) { $chk2="checked"; } else { $chk2=""; }
		$edit="ForumEdit('".$alias."','".$ar["id"]."','".$ar["name"]."','".$ar["text"]."', '".$chk1."', '".$chk2."')";
		$itext.='<tr class="TRLine1" id="Line'.$ar["id"].'">';
		$itext.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table1.'" '.$chk1.'></td>';
		$itext.='<td class="BigText"><a href="/'.$alias.'/part/'.$ar["id"].'"><b>'.$ar["name"].'</b></a></td>';
		$itext.='<td class="Act"><a href="javascript:void(0);" onclick="AddNewCat(\''.$alias.'\', \''.$ar["id"].'\')" title="Добавить категорию">'.AIco('11').'</a></td>';	
		if ($i!=0) { $itext.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$alias.'\', \''.$ar["id"].'\', \'0\')" title="Поднять">'.AIco('3').'</a></td>'; } else { $itext.='<td class="Act"></td>'; }
		if ($i<($data["total"]-1)) { $itext.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$alias.'\', \''.$ar["id"].'\', \'0\')" title="Опустить">'.AIco('4').'</a></td>'; } else { $itext.='<td class="Act"></td>'; }
		$itext.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
		$itext.='<td class="Act"> </td>';
		$itext.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$alias.'\', \''.$ar["id"].'\', \'0\')" title="Удалить">'.AIco('exit').'</a></td>';
		$itext.='<td class="Act"><input type="checkbox" id="'.$ar["id"].'-0" class="selectItem"></td>';
		$itext.='</tr>';
		$itext.=ForumChild($ar["id"]);
	endfor;
	$itext.="</table>";
	
	$result["content"]=$itext; $result["log"]=$log; $GLOBALS['_RESULT'] = $result;
}

// дополнительные функции ==============================================
function ForumChild($fid) {
	global $items, $VARS, $alias, $razs; $count=0; $stotal=count($items[$fid]); $text=""; $spacer="<img src='/admin/images/icons/sp.png' style='width:15px;' class='spacer' />";
	foreach($items[$fid] as $fid=>$item) { $count++; $fid=$item["fid"]; if ($item["stat"]==1) { $chk1="checked"; } else { $chk1=""; } if ($item["add"]==1) { $chk2="checked"; } else { $chk2=""; }
		if ($item["lock"]==1) { $isd=AIco('13','Тема закреплена')." "; $chk3="checked"; } else { $chk3=""; $isd=""; }
		$edit="CatEditF('".$alias."','".$item["id"]."','".$fid."','<select id=allraz>".$razs."</select>', '".$item["name"]."','".$item["text"]."','".$chk1."','".$chk2."','".$chk3."')";
		$text.='<tr class="TRLine"><td class="CheckInput"><input type="checkbox" id="RS-'.$item["id"].'-'.$alias.'_cat" '.$chk1.'></td>';
		$text.="<td class='BigText'>".$spacer.$isd."<a href='/".$alias."/cat/".$item["id"]."' target='_blank'>".trim($item["name"])."</a></td><td></td>";
		if ($count!=1) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$alias.'\', \''.$item["id"].'\', \''.$fid.'\')" title="Поднять">'.AIco('3').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
		if ($count<=($stotal-1)) { $text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$alias.'\', \''.$item["id"].'\', \''.$fid.'\')" title="Опустить">'.AIco('4').'</a></td>'; } else { $text.='<td class="Act"></td>'; }
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td><td class="Act"> </td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$alias.'\', \''.$item["id"].'\', \''.$fid.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.='<td class="Act"><input type="checkbox" id="'.$item["id"].'-'.$fid.'" class="selectItem"></td>';
		$text.="</tr>";
} return $text; }
?>