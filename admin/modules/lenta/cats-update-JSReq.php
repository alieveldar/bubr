<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$table=$R["table"];
		
	
	// отправляемые данные ==============================================

	$data=DB("SELECT * FROM `".$table."` ORDER BY `rate` DESC"); $text="";
	for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ($ar["stat"]==1) { $chk="checked"; } else { $chk=""; } 
		$text.='<tr class="TRLine TRLine'.($i%2).'" id="Line'.$ar["id"].'">';			
		$text.='<td class="CheckInput"><input type="checkbox" id="RS-'.$ar["id"].'-'.$table.'" '.$chk.'></td>';		
		$text.="<td class='BigText'><a href='/".$alias."/".$ar["id"]."' target='_blank'>".$ar["name"]."</a> <i>$ar[catn]</i></td>";	
		$edit="ItemEdit('".$ar["id"]."', '".$table."', '".$ar["name"]."');";	
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\', \''.$table.'\', \'5\')" title="Поднять">'.AIco(3).'</a></td>
		<td class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\', \''.$table.'\', \'5\')" title="Опустить">'.AIco(4).'</a></td>';
		$text.='<td class="Act"> </td>';
		$text.='<td class="Act" id="Act'.$ar["id"].'"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$table.'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor;
	
	$AdminText.="<table>".$text."</table>";

	$result["table"]=$table;
	$result["content"]=$AdminText;
	$GLOBALS['_RESULT']	= $result;
}

?>