<? 
$table=$link."_cats"; $table2=$link."_items"; $table3=$link."_contacts"; $table4=$link."_actions"; $table5=$link."_qa"; $table6="_widget_pics";
if ($start=="") { $start="cats"; $dir[1]="cats"; } $text="";


#############################################################################################################################################
if ($start=="view") {
	$file=$table2."-".$start.".".$page.".".$id;
	$where=$GLOBAL["USER"]["role"]==0?"AND `$table2`.`stat`=1":"";
	$data=DB("SELECT `".$table2."`.* FROM `".$table2."` WHERE (`$table2`.`id`='".(int)$dir[2]."' $where) GROUP BY 1 LIMIT 1");
	
	if ($data["total"]) {
		@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
	
		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetCompanyId($item); SetCache($file, $text, $cap); } UserTracker($link, $page);
		$edit="<div id='AdminEditItem'><a href='".$GLOBAL["mdomain"]."/admin/?cat=".$link."_edit&id=".(int)$dir[2]."'>Редактировать</a></div>";
	}
	else { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Материал не найден"; $Page404=1; }
}

if($start == 'cats'){
	$file=$table."-".$start.".".$page.".".$id;
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetCats(); SetCache($file, $text, $cap); }		
	$cap=$node["name"];
}

if($start == 'cat'){
	$file=$table."-".$start.".".$page.".".$id;
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetCompaniesList(); SetCache($file, $text, $cap); }		
}

if($start == 'consults'){
	$file=$table."-".$start.".".$page.".".$id;
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, 0); } else { list($text, $cap)=GetConsultsCats(); SetCache($file, $text, $cap); }		
	$cap='Консультации';
}

if($start == 'consult'){
	$file=$table."-".$start.".".$page.".".$id;
	if ($_SESSION['onsite']) $msg = addQuestion();
	list($text, $cap)=GetConsultId();
}

if($start == 'question' && $dir[2] == 'view'){
	$file=$table5."-".$start.".".$page.".".$id;
	$data=DB("SELECT `".$table5."`.*, `".$table."`.id AS cid, `".$table."`.name AS cname FROM `".$table5."` LEFT JOIN `".$table."` ON `".$table."`.id=`".$table5."`.rid WHERE (`$table5`.`id`='".(int)$dir[3]."') GROUP BY 1 LIMIT 1");
	
	if ($data["total"]) {
		@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
	
		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetQuestionId($item); SetCache($file, $text, $cap); } UserTracker($link, $page);
	}
	else { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Материал не найден"; $Page404=1; }
}

if($start == 'answer' && $dir[2] == 'add'){	
	$data=DB("SELECT `".$table5."`.*, `".$table."`.name AS catname FROM `".$table5."` LEFT JOIN `".$table."` ON `".$table."`.id=`".$table5."`.rid WHERE (`$table5`.`id`='".(int)$dir[3]."') GROUP BY 1 LIMIT 1");
	if($data['total']) { @mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]); list($text, $cap) = addAnswer($item); $cap='Добавление ответа'; }
	else { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Материал не найден"; $Page404=1; }
}

if($start == 'answer' && $dir[2] == 'edit'){	
	$data=DB("SELECT `t1`.*, `t2`.`name` AS `pname`, `t2`.`text` AS `ptext`, `".$table."`.name AS catname FROM `".$table5."` AS `t1` LEFT JOIN `".$table5."` AS `t2` ON `t2`.`id`=`t1`.`pid` LEFT JOIN `".$table."` ON `".$table."`.id=`t1`.rid WHERE (`t1`.`id`='".(int)$dir[3]."') GROUP BY 1 LIMIT 1");
	if($data['total']) { @mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]); list($text, $cap) = editAnswer($item); $cap='Редактирование ответа'; }
	else { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Материал не найден"; $Page404=1; }
}

if($start == 'actions' && (int)$dir[2]){
	$file=$table4."-list.".$dir[2];
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetCompanyActionsList(); SetCache($file, $text, $cap); }
}

if($start == 'actions' && !(int)$dir[2]){
	$file=$table4."-list.".$dir[2];
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetActionsList(); SetCache($file, $text, $cap); }
}

if($start == 'actions-r' && (int)$dir[2]){
	$file=$table4."-list.".$dir[2];
	if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetActionsRList(); SetCache($file, $text, $cap); }
}

