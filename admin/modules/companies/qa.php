<?
### МЕНЮ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$table=$alias."_qa";
$items=array();
	
	$AdminText.=$C15.'<h2 style="float:left;">Вопросы и ответы раздела</h2>';
	$data=DB("SELECT * FROM `".$table."` WHERE (`rid`=".$id.") ORDER BY `data` DESC");
	$items[0]["id"]=0; $items[0]["pid"]=-1; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1));
	$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"]; $items[$idr]["text"]=$ar["text"]; 
	$items[$idr]["pic"]=$ar["pic"]; $items[$idr]["data"]=ToRusData($ar["data"]); $items[$idr]["link"]="/".$alias."/question/view/".$ar["id"]."/"; endfor; $stotal=$data["total"]+1; 
	GetChild_(0); $AdminText.='<ul class="Consults_QA">'.$itext.'</ul>';
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="";
	
}

//=============================================

function GetChild_($i, $lvl=-1) { global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild_($lvl, $i); }
foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild_($key, $lvl); }}} } 

function HtmlChild_($lvl, $idi) {	
	global $items, $prid, $prpid, $prlvl, $ul, $r, $am, $table; $pid=$items[$idi]["pid"]; if ($prlvl>$lvl) { for ($k=0; $k<($prlvl-$lvl); $k++) { $text.="</ul></li>"; }}
	$show = HaveChild_($idi)==1 ? "<i><a href='javascript:void(0)' onclick='$(\"ul\", $(this).parents(\".RoundText\")).toggle();'>Посмотреть ответы</a></i>" : "<span>ОТВЕТОВ НЕТ</span>";
	if(!$lvl) $text.="<li class='RoundText' id='Line".$idi."'><p><a href='javascript:void(0);' onclick='ActionAndUpdate(".$idi.", \"DEL\", \"".$table."\");' title='Удалить' class='delete'>".AIco('exit')."</a><span class='date'>".$items[$idi]['data'][5]."</span><strong>".trim($items[$idi]["name"])."</strong> <i><a href='".$items[$idi]["link"]."' target='_blank'>Страница вопроса</a></i>".$show."</p>".$items[$idi]["text"];
	else $text.="<li><p>Отвечает: <strong>".trim($items[$idi]["name"])."</strong></p>".$items[$idi]["text"];
	if (HaveChild_($idi)==1) { $text.=$r."<ul>".$r; } else { $text.="</li>".$r; } $prid=$idi; $prpid=$pid; $prlvl=$lvl; return $text;
}

function HaveChild_($id) { global $items; foreach ($items as $key=>$item) {  if ($item["pid"]==$id) { return 1; }} return 0; }
$_SESSION["Msg"]="";
?>