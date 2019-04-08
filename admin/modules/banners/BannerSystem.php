<?
define('DOMAIN', str_replace("www.", "", $_SERVER['HTTP_HOST']));
require_once "../sites/all/themes/prokazan/js/JsHttpRequest.php";
$JsHttpRequest = new JsHttpRequest("windows-1251");
$id = (int)$_REQUEST['id'];
$cahcefile = "../userfiles/banstat-" . DOMAIN . "/";
$bans = array();
$rbans = array();
$bhas = array();
$i = 0;
// �������� ��� �������
$fp = fopen("../manager/banners-" . DOMAIN . ".txt", "r");
while (!feof($fp)) {
	list($id, $pid, $rate, $link, $name, $pic, $file, $text) = explode("|", fgets($fp, 2048));
	$link = str_replace('&', '_amp_', $link);
	$link = str_replace('&', '_amp_', $link);
	if ($id != "" && $id != 0) { $bans["rate"][$pid][$id] = $rate;
		$bans["data"][$pid][$id] = $id . "|" . $link . "|" . $file . "|" . $pic . "|" . $text;
	}
}
fclose($fp);
// ������ �������� ��������
foreach ($bans["data"] as $b => $c) { $k = sizeof($c);
	// $b - ����� ��������� //  $c - ������ �������� // $k = ����������� �������� � ���� ���������
	// ���������� ���������� �������� ��� ������ �������
	for ($j = 0; $j < $k; $j++) {
		// ���������� ����� �� ������� �������
		$setka = array();
		$ti = 0;
		foreach ($c as $v => $l) {
			if (!in_array($v, $bhas)) {// ���� ���� ID ������� �� �� ��������
				for ($x = 0; $x++ < $bans["rate"][$b][$v]; ) { $setka[$ti] = $v;
					$ti++;
				} // �� �������� ��� � ����� ������
			}
		}
		$ran = rand(0, sizeof($setka) - 1);
		$elem = $setka[$ran];
		// �������� ����� � ������� � ������� ������������� ��� ID �������
		array_push($bhas, $elem);
		// ������ ���� ID ������� ����������� ��� ��������� �������
		$jj = $j + 1;
		$res[$b][$jj] = $bans["data"][$b][$elem];
		// �������������� ������ $b - ���������, $j - ����� �������
	}
}
$result["Answer"] = $res;
$GLOBALS['_RESULT'] = $result;
?>