if($start == 'action' && $dir[2] == 'add'){	
	$where=$GLOBAL["USER"]["role"]==0?"AND `$table2`.`stat`=1":"";
	$data=DB("SELECT `".$table2."`.id FROM `".$table2."` WHERE (`$table2`.`id`='".(int)$dir[3]."' AND `$table2`.`uid`='".$_SESSION["userid"]."' $where) GROUP BY 1 LIMIT 1");	
	if($data['total']) { $text = addAction().getActionForm(); $cap='Добавление акции'; }
	else { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Материал не найден"; $Page404=1; }
}


if($start == 'action' && $dir[2] == 'edit'){
	$data=DB("SELECT `".$table4."`.*, `".$table2."`.uid FROM `".$table4."` JOIN `".$table2."` ON `".$table2."`.id=`".$table4."`.pid AND `$table2`.`uid`='".$_SESSION["userid"]."' WHERE (`$table4`.`id`='".(int)$dir[3]."') GROUP BY 1 LIMIT 1");	
	if($data['total']) { @mysql_data_seek($data["result"], 0); $action=@mysql_fetch_array($data["result"]); $text = saveAction($action).getActionForm($action); $cap='Редактирование акции'; }
	else { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Материал не найден"; $Page404=1; }
}

if($start == 'action' && $dir[2] == 'view'){
	$file=$table4."-".$dir[2].".".$dir[3];
	$where=$GLOBAL["USER"]["role"]==0?"AND `$table4`.`stat`=1":"";
	$data=DB("SELECT `".$table4."`.* FROM `".$table4."` WHERE (`$table4`.`id`='".(int)$dir[3]."' $where) GROUP BY 1 LIMIT 1");
	
	if ($data["total"]) {
		@mysql_data_seek($data["result"], 0); $item=@mysql_fetch_array($data["result"]);
	
		if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetActionId($item); SetCache($file, $text, $cap); } UserTracker($link, $page);
		$edit="<div id='AdminEditItem'><a href='".$GLOBAL["mdomain"]."/admin/?cat=".$link."_actionedit&id=".(int)$dir[3]."'>Редактировать</a></div>";
	}
	else { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Материал не найден"; $Page404=1; }
}

if ($GLOBAL["USER"]["role"]>2) { $text=$C.$edit.$C.$text; } $Page["Content"] = $text; $Page["Caption"] = $cap;

#############################################################################################################################################

function GetCompanyId($item) {
	global $VARS, $GLOBAL, $dir, $RealHost, $Page, $node, $table, $table2, $table3, $table4, $table6, $link, $C, $C5, $C15, $C20, $C10, $C25;
	
	$cap=$item["name"]; $text = '<div class="WhiteBlock">';
	if($item['pic']) $text.='<div class="nodePic"><a href="/userfiles/picoriginal/'.$item['pic'].'" rel="prettyPhoto[gallery]"><img src="/userfiles/picpreview/'.$item['pic'].'" border="0" /></a></div>';
	$text .= $item['text'];
	$path='/userfiles/picnews/'.$item["pic"];	
	$text.=$C10."<div class='Likes'>".Likes(Hsc($cap), "", "http://".$RealHost.$path, Hsc(strip_tags($item['lid']))).$C."</div>";
	if($item['urik']) $text.='<div class="urik">'.$item['urik'].'</div>';
	$text.='</div>';
	
	$data3=DB("SELECT `".$table4."`.* FROM `".$table4."` WHERE (`$table4`.`pid`='".$item["id"]."') GROUP BY 1 LIMIT 10");
	if ($data3["total"]) {
		$text.=$C5."<h2>Акции и скидки компании</h2><div class='Actions'>";
		for ($i=0; $i<$data3["total"]; $i++) { @mysql_data_seek($data3["result"], $i); $ar3=@mysql_fetch_array($data3["result"]); $pic = '';						
			if ($ar3["pic"]!="") $pic = "<a href='/".$dir[0]."/action/view/".$ar3["id"]."'><img src='/userfiles/picnews/".$ar3["pic"]."' title='".$ar3["name"]."' /></a>";
			$text.="<div class='WhiteBlock Action' id='action_".$ar3["id"]."'>";
			$text.="<div class='actionPic'>$pic</div>";
			$text.="<div class='actionContent'><div class='ActionName'><a href='/".$dir[0]."/action/view/".$ar3["id"]."'>".$ar3["name"]."</a></div>";						
			if ($ar3["todata"]) { $d=ToRusData($ar3["todata"]); $text.="<p>Акция действует до ".$d[2]."</p>"; }
			$text.="</div>".$C."</div>";
		}
		$text.="</div>";		
	}
	
	$p=DB("SELECT * FROM `".$table6."` WHERE (`pid`='".$item["id"]."' && `link`='".$dir[0]."' && `point`='pics' && `stat`=1) order by `rate` ASC"); 
	if ($p["total"]) { mysql_data_seek($p["result"],0); $ar=@mysql_fetch_array($p["result"]);		
		if(!$ar["sets"]){ $photos = '<div class="ItemAlbum">'; for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]);
			 $photos.='<a rel="prettyPhoto[gallery]" href="/userfiles/picoriginal/'.$ar["pic"].'"><img src="/userfiles/picnews/'.$ar["pic"].'"></a>'; endfor; $photos.='</div>'.$C;
		}
		else {
			$photos = '<div class="ItemAlbum2">'; for ($i=0; $i<$p["total"]; $i++): mysql_data_seek($p["result"],$i); $ar=@mysql_fetch_array($p["result"]);
			$photos.="<div class='PicCon'>";
			$photos.="<div class='albumPic'><a rel='prettyPhoto[gallery]' href='/userfiles/picoriginal/".$ar["pic"]."'><img src='/userfiles/picnews/".$ar["pic"]."'></a></div>";
			$photos.="<div class='albumContent'><h4>".$ar["name"]."</h4>".$ar["text"]."</div>";
			$photos.=$C."</div>";
			endfor; $photos.='</div>'.$C;
		}
		$text.= $C5.'<h2>Фотографии компании</h2><div class="WhiteBlock">'.$photos.'</div>'; 
	} 					
	
	$text .= $C5.'<h2>Контакты компании</h2><div class="WhiteBlock" id="company_'.$item["id"].'">';
	$data2=DB("SELECT * FROM ".$table3." WHERE (`pid`=".$item["id"].")");
	if ($data2["total"]){
		$text.=$C5.'<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><div id="Map'.$item['id'].'" style="float:right;"></div>';		
		for ($j=0; $j<$data2["total"]; $j++) { @mysql_data_seek($data2["result"], $j); $ar2=@mysql_fetch_array($data2["result"]); $worktimeArr = explode('|', $ar2["worktime"]);
			$worktime = '';
			$workt = false;
			for($i = 0; $i < sizeof($worktimeArr); $i++){
				$worktime .= '<td>'.$worktimeArr[$i].'</td>';
				if($worktimeArr[$i] != '') $workt = true;
			}
			$ar2["phone"] = preg_replace('/(\s*\()+/', ' (', $ar2["phone"]);
			$ar2["phone"] = preg_replace('/(\)\s*)+/', ') ', $ar2["phone"]);
			$text.="<div class='contacts'>";
			if($ar2["adres"]) $text.="<div><img src='/template/standart/address.png' style='vertical-align:middle;' /><strong class='address'>".$ar2["adres"]."</strong></div>";
			if($ar2["phone"]) $text.="<div><img src='/template/standart/phone.png' style='vertical-align:middle;' /><strong class='phone'>".$ar2["phone"]."</strong></div>";
			if($workt) {
				$worktime = '<table class="worktimeTable"><tr><th>Понедельник</th><th>Вторник</th><th>Среда</th><th>Четверг</th><th>Пятница</th><th>Суббота</th><th>Воскресенье</th></tr><tr>'.$worktime.'</tr></table>';
				$text .= $worktime.'<br>';
			}
			if($ar2["maps"]) $text.="<span class='maps' style='display:none;'>".$ar2["maps"]."</span>";
			$text.="<span class='worktime' style='display:none;'>".$ar2["worktime"]."</span></div>";
		}
	}
	if($item['site']) $text.='<img src="/template/standart/site.png" style="vertical-align:middle;" /><strong class="address">Сайт:</strong> <a href="'.$item['site'].'" target="_blank"><u>'.$item['site'].'</u></a>';	
	$text .= $C.'<script type="text/javascript">initMap('.$item["id"].');</script></div>';	
		
	
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>".$node["name"]."</a> &raquo; ".$item["name"]."</div>";
		
	return(array($text, $cap));
}

#############################################################################################################################################

function GetActionId($item) {
	global $VARS, $GLOBAL, $dir, $RealHost, $Page, $node, $table, $table2, $table3, $link, $C, $C5, $C15, $C20, $C10, $C25; $d=ToRusData($item["todata"]);
	
	$data2=DB("SELECT `".$table2."`.* FROM `".$table2."` WHERE (`$table2`.`id`='".$item["pid"]."') GROUP BY 1 LIMIT 1");
	@mysql_data_seek($data2["result"], 0); $comp=@mysql_fetch_array($data2["result"]);
	
	$data3=DB("SELECT * FROM ".$table3." WHERE (`pid`=".$comp["id"].")");			
	
	$cap=$item["name"];
	$text = '<div class="WhiteBlock">';
	$text.="<h2><a href='/".$dir[0]."/view/".$comp["id"]."' class='CompanyName'>".$comp["name"]."</a></h2>";
	if ($data3["total"]){
		for ($j=0; $j<$data3["total"]; $j++) { @mysql_data_seek($data3["result"], $j); $ar3=@mysql_fetch_array($data3["result"]);
			$text.="<img src='/template/standart/address.png' style='vertical-align:middle;' />";
			$text.="<strong class='address'>".$ar3["adres"]."</strong>";
			$ar3["phone"] = preg_replace('/(\s*\()+/', ' (', $ar3["phone"]);
			$ar3["phone"] = preg_replace('/(\)\s*)+/', ') ', $ar3["phone"]);
			if($ar3["phone"]) $text.="<strong class='phone'>тел: <span>".$ar3["phone"]."</span></strong>";
		}				
	}
	$text .= '</div>'.$C15; 
	$text .= '<div class="WhiteBlock" id="action_'.$item["id"].'">';
	if($item['pic']) $text.='<div class="nodePic"><a href="/userfiles/picoriginal/'.$item['pic'].'" rel="prettyPhoto[gallery]"><img src="/userfiles/picpreview/'.$item['pic'].'" border="0" /></a></div>';
	$text .= '<h4 style="margin-bottom:10px;">Акция действует до '.$d[2].'</h4>';
	$text .= nl2br($item['text']);
	if($item['rest']) $text .= $C10.'<div class="Info">'.$item['rest'].'</div>';	
	if($item['pics']){
		$text.= $C10.'<div class="ItemAlbum">';
		$pics = explode('|', $item['pics']);
		foreach($pics as $pic){
			$text.='<a rel="prettyPhoto[gallery]" href="/userfiles/picoriginal/'.$pic.'"><img src="/userfiles/picnews/'.$pic.'"></a>';
		}
		$text.= '</div>';
	}
	
	$path='/userfiles/picnews/'.$item["pic"];	
	$text.=$C10."<div class='Likes'>".Likes(Hsc($cap), "", "http://".$RealHost.$path, Hsc(strip_tags($item['lid']))).$C."</div>".$C10;	
	
	$text .= '<div class="CBG"></div><div class="C15"></div><div id="company_'.$comp["id"].'">';
	if($comp['pic']) $text .= "<div class='companyPic'><a href='/".$dir[0]."/view/".$comp["id"]."'><img src='/userfiles/picpreview/".$comp["pic"]."' title='".$comp["name"]."' /></a></div>";
	$text.="<div class='companyContent'><h2><a href='/".$dir[0]."/view/".$comp["id"]."' class='CompanyName'>".$comp["name"]."</a></h2>";
	if ($comp["anonce"]!="") $text.=$C5."<p>".$comp["anonce"]."</p>";
	$text.="</div>"				;
	
	if ($data3["total"]){
		$text.=$C5.'<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><div class="C15"></div><div id="Map'.$comp['id'].'" style="float:right; width:530px; height:400px;"></div>';		
		for ($j=0; $j<$data3["total"]; $j++) { @mysql_data_seek($data3["result"], $j); $ar3=@mysql_fetch_array($data3["result"]); $worktimeArr = explode('|', $ar3["worktime"]);
			$worktime = '';
			$workt = false;
			for($i = 0; $i < sizeof($worktimeArr); $i++){
				$worktime .= '<td>'.$worktimeArr[$i].'</td>';
				if($worktimeArr[$i] != '') $workt = true;
			}
			$ar3["phone"] = preg_replace('/(\s*\()+/', ' (', $ar3["phone"]);
			$ar3["phone"] = preg_replace('/(\)\s*)+/', ') ', $ar3["phone"]);
			$text.="<div class='contacts'>";
			if($ar3["adres"]) $text.="<div><img src='/template/standart/address.png' style='vertical-align:middle;' /><strong class='address'>".$ar3["adres"]."</strong></div>";
			if($ar3["phone"]) $text.="<div><img src='/template/standart/phone.png' style='vertical-align:middle;' /><strong class='phone'>".$ar3["phone"]."</strong></div>";
			if($workt) {
				$worktime = '<table class="worktimeTable"><tr><th>Понедельник</th><th>Вторник</th><th>Среда</th><th>Четверг</th><th>Пятница</th><th>Суббота</th><th>Воскресенье</th></tr><tr>'.$worktime.'</tr></table>';
				$text .= $worktime.'<br>';
			}
			if($ar3["maps"]) $text.="<span class='maps' style='display:none;'>".$ar3["maps"]."</span>";
			$text.="<span class='worktime' style='display:none;'>".$ar3["worktime"]."</span></div>";
		}
	}
	if($comp['site']) $text.='<img src="/template/standart/site.png" style="vertical-align:middle;" /><strong class="address">Сайт:</strong> <a href="'.$comp['site'].'" target="_blank"><u>'.$comp['site'].'</u></a>';
	if($comp['urik']) $text.='<div class="urik">'.$comp['urik'].'</div>';
	$text .= $C.'<script type="text/javascript">initMap('.$comp["id"].');</script></div></div>';
	if($item['fas']) $text .= $C10.'<div class="fas">'.$item['fas'].'</div>';
	
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>".$node["name"]."</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/view/".$comp["id"]."'>".$comp["name"]."</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/actions/".$comp["id"]."'>Акции и скидки</a> &raquo; ".$item["name"]."</div>";
		
	return(array($text, $cap));
}

