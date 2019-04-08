<?
### НАСТРОЙКИ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {
	$sets=explode("|", @file_get_contents("banners-sets.dat"));
	if ($sets[0]=="") { $AdminText="<div class='ErrorDiv'>Не выбрана необходимая <a href='?cat=banners_system'>таблица компаний</a> баннерной системы!</div>"; $GLOBAL["error"]=1;
	} else { @header("location: ?cat=".$sets[0]."_list"); exit(); }
	$AdminRight="";
}
$_SESSION["Msg"]="";
?>