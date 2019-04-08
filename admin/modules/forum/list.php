<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	
$table1=$alias."_forum"; $table2=$alias."_cat"; $items=array(); $itext="";

	$AdminText.=$C15.'<h2 style="float:left;">Структура форума</h2><div style="float:right;" class="LinkG"><a href="javascript:void(0);" onclick="AddNewForum(\''.$alias.'\');">Добавить форум</a></div>'.$C5."<div id='Msg2' class='InfoDiv'>Вы можете менять порядок форумов, а так же категорий форума</div>";
	
	// СПИСОК КАТЕГОРИЙ (ВЕТОК)
	$data=DB("SELECT * FROM `".$table2."` ORDER BY `lock` DESC, `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $fid=$ar["fid"]; $items[$fid][]=$ar; endfor;
	// СПИСОК ФОРУМОВ (ИНТЕРЕСОВ)
	$data=DB("SELECT * FROM `".$table1."` ORDER BY `rate` DESC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $razs.="<option value=".$ar["id"]." id=pt".$ar["id"].">".str_replace(array('"',"'"), "&quot;", $ar["name"])."</option>"; endfor; 
	
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
	$AdminText.="<div class='RoundText' id='Tgg'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete(\"".$alias."\")'>Удалить выбранные</a></div><table>".$itext."</table></div>";
	// ПРАВАЯ КОЛОНКА
	$AdminRight="<div class='SecondMenu'><a href='/".$alias."'>Перейти к форумам</a></div>";
}

//=============================================

function ForumChild($fid) {
	global $items, $table2, $VARS, $alias, $razs; $count=0; $stotal=count($items[$fid]); $text="";
	$spacer="<img src='/admin/images/icons/sp.png' style='width:15px;' class='spacer' />";
	foreach($items[$fid] as $fid=>$item) { $count++; $fid=$item["fid"];
		if ($item["stat"]==1) { $chk1="checked"; } else { $chk1=""; }
		if ($item["add"]==1) { $chk2="checked"; } else { $chk2=""; }
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
	}
	return $text;
}
$_SESSION["Msg"]="";
?>