#############################################################################################################################################

function GetConsultId($item) {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealPage, $RealHost, $Page, $node, $msg, $UserSetsSite, $table, $table2, $table3, $table4, $table5, $C, $C15, $C20, $C10, $C25;
	
	$where=$GLOBAL["USER"]["role"]==0?"AND `".$table2."`.`stat`=1":"";
	$onpage=$node["onpage"]; $pg = $dir[3] ? $dir[3] : 1; $orderby="ORDER BY `".$table2."`.`vip` DESC"; $from=($pg - 1)*$onpage; $onblock=4; /* Новостей в каждом блоке */
	$data=DB("SELECT `".$table2."`.id, `".$table2."`.name, `".$table2."`.pic, `".$table2."`.anonce, `".$table2."`.vip, `".$table."`.name as `cname` FROM `".$table2."` LEFT JOIN `".$table."` ON `".$table."`.`id`=".$dir[2]." WHERE (`".$table2."`.`consultscats` LIKE '%,".$dir[2].",%' $where) GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	if ($data["total"]) {
		$text.="<h2>На ваши вопросы отвечают</h2><div class='Actions'>";
		for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $pic = '';						
			if ($ar["pic"]!="") $pic = "<a href='/".$dir[0]."/view/".$ar["id"]."'><img src='/userfiles/picpreview/".$ar["pic"]."' title='".$ar["name"]."' /></a>";
			if ($ar["vip"]) $text.="<div class='WhiteBlock Vip Action'>";
			else $text.="<div class='WhiteBlock Action'>";
			$text.="<div class='companyPic'>$pic</div>";
			$text.="<div class='actionContent'><a href='/".$dir[0]."/view/".$ar["id"]."'>".$ar["name"]."</a>";
			
			$data2=DB("SELECT * FROM ".$table3." WHERE (`pid`=".$ar["id"].")");
			if ($data2["total"]){
				for ($j=0; $j<$data2["total"]; $j++) { @mysql_data_seek($data2["result"], $j); $ar2=@mysql_fetch_array($data2["result"]);
					$ar2["phone"] = preg_replace('/(\s*\()+/', ' (', $ar2["phone"]);
					$ar2["phone"] = preg_replace('/(\)\s*)+/', ') ', $ar2["phone"]);
					$text.="<p class='contacts'><img src='/template/standart/address.png' style='vertical-align:middle;' />";
					if($ar2["adres"]) $text.="<strong class='address'>".$ar2["adres"]."</strong>";
					if($ar2["adres"] && $ar2["phone"]) $text.="<strong class='address'>. </strong>";
					if($ar2["phone"]) $text.="<strong class='phone'>тел: <span>".$ar2["phone"]."</span></strong>";
					/*$text.="<span style='display:none;'><span class='maps'>".$ar2["maps"]."</span><span class='worktime'>".$ar2["worktime"]."</span></span>";
					if($ar2["maps"]) $text.="<a href='javascript:void(0);' onclick='showMap(".$ar["id"].", $(this))' class='lookOnMap'>Посмотреть на карте</a></p>";*/
					$text.="</p>";
				}				
			}
			$text.="</div>".$C."</div>";
		}
		$text.="</div>";		
	}
	
	$text.='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.=$C15.$msg.'<h2>Задать вопрос специалисту</h2><div class="WhiteBlock"><form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();" class="actionForm"><table class="actionTable">';
	$text.="<tr class='TRLine0'><td class='VarText'>Ваше имя<star>*</star></td><td class='LongInput'><input name='name' type='text'></td></tr>";
	if ($dir[2]==67) { $text.="<tr class='TRLine0'><td class='VarText'>Ваш телефон</td><td class='LongInput'><input name='phone' type='text'></td></tr>"; }
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Ваш вопрос<star>*</star></td><td class="LongInput"><textarea name="text" style="outline:none; height:80px;"></textarea></td></tr>';
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Прикрепить изображения</td><td class="LongInput"><div class="uploaderCon"><div class="uploader" data-name="attachment[]" data-multiple="1"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles"></div></td></tr>';
	if($UserSetsSite[5] == '1') $text.='<tr class="TRLine0"><td class="VarText">Код с картинки<star>*</star></td><td><img src="/modules/standart/captcha/Captcha.php?'.time().'" class="captchaImg" /><input name="captcha" type="text" class="JsVerify2 captchaInput"><input type="submit" name="QuestionButton" class="SaveButton" style="margin-left:182px;" value="Отправить"></td></tr>';			
	$text.='</table>';
	$text.='</form></div>';
	
	$data3=DB("SELECT t1.`id`, t1.`name`, t1.`text`, COUNT(t2.`id`) AS `cnt` FROM ".$table5." AS t1 JOIN ".$table5." AS t2 ON t2.`pid`=t1.`id` WHERE (t1.`pid`=0 AND t1.rid=".$dir[2].") GROUP BY 1 ORDER BY t1.`data` DESC");
	if ($data3["total"]){
		$text.=$C15.'<a name="questions"></a><h2>Вопросы специалистам</h2>';
		for ($j=0; $j<$data3["total"]; $j++) { @mysql_data_seek($data3["result"], $j); $ar3=@mysql_fetch_array($data3["result"]);
			$text.='<div class="WhiteBlock"><img src="/template/standart/question.png" width="14" align="absmiddle" /> Вопрос от <strong>'.$ar3['name'].':</strong><br /><a href="/'.$dir[0].'/question/view/'.$ar3["id"].'">'.$ar3['text'].'</a><a href="/'.$dir[0].'/question/view/'.$ar3["id"].'" style="margin-left:10px;"><u><b>Читать ответы</b></u></a></div>'.$C15;
		}				
	}	
	
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/consults/'>Консультации</a> &raquo; ".$ar["cname"]."</div>";
	return(array($text, 'Консультации: '.$ar["cname"]));
}

#############################################################################################################################################

