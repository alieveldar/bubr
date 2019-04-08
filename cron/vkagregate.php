<?
$ROOT=$_SERVER['DOCUMENT_ROOT']; $GLOBAL["sitekey"] = 1; $now=time(); @require_once($ROOT."/modules/standart/DataBase.php");	
$datarss=DB("SELECT * FROM `_agregrss`"); if ($datarss["total"]>0) { for($r=0; $r<$datarss["total"]; $r++) { @mysql_data_seek($datarss["result"], $r); $rss=@mysql_fetch_array($datarss["result"]);

//=====================================================================================================================================================================
//=====================================================================================================================================================================

$groups=explode(",", $rss["groups"]); $items=array(); $XMLitems=''; 

foreach ($groups as $group) {  
	$query="https://api.vk.com/method/wall.get?domain=".$group."&count=5&v=5.23"; $obj=json_decode(file_get_contents($query)); $ars=$obj->response->items;
	foreach($ars as $ar) { $i=$ar->date; $items[$i]["text"]=$ar->text; $items[$i]["group"]=$group; $items[$i]["pic"]=$ar->attachments[0]->photo->photo_604; }
}

ksort($items);

foreach($items as $data=>$item) {
	$XMLitem="<item>";
		$XMLitem.="<title></title>";
		$XMLitem.="<pubDate>".date("r", $data)."</pubDate>";
		$XMLitem.="<guid isPermaLink='false'>".$item["group"]."-".$data."</guid>";
		if ($item["pic"]!="" && $item["pic"]!=NULL) {
			$newpic="img-".time()."-".rand(11111111,9999999999).".jpg";
			@file_put_contents($ROOT."/userfiles/temp/".$newpic, @file_get_contents($item["pic"]));
			$XMLitem.="<enclosure url='http://bubr.ru/userfiles/temp/".$newpic."' type='image/jpeg' />";
		}
		if ($item["text"]!="" && $item["text"]!=NULL) { $XMLitem.="<description><![CDATA[ ".strip_tags(htmlspecialchars($item["text"]))." ]]></description>"; }
		$XMLitem.="<link>https://vk.com/like_wall</link>";
	$XMLitem.="</item>";
	$XMLitems=$XMLitem.$XMLitems;
}

$XML='<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
<channel>
<atom:link href="http://bubr.ru/'.$rss["file"].'" rel="self" type="application/rss+xml"/>
<title>'.$rss["name"].'</title>
<lastBuildDate>'.date("r").'</lastBuildDate>
'.$XMLitems.'
</channel>
</rss>';

@file_put_contents($ROOT."/".$rss["file"], $XML); echo $XML."<hr />";
//=====================================================================================================================================================================
//=====================================================================================================================================================================
sleep(3);}}
?>