<?
session_start();
if ($_SESSION['userrole']>2) {
	$GLOBAL["sitekey"]=1;
	@require "../../../modules/standart/DataBase.php";
	//@require "../../../modules/standart/Settings.php";
	@require "../../../modules/standart/JsRequest.php";
	$JsHttpRequest=new JsHttpRequest("utf-8");
	// полученные данные ================================================
	
	$R=$_REQUEST;
	$item=(int)$R["id"];
	$table="_crosslink";
	
		
	// операции =========================================================
	
	if ($R["act"]=="DEL") {
		DB("DELETE FROM `".$table."` WHERE (`id`='".$item."')");
	}
	
	// отправляемые данные ==============================================

	$data=DB("SELECT * FROM `".$table."` ORDER BY `name` ASC"); $text="";
	for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1)); $ar=@mysql_fetch_array($data["result"]); 
		$text.='<tr class="TRLine'.($i%2).'" id="Line'.$i.'">';	
		$text.="<td class='BigText'><a href='".$ar["link"]."' target='_blank'>".$ar["name"]."</a> <i>".$ar["link"]."</i></td>";
		$edit="ItemEdit('".$ar["name"]."', '".$ar["link"]."', '".$ar["id"]."')";
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="'.$edit.'" title="Править">'.AIco('28').'</a></td>';
		$text.='<td class="Act"> </td>';
		$text.='<td class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\')" title="Удалить">'.AIco('exit').'</a></td>';
		$text.="</tr>";
	endfor; $AdminText.="<table>".$text."</table>";




	$result["content"]=$AdminText;
	$GLOBALS['_RESULT']	= $result;
}
?>