function GetQuestionId($item) {
	global $VARS, $GLOBAL, $dir, $RealHost, $Page, $node, $table, $table2, $table3, $table5, $link, $C, $C5, $C15, $C20, $C10, $C25; $d=ToRusData($item["todata"]);
	
	$data2=DB("SELECT `".$table5."`.*, `".$table2."`.name AS cname FROM `".$table5."` LEFT JOIN `".$table2."` ON `".$table2."`.id=`".$table5."`.cid WHERE (`$table5`.`pid`='".$item["id"]."') GROUP BY 1");	
	
	$text = '<div class="queston-btns"><a href="http://'.$RealHost.'/'.$dir[0].'/consult/'.$item["cid"].'#questions">Другие вопросы</a><a href="http://'.$RealHost.'/'.$dir[0].'/consult/'.$item["cid"].'">Задать свой вопрос</a></div>';
	$text .= $C5.'<div class="WhiteBlock">';
	$text.='<img src="/template/standart/question.png" width="14" align="absmiddle" /> Вопрос от <strong>'.$item['name'].'</strong>:<div class="C5"></div>';
	$text.='<p>'.nl2br($item['text']).'</p>';
	if($item["pics"]){
		$pics = explode('|', $item["pics"]);
		$text .= '<div class="Attachment">';
		foreach($pics as $pici){
			$text .= '<a href="/userfiles/picoriginal/'.$pici.'" rel="prettyPhoto[gallery]"><img src="/userfiles/picnews/'.$pici.'" /></a>';
		}
		$text .= '</div>';
	}
	if ($data2["total"]){
		for ($i=0; $i<$data2["total"]; $i++) { @mysql_data_seek($data2["result"], $i); $ar2=@mysql_fetch_array($data2["result"]);
			$text.=$C10.'<div class="CBG"></div><a name="answer'.$ar2["id"].'"></a>'.$C10;
			if ($ar2["pic"]) $text.='<div class="answerPic"><img src="/userfiles/avatar/'.$ar2["pic"].'" title="'.$ar2["name"].'" /></div>';
			$text.='Отвечает <strong>'.$ar2["name"].'</strong>'.$C;
			$text.='<p>'.nl2br($ar2["text"]).'</p>'.$C;
			if($ar2["pics"]){
				$pics = explode('|', $ar2["pics"]);
				$text .= '<div class="Attachment">';
				foreach($pics as $pici){
					$text .= '<a href="/userfiles/picoriginal/'.$pici.'" rel="prettyPhoto[gallery]"><img src="/userfiles/picnews/'.$pici.'" /></a>';
				}
				$text .= '</div>';
			}
			$text.='<strong><a href="/'.$dir[0].'/view/'.$ar2["cid"].'/">'.$ar2['cname'].'</a></strong>'.$C;
		}				
	}
	$text .= '</div>';
	$text .= $C5.'<div class="queston-btns"><a href="http://'.$RealHost.'/'.$dir[0].'/consult/'.$item["cid"].'#questions">Другие вопросы</a><a href="http://'.$RealHost.'/'.$dir[0].'/consult/'.$item["cid"].'">Задать свой вопрос</a></div>'.$C;
	
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/consults/'>Консультации</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/consult/".$item["cid"]."/'>".$item["cname"]."</a> &raquo; Вопрос от ".$item['name']."</div>";
		
	return(array($text, 'Вопрос от '.$item['name']));
}

#############################################################################################################################################

function GetCompaniesList() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $table4, $C, $C20, $C10, $C25;
	
	$where=$GLOBAL["USER"]["role"]==0?"AND `".$table2."`.`stat`=1":"";
	$onpage=$node["onpage"]; $pg = $dir[3] ? $dir[3] : 1; $orderby="ORDER BY `".$table2."`.`vip` DESC"; $from=($pg - 1)*$onpage; $onblock=4; /* Новостей в каждом блоке */
	$data=DB("SELECT `".$table2."`.id, `".$table2."`.name, `".$table2."`.pic, `".$table2."`.anonce, `".$table2."`.vip, `".$table."`.name as `cname` FROM `".$table2."` LEFT JOIN `".$table."` ON `".$table."`.`id`=".$dir[2]." WHERE (`".$table2."`.`cats` LIKE '%,".$dir[2].",%' $where) GROUP BY 1 ".$orderby." LIMIT $from, $onpage");
	if ($data["total"]) {
		$text = '<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script>';
		for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $pic = '';						
			if ($ar["pic"]!="") $pic = "<a href='/".$dir[0]."/view/".$ar["id"]."'><img src='/userfiles/picpreview/".$ar["pic"]."' title='".$ar["name"]."' /></a>";
			if($ar["vip"]) $text.="<div class='WhiteBlock Vip' id='company_".$ar["id"]."'>";
			else $text.="<div class='WhiteBlock' id='company_".$ar["id"]."'>";
			$text.="<div class='companyPic'>$pic</div>";
			$text.="<div class='companyContent'><h2><a href='/".$dir[0]."/view/".$ar["id"]."' class='CompanyName'>".$ar["name"]."</a></h2>";						
			if ($ar["anonce"]!="") $text.=$C5."<p>".$ar["anonce"]."</p>";
			
			$data2=DB("SELECT * FROM ".$table3." WHERE (`pid`=".$ar["id"].")");
			if ($data2["total"]){
				for ($j=0; $j<$data2["total"]; $j++) { @mysql_data_seek($data2["result"], $j); $ar2=@mysql_fetch_array($data2["result"]);
					$ar2["phone"] = preg_replace('/(\s*\()+/', ' (', $ar2["phone"]);
					$ar2["phone"] = preg_replace('/(\)\s*)+/', ') ', $ar2["phone"]);
					$text.="<p class='contacts'><img src='/template/standart/address.png' style='vertical-align:middle;' />";
					if($ar2["adres"]) $text.="<strong class='address'>".$ar2["adres"]."</strong>";
					if($ar2["adres"] && $ar2["phone"]) $text.="<strong class='address'>. </strong>";
					if($ar2["phone"]) $text.="<strong class='phone'>тел: <span>".$ar2["phone"]."</span></strong>";
					$text.="<span style='display:none;'><span class='maps'>".$ar2["maps"]."</span><span class='worktime'>".$ar2["worktime"]."</span></span>";
					if($ar2["maps"]) $text.="<a href='javascript:void(0);' onclick='showMap(".$ar["id"].", $(this))' class='lookOnMap'>Посмотреть на карте</a></p>";
					$text.="</p>";
				}				
			}
			$text.="</div>";
			$data3=DB("SELECT `".$table4."`.* FROM `".$table4."` WHERE (`$table4`.`pid`='".$ar["id"]."') GROUP BY 1 LIMIT 3");
			if ($data3["total"]) {
				$text.="<div class='Actions'>";
				for ($j=0; $j<$data3["total"]; $j++) { @mysql_data_seek($data3["result"], $j); $ar3=@mysql_fetch_array($data3["result"]); $pic = '';						
					if ($ar3["pic"]!="") $pic = "<a href='/".$dir[0]."/action/view/".$ar3["id"]."'><img src='/userfiles/picnews/".$ar3["pic"]."' title='".$ar3["name"]."' /></a>";
					$text.="<div class='WhiteBlock Action' id='action_".$ar3["id"]."'>";
					$text.="<div class='actionPic'>$pic</div>";
					$text.="<div class='actionContent'><div class='ActionName'><a href='/".$dir[0]."/action/view/".$ar3["id"]."'>".$ar3["name"]."</a></div>";						
					if ($ar3["todata"]) { $d=ToRusData($ar3["todata"]); $text.="<p>Акция действует до ".$d[2]."</p>"; }
					$text.="</div>".$C."</div>";
				}
				$text.="</div>";		
			}
			$text.=$C."</div>".$C25;
		}		
	}
	
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>".$node["name"]."</a> &raquo; ".$ar["cname"]."</div>";
	$data=DB("SELECT count(id) as `cnt` FROM `".$table2."` WHERE (`".$table2."`.`cats` LIKE '%,".$dir[2].",%' $where)");
	$text.=Pager2($pg, $onpage, ceil($ar["cnt"]/$onpage), $dir[0]."/".$dir[1]."/[page]");
	return(array($text, $node["name"].': '.$ar["cname"]));
}

#############################################################################################################################################

