<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

	$table1="_menulist";
	$table2="_menuitem";
	
	
	$data=DB("SELECT `name`, `id` FROM `".$table1."` WHERE (id='".(int)$id."')");
	@mysql_data_seek($data["result"], 0); $menu=@mysql_fetch_array($data["result"]);
	
	if ((int)$menu["id"]==0) { 
		$AdminText="<div class='SystemAlert'><h1>Ошибка</h1>Данный пункт меню не существует или был удален ранее.".$C10."<b>ID: [".$id."]</b></div>"; $GLOBAL["error"]=1;
	} else {
		DB("DELETE FROM `".$table1."` WHERE (`id`='".$id."') LIMIT 1"); DB("DELETE FROM `".$table2."` WHERE (`nid`='".$id."')");
		$AdminText="<div class='SystemAlert'><h1>Уведомление</h1>Пункт меню &laquo;<b>".$menu["name"]."</b>&raquo; был уcпешно удален.".$C10."<b>ID: [".$id."]</b></div>"; $GLOBAL["error"]=1;
	}
	
}
$_SESSION["Msg"]="";
?>