<?
if ($start=="") { $start="map"; } $page=(int)$page; $file=$table."-".$start.".".$page.".".$id;

	
if ($start=="map") {
	#if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetMap(); SetCache($file, $text, $cap); }	
	list($text, $cap)=GetMap(); $Page["Content"]=$text; $Page["Caption"]=$cap; 
}
	
	

function GetMap() {
	global $page, $VARS, $link; $data=DB("SELECT `name`, `sets`, `text` FROM `_pages` WHERE (`module`='eventmap' && `stat`='1') limit 1"); 
	if (!$data["total"]) { $cap="Материал не найден"; $text=@file_get_contents($ROOT."/template/404.html"); } else { $text="";
	@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $cap=$ar["name"]; $sets=explode('|', $ar['sets']); $time=time()-($sets[2]*24*60*60);
	
	### Все разделы карты 
	$data=DB("SELECT `id`,`name` FROM `_widget_eventtype` WHERE (`stat`=1) ORDER BY `rate` DESC"); $types=""; for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $type=@mysql_fetch_array($data["result"]);
	if ($page==$type["id"]) { $types.="<a href='/$link/map/$type[id]' class='ThisTypeActive'>".$type["name"]."</a>"; } else { $types.="<a href='/$link/map/$type[id]' class='ThisTypeNoActive'>".$type["name"]."</a>"; }}
	if ($data["total"]>1) {
		if ($page==0) { $types="<a href='/$link/' class='ThisTypeActive'>Все события ".$sets[1]."</a>".$types; } else { $types="<a href='/$link/' class='ThisTypeNoActive'>Все события ".$sets[1]."</a>".$types; }	
		$text.="<div class='C'></div>".$types."<div class='C10'></div>";
	}
	
	### Вывод карты
	if ($page!=0) { $w=" AND `_widget_eventmap`.`tid`='$page'"; } else { $w=""; } 
	$q="SELECT `_widget_eventmap`.*, `_widget_eventtype`.`stat` as `tstat`, `_widget_eventtype`.`pic` as `ticon` FROM `_widget_eventmap` LEFT JOIN `_widget_eventtype` ON `_widget_eventtype`.`id`=`_widget_eventmap`.`tid`
	WHERE (`_widget_eventmap`.`stat`=1 AND `_widget_eventtype`.`stat`='1' AND (`_widget_eventmap`.`data`>='".$time."' OR `_widget_eventmap`.`promo`='1') $w)"; $data=DB($q);

	if($data["total"]) {
		
		$events='['; $dates='';
		for ($i=0; $i<$data["total"]; $i++) {
			@mysql_data_seek($data["result"], $i); $ar2=@mysql_fetch_array($data["result"]);
			if($events != '[') $events .= ', ';
			$events .= '['.$ar2['id'].', "'.htmlspecialchars($ar2['name']).'", "'.$ar2['maps'].'"';
			if($ar2['icon'] || $ar2['ticon']) $events .= ', "/userfiles/mapicon/'.($ar2['icon'] ? $ar2['icon'] : $ar2['ticon']).'"';				
			$events .= ']';
			
			if($ar2['data']) $dates .= $dates != '' ? ','.date('d-n-Y', $ar2['data']) : date('d-n-Y', $ar2['data']);
		}
		$events .= ']';
		$text.='<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><div id="Map" style="width:'.$sets[3].'px; height:'.$sets[4].'px;"></div><div class="C20"></div><div class="Calendar"></div><div class="C20"></div><div class="EventsList"></div>';
		$text.='<script type="text/javascript">initMap("'.$VARS["maps"].'", '.$events.'); Calendar("'.$dates.'");</script>';
		
	} else {
		$text.="<h2>Не найдено событий этого типа =(</h2>";
	}
	if ($ar["text"]!="") { $text.="<div class='C15'></div><div class='WhiteBlock'>".$ar["text"]."</div><div class='C10'></div>"; }
	
	}
	return(array($text, $cap));
}
?>