function GetCompanyActionsList() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $table4, $C, $C20, $C10, $C25;
	
	$where=$GLOBAL["USER"]["role"]==0?"AND `".$table4."`.`stat`=1":"";
	$data=DB("SELECT `".$table4."`.*, `".$table2."`.id as `cid`, `".$table2."`.name as `cname` FROM `".$table4."` JOIN `".$table2."` ON `".$table2."`.id=`".$table4."`.pid AND `$table2`.`stat`=1 WHERE (`$table4`.`pid`='".(int)$dir[2]."' $where) GROUP BY 1");
	if ($data["total"]) {
		$text.="<div class='Actions'>";
		for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $pic = '';						
			if ($ar["pic"]!="") $pic = "<a href='/".$dir[0]."/action/view/".$ar["id"]."'><img src='/userfiles/picnews/".$ar["pic"]."' title='".$ar["name"]."' /></a>";
			$text.="<div class='WhiteBlock Action' id='action_".$ar["id"]."'>";
			$text.="<div class='actionPic'>$pic</div>";
			$text.="<div class='actionContent'><div class='ActionName'><a href='/".$dir[0]."/action/view/".$ar["id"]."'>".$ar["name"]."</a></div>";						
			if ($ar["todata"]) { $d=ToRusData($ar["todata"]); $text.="<p>Акция действует до ".$d[2]."</p>"; }
			$text.="</div>".$C."</div>";
		}
		$text.="</div>";		
	}
	
	$cap = $ar["cname"].': Акции и скидки';
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>".$node["name"]."</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/view/".$ar["cid"]."'>".$ar["cname"]."</a> &raquo; Акции и скидки</div>";
	return(array($text, $cap));
}

#############################################################################################################################################

function GetActionsList() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $table4, $C, $C20, $C10, $C25;
	
	list($catalog)=GetActionsCats();
	$text='<div class="WhiteBlock ActionsCatalog">'.$catalog.'</div>';
	
	$where=$GLOBAL["USER"]["role"]==0?"AND `".$table2."`.`stat`=1":"";
	$data=DB("SELECT `".$table2."`.id, `".$table2."`.name, `".$table2."`.vip, `".$table4."`.id AS `aid`, `".$table4."`.name AS `aname`, `".$table4."`.pic AS `apic` FROM `".$table2."` LEFT JOIN `".$table4."` ON `".$table4."`.pid=`".$table2."`.id AND `".$table4."`.stat=1 WHERE (`$table2`.`stat`=1) ORDER BY `$table2`.`vip` DESC");
	if ($data["total"]) {
		$text.="<div class='ActionsWithCatalog'>";
		for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $pic = '';
			if(!$ar["aid"]) continue;						
			if ($ar["apic"]!="") $pic = "<a href='/".$dir[0]."/action/view/".$ar["aid"]."'><img src='/userfiles/picnews/".$ar["apic"]."' title='".$ar["aname"]."' /></a>";
			$text.="<div class='WhiteBlock Action' id='action_".$ar["aid"]."'>";
			$text.="<div class='actionPic'>$pic</div>";
			$text.="<div class='actionContent'><a href='/".$dir[0]."/action/view/".$ar["aid"]."'>".$ar["aname"]."</a><p class='company'><a href='/".$dir[0]."/view/".$ar["id"]."'>".$ar["name"]."</a></p>";						
			//if ($ar["atodata"]) { $d=ToRusData($ar["atodata"]); $text.="<p>Акция действует до ".$d[2]."</p>"; }
			$text.="</div>".$C."</div>";
		}
		$text.="</div>";	
	}
	
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; Акции и скидки в Казани</div>";
	return(array($text, 'Акции и скидки в Казани'));
}

#############################################################################################################################################

function GetActionsRList() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $UserSetsSite, $table, $table2, $table3, $table4, $C, $C20, $C10, $C25;
	
	list($catalog)=GetActionsCats();
	$text='<div class="WhiteBlock ActionsCatalog">'.$catalog.'</div>';
	
	$where=$GLOBAL["USER"]["role"]==0?"AND `".$table2."`.`stat`=1":"";
	$data=DB("SELECT `".$table2."`.id, `".$table2."`.name, `".$table2."`.vip, `".$table."`.name AS `cname`, `".$table4."`.id AS `aid`, `".$table4."`.name AS `aname`, `".$table4."`.pic AS `apic` FROM `".$table."` JOIN `".$table2."` ON `".$table2."`.`cats` LIKE '%,".$dir[2].",%' LEFT JOIN `".$table4."` ON `".$table4."`.pid=`".$table2."`.id AND `".$table4."`.stat=1 WHERE (`".$table."`.`id`=".$dir[2].") ORDER BY `$table2`.`vip` DESC");
	if ($data["total"]) {
		$text.="<div class='ActionsWithCatalog'>";
		for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $pic = ''; if (!$ar["aid"]) continue;
			if(!$ar["aid"]) continue;						
			if ($ar["apic"]!="") $pic = "<a href='/".$dir[0]."/action/view/".$ar["aid"]."'><img src='/userfiles/picnews/".$ar["apic"]."' title='".$ar["aname"]."' /></a>";
			$text.="<div class='WhiteBlock Action' id='action_".$ar["aid"]."'>";
			$text.="<div class='actionPic'>$pic</div>";
			$text.="<div class='actionContent'><a href='/".$dir[0]."/action/view/".$ar["aid"]."'>".$ar["aname"]."</a><p class='company'><a href='/".$dir[0]."/view/".$ar["id"]."'>".$ar["name"]."</a></p>";						
			//if ($ar["todata"]) { $d=ToRusData($ar["todata"]); $text.="<p>Акция действует до ".$d[2]."</p>"; }
			$text.="</div>".$C."</div>";
		}
		$text.="</div>";	
	}
	
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/actions/'>Акции и скидки в Казани</a> &raquo; ".$ar["cname"]."</div>";
	return(array($text, 'Акции и скидки в Казани: '.$ar["cname"]));
}

#############################################################################################################################################

function GetCats() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $itext, $link, $items, $mvl, $prlvl, $ul, $UserSetsSite, $table, $table2, $C, $C20, $C10, $C25;
	$itext=""; $ul=0; $mvl=array(); $items=array(); $prlvl=0;
	$data=DB("SELECT ".$table.".*, COUNT(`".$table2."`.id) AS `cnt` FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`cats` LIKE CONCAT('%',',',".$table.".id,',','%') WHERE (".$table.".`stat`=1 && ".$table.".`type`=1) GROUP BY 1 ORDER BY ".$table.".`name`");
	$items[0]["id"]=0; $items[0]["pid"]=0; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1)); $ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"];
	$items[$idr]["items"] = $ar["cnt"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"]; $items[$idr]["text"]=$ar["text"]; 
	$items[$idr]["pic"]=$ar["pic"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["link"]="/".$link."/cat/".$ar["id"]."/"; endfor; $stotal=$data["total"]+1; 
	GetChild_(0); $text.="<ul class='Catalog'>".$itext."</ul>";
	return(array($text, ""));
}


function GetChild_($i, $lvl=-1) { global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild_($lvl, $i); }
foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild_($key, $lvl); }}} } 

function HtmlChild_($lvl, $idi) {	
	global $items, $prid, $prpid, $prlvl, $ul, $r, $am; if(!$lvl || !$items[$idi]["items"]) return; $pid=$items[$idi]["pid"]; if ($prlvl>$lvl) { for ($k=0; $k<($prlvl-$lvl); $k++) { $text.="</ul></li>"; }}
	$text.="<li class='CatalogLvl".$lvl."'><a href='".$items[$idi]["link"]."' title='".trim($items[$idi]["name"])."'><span class='catImg'><img src='/userfiles/picoriginal/".$items[$idi]["pic"]."' /></span>".trim($items[$idi]["name"])."</a>";
	if (HaveChild_($idi)==1) { $text.=$r."<ul>".$r; } else { $text.="</li>".$r; } $prid=$idi; $prpid=$pid; $prlvl=$lvl; return $text;
}

function HaveChild_($id) { global $items; foreach ($items as $key=>$item) {  if ($item["pid"]==$id) { return 1; }} return 0; }



function GetChild_C($i, $lvl=-1) { global $itext, $items, $mvl; if ($i!=0) { $itext.=HtmlChild_C($lvl, $i); }
foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild_C($key, $lvl); }}} } 

function HtmlChild_C($lvl, $idi) {	
	global $items, $prid, $prpid, $prlvl, $ul, $r, $am; $pid=$items[$idi]["pid"]; if ($prlvl>$lvl) { for ($k=0; $k<($prlvl-$lvl); $k++) { $text.="</ul></li>"; }}
	if(!$lvl) $text.="<li class='WhiteBlock CatalogLvl".$lvl."'><span class='catImg'><img src='/userfiles/picoriginal/".$items[$idi]["pic"]."' /></span><h2>".trim($items[$idi]["name"])."</h2>";
	else $text.="<li class='CatalogLvl".$lvl."'><a href='".$items[$idi]["link"]."' title='".trim($items[$idi]["name"])."'>".trim($items[$idi]["name"])."</a>";
	if (HaveChild_C($idi)==1) { $text.=$r."<ul>".$r; } else { $text.="</li>".$r; } $prid=$idi; $prpid=$pid; $prlvl=$lvl; return $text;
}

