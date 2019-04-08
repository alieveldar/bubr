<?	
	#в файле статистики 0;1;2;3; = показы, уникальные показы, клики, уникальные клики
	$id=(int)$_GET["id"]; $ar=explode("away=",$_SERVER["REQUEST_URI"]); $away=$ar[1]; $ROOT = $_SERVER['DOCUMENT_ROOT']; 	
	if (is_file($ROOT."/advert/statistic/".$id.".dat")) { $m=explode(";", @file_get_contents($ROOT."/advert/statistic/".$id.".dat")); } else { $m=array(0, 0, 0, 0); }
	$m[2]++; @file_put_contents($ROOT."/advert/statistic/".$id.".dat", $m[0].";".$m[1].";".$m[2].";".$m[3]); @header("location: ".rawurldecode($away)); exit(); 
	//echo $ROOT."/advert/statistic/".$id.".dat --- ".$m[0].";".$m[1].";".$m[2].";".$m[3]
	
?>