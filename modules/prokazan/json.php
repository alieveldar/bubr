<?php

if (empty($_POST['prokazanexport']) || $_POST['prokazanexport'] !== 'C?r~LeG@N3b$') {
    die('[]');
}

error_reporting(0);
ini_set('display_errors', 0);

$GLOBAL["sitekey"] = 1;
require_once $_SERVER['DOCUMENT_ROOT'] . "/modules/standart/DataBase.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/modules/standart/Settings.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/modules/standart/Cache.php";

$tables = array(post_lenta);

$req = array_merge(['action' => '', 'limit' => 20, 'tags' => []], $_POST);
$req['limit'] = max((int)$req['limit'], 2);
$req['tags'] = array_filter(array_map('intval', $req['tags']));

$query = "";
$order = "ORDER BY `data` DESC";
$limit = "LIMIT " . $req['limit'];
$cached = false;
$afterAction = null;

if($req['action']) {
    $cacheFile = '_prokazan-lenta';
    if (RetCache($cacheFile, "cacheblock") == 'true') {
        list($cached) = GetCache($cacheFile);
    } else {
        $query = 'SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`lname`, `[table]`.`adata`, `[table]`.`pic`, `[table]`.`comcount`, "[link]" as `link` FROM `[table]` 
        WHERE (`[table]`.`stat` = "1" AND `[table]`.`pknews` = "1")';
    }
} else {
    mysql_close();
    die('[]');
}

$news = [];
if (!empty($query)) {
    $queries = [];
    foreach ($tables as $table) {
        $link = explode("_", $table)[0];
        $queries[] = str_replace(['[link]', '[table]'], [$link, $table], $query);
    }
    $sqlquery = '(' . implode(") UNION ALL \n(", $queries) . ')';

    $newsdb = DB(trim($sqlquery) . " $order $limit");

    while ($newsdbitem = mysql_fetch_assoc($newsdb["result"])) {
        $news[] = $newsdbitem;
    }

    $response = json_encode($news);
} elseif (!empty($cached)) {
    $response = $cached;
} else {
    $response = '[]';
}

mysql_close();

if (is_callable($afterAction)) {
    $afterAction($news, $response);
}

if (!empty($cacheFile) && !empty($query)) {
    SetCache($cacheFile, $response, '', 'cacheblock');
}

echo $response;
die;