function HaveChild_C($id) { global $items; foreach ($items as $key=>$item) {  if ($item["pid"]==$id) { return 1; }} return 0; }

#############################################################################################################################################

function GetActionsCats() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $itext, $link, $items, $mvl, $prlvl, $ul, $UserSetsSite, $table, $table2, $C, $C20, $C10, $C25;
	$itext=""; $ul=0; $mvl=array(); $items=array(); $prlvl=0;
	$data=DB("SELECT ".$table.".*, COUNT(`".$table2."`.id) AS `cnt` FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`cats` LIKE CONCAT('%',',',".$table.".id,',','%') WHERE (".$table.".`stat`=1 && ".$table.".`type`=1) GROUP BY 1 ORDER BY ".$table.".`name`");
	$items[0]["id"]=0; $items[0]["pid"]=0; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1)); $ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"];
	$items[$idr]["items"] = $ar["cnt"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"]; $items[$idr]["text"]=$ar["text"]; 
	$items[$idr]["pic"]=$ar["pic"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["link"]="/".$link."/actions-r/".$ar["id"]."/"; endfor; $stotal=$data["total"]+1; 
	GetChild_(0); $text.="<ul class='Catalog'>".$itext."</ul>";
	return(array($text, ""));
}

#############################################################################################################################################

function GetConsultsCats() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $itext, $link, $items, $mvl, $prlvl, $ul, $UserSetsSite, $table, $table2, $C, $C20, $C10, $C25;
	$itext=""; $ul=0; $mvl=array(); $items=array(); $prlvl=0;
	$data=DB("SELECT ".$table.".*, COUNT(`".$table2."`.id) AS `cnt` FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`consultscats` LIKE CONCAT('%',',',".$table.".id,',','%') WHERE (".$table.".`stat`=1 && ".$table.".`type`=2) GROUP BY 1 ORDER BY ".$table.".`name`");
	$items[0]["id"]=0; $items[0]["pid"]=0; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1)); $ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"];
	$items[$idr]["items"] = $ar["cnt"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"]; $items[$idr]["text"]=$ar["text"]; 
	$items[$idr]["pic"]=$ar["pic"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["link"]="/".$link."/consult/".$ar["id"]."/"; endfor; $stotal=$data["total"]+1; 
	GetChild_C(0); $text.="<ul class='ConsultsCatalog'>".$itext."</ul>";
	return(array($text, ""));
}

#############################################################################################################################################

function getActionForm($action = array()){
	global $VARS, $GLOBAL, $ROOT, $RealPage, $dir, $Page, $node, $table, $table4, $C, $C10, $C15, $C25, $C5; if($action["todata"]) $d=ToRusData($action["todata"]);
	$text='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.='<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();" class="actionForm"><table class="actionTable">';
	$text.="<tr class='TRLine0'><td class='VarText'>Название</td><td class='LongInput'><input name='name' type='text' value='".$action['name']."'></td></tr>";
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Основное фото акции</td><td class="LongInput"><div class="uploaderCon" style="'.($action['pic'] ? 'display:none;' : '').'"><div class="uploader" data-name="pic" data-multiple="0"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	if($action['pic']) $text.='<span class="imgCon"><img src="/userfiles/picpreview/'.$action['pic'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="pic" value="'.$action['pic'].'" /></span>';
	$text.='</div></td></tr>';
	$text.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Текст</td><td class="LongInput"><textarea name="text" style="outline:none;">'.$action['text'].'</textarea></td></tr>';
	$text.="<tr class='TRLine0'><td class='VarText'>Дата окончания</td><td class='DateInput'><input id='datepick' name='todata' type='text' value='".$d[5]."' readonly></td></tr>";
	$text.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Ограничения акции</td><td class="LongInput"><textarea name="rest" style="outline:none; height:50px;">'.$action['rest'].'</textarea></td></tr>';
	$text.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Предупреждение ФАС</td><td class="LongInput"><textarea name="fas" style="outline:none; height:50px;">'.$action['fas'].'</textarea></td></tr>';
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Фотографии акции</td><td class="LongInput"><div class="uploader" data-name="attachment[]" data-multiple="1"></div><div class="Info">Вы можете загрузить фотографии в форматах jpg, gif и png</div><div class="uploaderFiles">';
	if($action['pics']){
		$pics = explode('|', $action['pics']);
		foreach($pics as $pic){
			$text.='<span class="imgCon"><img src="/userfiles/picpreview/'.$pic.'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="attachment[]" value="'.$pic.'" /></span>';
		}
	}
	$text.='</div></td></tr>';				
	$text.='</table>'.$C10.'<div class="CenterText"><input type="submit" name="ActionButton" class="SaveButton" value="Сохранить"></div>';
	$text.='</form>'.$C25;
	return $text;
}


function addAction(){
	global $table4, $dir, $GLOBAL, $ROOT;
	if (isset($_SESSION['Data']["ActionButton"])) {	
		$P = $_SESSION['Data'];
		if(trim($P['name']) == '') $msg = '<div class="ErrorDiv">Ошибка! Поля не заполнены или заполнены неверно</div>';
		else{		
			$ar=explode(".", $P["todata"]); $sdata1=mktime(0, 0, 0, $ar[1], $ar[0], $ar[2]);
			$pics = $pic = "";
			$name = str_replace("'", "\'", $P['name']);
			$text = str_replace("'", "\'", $P['text']);
			$fas = str_replace("'", "\'", $P['fas']);
			$rest = str_replace("'", "\'", $P['rest']);
			if($P["pic"]){			
				$pic = $P["pic"];
				actionsResizePhoto($pic);				
			}
			if($P["attachment"]){
				foreach ($P["attachment"] as $pici) {
					$pics .= $pics ? "|".$pici : $pici;
					actionsResizePhoto($pici);
				}
			}
			$q="INSERT INTO `$table4` (`data`, `todata`, `pid`, `name`, `text`, `pic`, `pics`, `fas`, `rest`) VALUES ('".time()."', '$sdata1', '".$dir[3]."', '".$name."', '".$text."', '$pic', '$pics', '".$fas."', '".$rest."')";
			DB($q); $last=DBL(); DB("UPDATE `$table4` SET `rate`='".$last."' WHERE  (id='".$last."')");
			$msg = '<div class="SuccessDiv">Спасибо! Запись сохранена</div>';
		}
		SD();		
	}
	return $msg;
}


function saveAction(&$action){
	global $table4, $GLOBAL, $ROOT;
	if (isset($_SESSION['Data']["ActionButton"])) {	
		$P = $_SESSION['Data'];
		if(trim($P['name']) == '') $msg = '<div class="ErrorDiv">Ошибка! Поля не заполнены или заполнены неверно</div>';
		else{		
			$ar=explode(".", $P["todata"]); $sdata1=mktime(0, 0, 0, $ar[1], $ar[0], $ar[2]);
			$pics = $action['pics']; $pic = $action['pic'];
			
			if($pic != $P["pic"]){				
				if($pic) { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$pic); }}
				$pic = $P["pic"];				
				if($pic) actionsResizePhoto($pic);
			}
			
			if($pics){
				$pics = explode('|', $pics);
				foreach ($pics as $key => $pici) {
					if(!in_array($pici, $P["attachment"])) { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$pici); } unset($pics[$key]);}
				}
			}
						
			if($P["attachment"]){
				foreach ($P["attachment"] as $pici) {
					if(is_array($pics) && in_array($pici, $pics)) continue;
					actionsResizePhoto($pici);
					$pics[] = $pici;
				}
			}
			$pics = implode('|', $pics);
			
			$q="UPDATE `$table4` SET 
			`todata`='$sdata1',
			`name`='".str_replace("'", "\'", $P['name'])."',
			`text`='".str_replace("'", "\'", $P['text'])."', 
			`pic`='$pic', 
			`pics`='$pics',
			`fas`='".str_replace("'", "\'", $P['fas'])."', 
			`rest`='".str_replace("'", "\'", $P['rest'])."'
			WHERE (`$table4`.`id`='".$action['id']."')";			
			
			$action['todata'] = $sdata1;
			$action['name'] = $P['name'];
			$action['text'] = $P['text'];
			$action['fas'] = $P['fas'];
			$action['rest'] = $P['rest'];
			$action['pic'] = $pic;
			$action['pics'] = $pics;
						
			DB($q);
			$msg = '<div class="SuccessDiv">Спасибо! Запись сохранена</div>';
		}
		SD();	
	}
	return $msg;
}
			
