<?	
	#в файле статистики 0;1;2;3; = показы, уникальные показы, клики, уникальные клики
	$sid=array(); $sid=explode(".", trim($_GET["ids"],".")); $ROOT = $_SERVER['DOCUMENT_ROOT'];

	@file_put_contents($ROOT."/advert/debug.dat", $_GET["ids"]);

	foreach($sid as $key=>$id) {
		if (is_file($ROOT."/advert/statistic/".$id.".dat")) { $m=explode(";", @file_get_contents($ROOT."/advert/statistic/".$id.".dat")); } else { $m=array(0, 0, 0, 0); }
		$m[0]++; @file_put_contents($ROOT."/advert/statistic/".$id.".dat", $m[0].";".$m[1].";".$m[2].";".$m[3]); 
	}
	
	@header ("Content-type: image/gif"); exit();
?>