function addQuestion(){
	global $table, $table2, $table5, $dir, $GLOBAL, $VARS, $ROOT, $RealHost, $RealPage;
	if (isset($_SESSION['Data']["QuestionButton"])) {	
		$P = $_SESSION['Data'];
		preg_match_all('/[а-я]/iu', $P['text'], $matches);
		
		if(trim($P['name']) == '' || trim($P['text']) == '' || (isset($_SESSION["CaptchaCode"]) && strtolower($P['captcha']) != strtolower($_SESSION["CaptchaCode"]))) $msg = '<div class="ErrorDiv">Ошибка! Поля не заполнены или заполнены неверно</div>';
		else if(sizeof($matches[0]) < 3) $msg = '<div class="ErrorDiv">Ошибка! Вопрос не имеет информативного содержания</div>';
		else{
			@require($ROOT."/modules/standart/ImageResizeCrop.php");
			$name = str_replace("'", "\'", $P['name']);
			$text = str_replace("'", "\'", $P['text']);
			$phone = str_replace("'", "\'", $P['phone']);
			if($P["attachment"]){
				foreach ($P["attachment"] as $pici) {
					$pics .= $pics ? "|".$pici : $pici;
					list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$pici);
					list($sw, $sh)=explode("-", $GLOBAL['AutoPicPaths']['picnews']);
					$k = min($w / $sw, $h / $sh);
					$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
					crop($ROOT."/userfiles/temp/".$pici, $ROOT."/userfiles/picnews/".$pici, array($x, $y, round($sw * $k), round($sh * $k)));
					resize($ROOT."/userfiles/picnews/".$pici, $ROOT."/userfiles/picnews/".$pici, $sw, $sh);
					rename($ROOT."/userfiles/temp/".$pici, $ROOT."/userfiles/picoriginal/".$pici);
				}
			}
			$q="INSERT INTO `$table5` (`data`, `name`, `phone`, `text`, `rid`, `pics`) VALUES ('".time()."', '".$name."', '".$phone."', '".$text."', '".(int)$dir['2']."', '".$pics."')";	
			DB($q); $last=DBL();
			$msg = '<div class="SuccessDiv">Спасибо! Ваш вопрос отправлен</div>';
			
			$data=DB("SELECT _users.mail, ".$table.".id, ".$table.".name, ".$table2.".id AS cid, ".$table2.".name AS cname FROM ".$table." LEFT JOIN ".$table2." ON ".$table2.".consultscats LIKE '%,".$dir[2].",%' LEFT JOIN `_users` ON `_users`.id=".$table2.".uid WHERE (".$table.".`id`=".$dir[2].") GROUP BY 1");
			if ($data["total"]) {						
				for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
					$subject = 'Вопрос специалисту в разделе '.$ar["name"];
					$body = '<p>Добрый день. Ваша компания <a href="http://'.$RealHost.'/'.$dir[0].'/view/'.$ar['cid'].'/">'.$ar["cname"].'</a> размещена в разделе консультаций <a href="http://'.$RealHost.'/'.$dir[0].'/'.$dir[1].'/'.$dir[2].'/">'.$ar["name"].'</a>. В данном разделе задан новый вопрос.</p>';
					$body .= '<p><strong>'.$name.':</strong><br />'.$text.'<p>';
					$body .= '<p>Вы можете ответить на этот вопрос <a href="http://'.$RealHost.'/'.$dir[0].'/answer/add/'.$last.'/">здесь</a><p>';
					if($ar['mail']) MailSend($ar['mail'], $subject, $body, $VARS["sitemail"]);		
				}
			}
		}
		SD();		
	}
	return $msg;
}

function GetSelected($ar, $id) {
	$text=""; foreach ($ar as $key=>$val) { if ($key==$id) { $text.="<option value='$key' selected>$val</option>"; } else { $text.="<option value='$key'>$val</option>"; }} return $text;
}


function addAnswer($item){
	global $table, $table2, $table5, $dir, $GLOBAL, $VARS, $ROOT, $RealHost, $RealPage, $C10;
	if (isset($_SESSION['Data']["AnswerButton"])) {
		$P = $_SESSION['Data'];
		if(trim($P['text']) == '' || !$P['cid']) $msg = '<div class="ErrorDiv">Ошибка! Поля не заполнены или заполнены неверно</div>';
		else{
			@require($ROOT."/modules/standart/ImageResizeCrop.php");			
			if(!$P['name'] && $P['spec']){
				$data=DB("SELECT `id`,`name`,`pic` FROM `".$table5."` WHERE (id=".$P['spec'].") LIMIT 1");
				@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
				$name = $ar['name'];
				$pic = $ar['pic'];
			}
			else if($P['name']){
				$name = str_replace("'", "\'", $P['name']);
				$pic = $P['pic'];
				if($pic) {					
					list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$pic);
					$sw = 90; $sh = 90;
					$k = min($w / $sw, $h / $sh);
					$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
					crop($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/avatar/".$pic, array($x, $y, round($sw * $k), round($sh * $k)));
					resize($ROOT."/userfiles/avatar/".$pic, $ROOT."/userfiles/avatar/".$pic, $sw, $sh);
				}
			}
			if($P["attachment"]){
				foreach ($P["attachment"] as $pici) {
					$pics .= $pics ? "|".$pici : $pici;
					list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$pici);
					list($sw, $sh)=explode("-", $GLOBAL['AutoPicPaths']['picnews']);
					$k = min($w / $sw, $h / $sh);
					$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
					crop($ROOT."/userfiles/temp/".$pici, $ROOT."/userfiles/picnews/".$pici, array($x, $y, round($sw * $k), round($sh * $k)));
					resize($ROOT."/userfiles/picnews/".$pici, $ROOT."/userfiles/picnews/".$pici, $sw, $sh);
					rename($ROOT."/userfiles/temp/".$pici, $ROOT."/userfiles/picoriginal/".$pici);
				}
			}
			$text = str_replace("'", "\'", $P['text']);
			$q="INSERT INTO `$table5` (`data`, `name`, `text`, `pic`, `pid`, `cid`, `rid`, `pics`) VALUES ('".time()."', '".$name."', '".$text."', '".$pic."', ".$item['id'].", ".$P['cid'].", ".$item['rid'].", '".$pics."')";
			DB($q);	SD();
			header('Location: /'.$dir[0].'/question/view/'.$item["id"]);			
		}
		SD();		
	}
	if($_SESSION['userid']){
		$data=DB("SELECT `id`,`name` FROM `".$table2."` WHERE (uid=".$_SESSION['userid']." && consultscats LIKE '%,".$item["rid"].",%')");
		if($data["total"]){
			$comps = array();
			for ($i=0; $i<$data["total"]; $i++){
				@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $comps[$ar['id']] = $ar['name'];
			}
			$compsStr = implode(',', array_flip($comps));
		}
		
		$data2=DB("SELECT `id`,`name` FROM `".$table5."` WHERE (cid IN (".$compsStr.")) GROUP BY `name`");
		if($data2["total"]){
			$specs = array();
			for ($i=0; $i<$data2["total"]; $i++){
				@mysql_data_seek($data2["result"], $i); $ar2=@mysql_fetch_array($data2["result"]); $specs[$ar2['id']] = $ar2['name'];
			}
		}
	}
	$text = '<div class="WhiteBlock">';
	$text.='Вопрос от <strong>'.$item['name'].'</strong>:';
	$text.='<p><i>'.nl2br($item['text']).'</i></p><div class="CBG"></div>';
	$text.='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.=$C10.$msg.'<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();" class="actionForm"><table class="actionTable">';		
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Текст ответа<star>*</star></td><td class="LongInput"><textarea name="text" style="outline:none;"></textarea></td></tr>';
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Прикрепить изображения</td><td class="LongInput"><div class="uploaderCon"><div class="uploader" data-name="attachment[]" data-multiple="1"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles"></div></td></tr>';		
	$text.='<tr class="TRLine0"><td class="VarText">Компания</td><td class="LongInput"><select name="cid">'.GetSelected($comps).'</select></td></tr>';
	if($specs) {
		$text.='<tr class="TRLine0" style="display:table-row;"><td class="VarText" style="vertical-align:top; padding-top:10px;">Специалист</td><td class="LongInput"><select name="spec" onchange="ShowSets(this);">'.GetSelected($specs, 0).'<option value="0">Добавить нового...</option></select>';
		$hide = ' ShowSets';
	}
	$text.="<tr class='TRLine0".$hide."'><td class='VarText'>Имя и должность специалиста</td><td class='LongInput'><input name='name' type='text'></td></tr>";
	$text.='<tr class="TRLine0'.$hide.'"><td class="VarText" style="vertical-align:top; padding-top:10px;">Фото специалиста</td><td class="LongInput"><div class="uploaderCon"><div class="uploader" data-name="pic" data-multiple="0"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles"></div></td></tr>';			
	$text.='</table>'.$C10.'<div class="CenterText"><input type="submit" name="AnswerButton" class="SaveButton" value="Отправить"></div>';
	$text.='</form>';
	
	//$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/consults/'>Консультации</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/consult/".$item["cid"]."/'>".$item["cname"]."</a> &raquo; Вопрос от ".$item['name']."</div>";
		
	return(array($text, ""));
	
}


function editAnswer($item){
	global $table, $table2, $table5, $dir, $GLOBAL, $VARS, $ROOT, $RealHost, $RealPage, $C10;	
	if (isset($_SESSION['Data']["AnswerButton"])) {
		$P = $_SESSION['Data'];
		if(trim($P['text']) == '' || !$P['cid']) $msg = '<div class="ErrorDiv">Ошибка! Поля не заполнены или заполнены неверно</div>';
		else{
			@require($ROOT."/modules/standart/ImageResizeCrop.php");
			$pics_ = $item['pics'];
			if(!$P['name'] && $P['spec']){
				$data=DB("SELECT `id`,`name`,`pic` FROM `".$table5."` WHERE (id=".$P['spec'].") LIMIT 1");
				@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
				$name = $ar['name'];
				$pic = $ar['pic'];
			}
			else if($P['name']){
				$name = str_replace("'", "\'", $P['name']);
				$pic = $P['pic'];
				if($pic) {					
					list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$pic);
					$sw = 90; $sh = 90;
					$k = min($w / $sw, $h / $sh);
					$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
					crop($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/avatar/".$pic, array($x, $y, round($sw * $k), round($sh * $k)));
					resize($ROOT."/userfiles/avatar/".$pic, $ROOT."/userfiles/avatar/".$pic, $sw, $sh);
				}
			}
			if($P["attachment"]){
				foreach ($P["attachment"] as $pici) {
					$pics .= $pics ? "|".$pici : $pici;
					if(is_array($pics_) && in_array($pici, $pics_)) continue;
					list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$pici);
					list($sw, $sh)=explode("-", $GLOBAL['AutoPicPaths']['picnews']);
					$k = min($w / $sw, $h / $sh);
					$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
					crop($ROOT."/userfiles/temp/".$pici, $ROOT."/userfiles/picnews/".$pici, array($x, $y, round($sw * $k), round($sh * $k)));
					resize($ROOT."/userfiles/picnews/".$pici, $ROOT."/userfiles/picnews/".$pici, $sw, $sh);
					rename($ROOT."/userfiles/temp/".$pici, $ROOT."/userfiles/picoriginal/".$pici);
				}
			}
			$text = str_replace("'", "\'", $P['text']);
			$q="UPDATE `$table5` SET `name`='".$name."', `text`='".$text."', `pic`='".$pic."', `cid`=".$P['cid'].", `pics`='".$pics."' WHERE (`id`=".$item['id'].")";
			DB($q);	SD();
			header('Location: /'.$dir[0].'/question/view/'.$item["pid"]);			
		}
		SD();		
	}
	if($_SESSION['userid']){
		$data=DB("SELECT `id`,`name` FROM `".$table2."` WHERE (uid=".$_SESSION['userid']." && consultscats LIKE '%,".$item["rid"].",%')");
		if($data["total"]){
			$comps = array();
			for ($i=0; $i<$data["total"]; $i++){
				@mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $comps[$ar['id']] = $ar['name'];
			}
			$compsStr = implode(',', array_flip($comps));
		}
		
		$data2=DB("SELECT `id`,`name` FROM `".$table5."` WHERE (cid IN (".$compsStr.")) GROUP BY `pic`");
		if($data2["total"]){
			$specs = array();
			for ($i=0; $i<$data2["total"]; $i++){
				@mysql_data_seek($data2["result"], $i); $ar2=@mysql_fetch_array($data2["result"]); $specs[$ar2['id']] = $ar2['name'];
			}
		}
	}
	$text = '<div class="WhiteBlock">';
	$text.='Вопрос от <strong>'.$item['pname'].'</strong>:';
	$text.='<p><i>'.nl2br($item['ptext']).'</i></p><div class="CBG"></div>';
	$text.='<link media="all" href="/modules/standart/multiupload/client/uploader2.css" type="text/css" rel="stylesheet"><script type="text/javascript" src="/modules/standart/multiupload/client/uploader.js"></script>';
	$text.=$C10.$msg.'<form action="/modules/SubmitForm.php?bp='.$RealPage.'" enctype="multipart/form-data" method="post" onsubmit="return JsVerify();" class="actionForm"><table class="actionTable">';		
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Текст ответа<star>*</star></td><td class="LongInput"><textarea name="text" style="outline:none;">'.$item['text'].'</textarea></td></tr>';
	$text.='<tr class="TRLine0"><td class="VarText" style="vertical-align:top; padding-top:10px;">Прикрепить изображения</td><td class="LongInput"><div class="uploader" data-name="attachment[]" data-multiple="1"></div><div class="Info">Вы можете загрузить фотографии в форматах jpg, gif и png</div><div class="uploaderFiles">';
	if($item['pics']){
		$pics = explode('|', $item['pics']);
		foreach($pics as $pic){
			$text.='<span class="imgCon"><img src="/userfiles/picoriginal/'.$pic.'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="attachment[]" value="'.$pic.'" /></span>';
		}
	}
	$text.='</div></td></tr>';		
	$text.='<tr class="TRLine0"><td class="VarText">Компания</td><td class="LongInput"><select name="cid">'.GetSelected($comps, $item['cid']).'</select></td></tr>';
	if($specs) {
		$text.='<tr class="TRLine0" style="display:table-row;"><td class="VarText" style="vertical-align:top; padding-top:10px;">Специалист</td><td class="LongInput"><select name="spec" onchange="ShowSets(this);">'.GetSelected($specs, $item['id']).'<option value="0">Добавить нового...</option></select>';
		$hide = ' ShowSets';
	}
	$text.="<tr class='TRLine0".$hide."'><td class='VarText'>Имя и должность специалиста</td><td class='LongInput'><input name='name' type='text'></td></tr>";
	$text.='<tr class="TRLine0'.$hide.'"><td class="VarText" style="vertical-align:top; padding-top:10px;">Фото специалиста</td><td class="LongInput"><div class="uploaderCon"><div class="uploader" data-name="pic" data-multiple="0"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles"></div></td></tr>';			
	$text.='</table>'.$C10.'<div class="CenterText"><input type="submit" name="AnswerButton" class="SaveButton" value="Отправить"></div>';
	$text.='</form>';
	
	//$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/consults/'>Консультации</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."/consult/".$item["cid"]."/'>".$item["cname"]."</a> &raquo; Вопрос от ".$item['name']."</div>";
		
	return(array($text, ""));
	
}


function actionsResizePhoto($pic){ 
	global $GLOBAL,	$ROOT;
	@require($ROOT."/modules/standart/ImageResizeCrop.php");
	$picxy="";
	$ext=str_replace("jpeg", "jpg", strtolower(substr($pic, 1+strrpos($pic, "."))));	
	
	foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) {			
		if (!is_dir($ROOT."/userfiles/".$path)) { mkdir($ROOT."/userfiles/".$path, 0777); }
		list($w,$h)=getimagesize($ROOT."/userfiles/temp/".$pic);
		list($sw, $sh)=explode("-", $size); if ($sw!=0 && $sh!=0) { $sk=$sw/$sh; }
		
		if($path=="picpreview") resize($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic, $sw, $sh);
		else if($path=="picoriginal"){
			if($w > $sw) resize($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic, $sw, $sh);
			else copy($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic);
		}
		else{					
			$k = min($w / $sw, $h / $sh);
			$x = round(($w - $sw * $k) / 2); $y = round(($h - $sh * $k) / 2);
			crop($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/".$path."/".$pic, array($x, $y, round($sw * $k), round($sh * $k)));
			resize($ROOT."/userfiles/".$path."/".$pic, $ROOT."/userfiles/".$path."/".$pic, $sw, $sh);
			$picxy.=$path."=".$x.",".$y.",".round($sw * $k + $x).",".round($sh * $k + $y).";";				
		}
	}
}
?>