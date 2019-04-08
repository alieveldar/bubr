<?
### ПОЛЬЗОВАТЕЛИ ##########################################################################################################################################
$start.=""; $table="_users";  $edit='';
$file="cache".$table."-".$start.".".$page.".".$id;

if ($UserSetsSite[0]==1) {
	if ($start=="exit") { $_SESSION["userid"]=0; $_SESSION["userrole"]=0; $_SESSION["userfrom"]=""; $_SESSION=array(); unset($_SESSION); @header("Location: http://".$_SERVER['HTTP_HOST']); exit(); }
	if ($start=="0") { if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetUsersAll(); SetCache($file, $text, $cap); }}
	if ($start=="view") { if (RetCache($file)=="true") { list($text, $cap)=GetCache($file); } else { list($text, $cap)=GetUsersId(); SetCache($file, $text, $cap); }
	$edit="<div id='AdminEditItem'><a href='".$GLOBAL["mdomain"]."/admin/?cat=adm_usersedit&id=".(int)$dir[2]."'>Редактировать</a></div>"; }
	if ($start=="mysets") { if ($_SESSION["userid"]!=0) { list($text, $cap)=GetUsersSets(); } else { $text=@file_get_contents($ROOT."/template/403.html"); $cap="Доступ закрыт - 403"; $Page404=1; }}
	if ($start=="mylist") { if ($_SESSION["userid"]!=0) { list($text, $cap)=GetUsersList(); } else { $text=@file_get_contents($ROOT."/template/403.html"); $cap="Доступ закрыт - 403"; $Page404=1; }}
	if ($start=="lostid") { $cap="Доступ закрыт";  $text="<b>Это могло случиться по нескольким причинам:</b><ul><li>Истекло время жизни сессии: авторизуйтесь ещё раз</li><li>На сайте идет модернизация: работа некоторых модулей нестабильна</li><li>Доступ закрыт администратором: скорее всего, вы нарушили правила сайта</li></ul>";$Page404=1; }
	if ($Page404!=1) {
		#### Компании пользователя
		if($_SESSION['userid'] && $dir[2] && $_SESSION['userid']==$dir[2]) $text.=userCompanies();
		#### Альбомы пользователя
		if($_SESSION['userid'] && $dir[2] && $_SESSION['userid']==$dir[2]) $text.=userAlbums();
	}
} else {
	$text=@file_get_contents($ROOT."/template/404.html"); $cap="Страница не найдена - 404"; $Page404=1;
}

if ($GLOBAL["USER"]["role"]>2) { $text=$C.$edit.$C.$text; } $Page["Content"] = $text; $Page["Caption"] = $cap; 

#############################################################################################################################################

function GetUsersAll() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table;

	return(array($text, $cap));
}

#############################################################################################################################################

function GetUsersId() {
	global $VARS, $GLOBAL, $ROOT, $dir, $Page, $node, $table, $table2, $C, $C10, $C15, $C25, $C5;
	$data=DB("SELECT `".$table."`.*  FROM `".$table."` WHERE (`id`='".(int)$dir[2]."') LIMIT 1");
	if ($data["total"]==0) { $text=@file_get_contents($ROOT."/template/404.html"); $cap="Пользователь не найден"; $Page404=1;
	} else {
		@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); 
		$cap="Пользователь: ".$ar["nick"]; $d=ToRusData($ar["created"]);
		if ((int)$ar["lasttime"]!=0) { if ((time()-$ar["lasttime"])>600) { $l=ToRusData($ar["lasttime"]); } else {$l[1]="<b style='color:green;'>Сейчас на сайте<b>"; }} else { $l[1]="<i style='color:red;'>нет</i>"; }
		$text.="<div class='WhiteBlock'><table width=100%>";
		$text.="<tr>"; if ($ar["avatar"]!="" && is_file($ROOT."/".$ar["avatar"]) && filesize($ROOT."/".$ar["avatar"])>100) { $text.="<td rowspan=4 valign=top class='UserAvatar'><img src='/".$ar["avatar"]."' /></td>"; }
		$text.="<td width=50%><b>Аккаунт:</b> "; if ($ar["stat"]==1) { $text.="Активен"; } else { $text.="Заблокирован"; } $text.="</td><td width=50%><b>Статус:</b> ".$GLOBAL["roles"][$ar["role"]]."</td></tr>";
		$text.="<tr><td><b>Регистрация:</b> ".$d[1]."</td><td><b>Вход на сайт:</b> ".$l[1]."</td></tr>";
		$text.="<tr><td><b>Рейтинг:</b> ".$ar["karma"]."</td><td><b>Звание:</b> ".$ar["spectitle"]."</td></tr><tr><td colspan=2><b>Подпись:</b> ".$ar["signature"]."</td></tr>";
		$text.="</table></div>";		
		#### Слежение за комментариями
		$item=array(); $moth2ago=time()-60*60*24*30*1; $data=DB("SELECT `_tracker`.`link`, `_tracker`.`pid` FROM `_tracker` WHERE (`_tracker`.`uid`='".(int)$dir[2]."' && `data`>'".$moth2ago."') ORDER BY `_tracker`.`data` DESC");
		if ($data["total"]>0) { $text.=$C10."<h3>".$ar["nick"]." следит за темами:</h3>".$C10; for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $id=$ar["pid"]; $link=$ar["link"]; $themes[$link][$id]=$ar; }
		### Составляем список по всем таблицам
		$ids=array(); foreach ($themes as $link=>$ar) { foreach ($ar as $key=>$val) { $ids[$link].=$key.","; } $ids[$link]=trim($ids[$link],","); }
		### Выбираем из базы данных
		$news=array(); foreach ($ids as $link=>$pids) { $tab=$link."_lenta"; $data=DB("SELECT `$tab`.`id`, `$tab`.`name`, `$tab`.`comcount`, (select `_comments`.`data` from `_comments` WHERE (`_comments`.`pid`=`$tab`.`id` AND `_comments`.`link`='$link' AND `_comments`.`pid` IN ($pids)) ORDER BY `_comments`.`data` DESC LIMIT 1) as `data` 	, (select concat_ws('|', `_comments`.`uid`, `_users`.`nick`) from `_comments` LEFT JOIN `_users` ON `_comments`.`uid`=`_users`.`id`  WHERE (`_comments`.`pid`=`$tab`.`id` AND `_comments`.`link`='$link' AND `_comments`.`pid` IN ($pids)) ORDER BY `_comments`.`data` DESC  LIMIT 1) as `user`
		FROM `$tab` WHERE (`$tab`.`id` IN ($pids) && `$tab`.`comcount`>0) GROUP BY 1 LIMIT 50"); for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $ar["link"]=$link; $news[]=$ar; }} $ars=array(); foreach($news as $key=>$arr){ $ars[$key]=$arr['data']; } array_multisort($ars, SORT_DESC, $news);
		$text.="<table class='RefreshTable'><tr class='TableHead'><td>Публикация</td><td>Ответов</td><td>Обновлено</td></tr>";
		$j=0; foreach($news as $i=>$item) { $j++; $link=$item["link"]; $id=$item["id"]; $path=""; $clas="tdrow".$j%2; $d=ToRusData($item["data"]); list($item["uid"], $item["nick"])=explode("|", $item["user"]); $path="http://".$VARS["mdomain"]."/".$link."/view/".$id."#comments";
		if ($item["uid"]!=0 && $item["nick"]!="") { $user="<a href='/users/view/$item[uid]/'><u>".$item["nick"]."</u></a>"; } else { $user="Гость сайта"; }
		$text.="<tr class='".$clas."'><td><a href='".$path."'>".$item["name"]."</a></td><td class='Data' width='1%' align='center'><a href='".$path."'><u>".$item["comcount"]."</u></a></td><td class='Data' width='1%'>".$user."<br>".$d[4]."</td></tr>";
		} if ($j==0) { $text.="<tr><td colspan=2><i></i></td></tr>";} $text.="</table>".$C5."<div class='Info'>Отображаются публикации, где пользователь оставлял комментарии за последний месяц</div>"; }

		#$text.=$C15."<h3>Лучшие комментарии ".$ar["nick"].":</h3>В разработке...";
		####		
		####
		####
	}
	return(array($text, $cap));
}

#############################################################################################################################################

function GetUsersSets() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table, $RealPage, $RealHost, $C10, $C15, $C, $ROOT; $USER=$GLOBAL["USER"]; $cap="Настройки аккаунта"; $ufrom=$_SESSION['userfrom'];
	if ($USER["avatar"]=="" || !is_file($ROOT."/".$USER["avatar"]) || filesize($ROOT."/".$USER["avatar"])<100) { $avatar="<img src='/userfiles/avatar/no_photo.jpg'>"; } else { $avatar="<img src='/".$USER["avatar"]."'>"; }
	### Основные настройки сайта
	$text.="<h3>Основные настройки</h3>".$C10."<div class='WhiteBlock'>"; if ($ufrom=="") { $text.="<b>Ваш логин:</b> ".$USER["login"].$C10; }
	$text.="<div style='float:left;'><div class='MailUs'><div id='sendstat' class='SaveDiv'>Сохранение настроек</div>
	<div class='Lab'>Введите псевдоним (вас будут знать под этим именем)<star>*</star></div><input class='Inp380' id='uname' type='text' value='$USER[nick]' maxlenght='64' placeholder='это имя видят посетители сайта' />
	<div class='Lab'>Введите подпись для форума</div><input class='Inp380' id='uforum' type='text' value='$USER[signature]' maxlenght='255' />
	<div class='Lab'>Введите E-mail (обещаем отправлять только уведомления)</div><input class='Inp380' id='umail' type='text' value='$USER[mail]' maxlenght='255' placeholder='ваш E-mail' />";
	if ($ufrom=="") { $text.="<div class='Lab'>Сменить пароль от аккаунта</div><input class='Inp380' id='upass' type='text' maxlenght='64' placeholder='новый пароль' />".$C;
	} else { $text.="<div class='Lab' style='display:none;'>Сменить пароль от аккаунта</div><input class='Inp380' id='upass' type='hidden' maxlenght='64' placeholder='новый пароль' />".$C; } 
	$text.="<input type='submit' name='sendbutton' id='sendbutton' class='SaveButton' value='Сохранить настройки' onClick='SaveSettings();'></div></div>
	<div class='Avatar' id='AvatarT'><div id='AvatarI'>".$avatar."</div><span class='Info'>Вы можете загрузить картинку на аватар.<br>Рекомендуемый размер: 100x100px</span>
	<form action='return false;' enctype='multipart/form-data'><div title='Нажмите для выбора файла' id='Podstava' class='Podstava1'>
	<input type='file' id='uavatar' name='uavatar' accept='image/jpeg,image/gif,image/x-png' onChange='StartUploadAvatar();' /></div></form></div>".$C."</div>";
	### Авторизация и объединение
	$URL=rawurlencode("http://".$VARS["mdomain"]."/modules/standart/SocialAddAccount.php?back=http://".$RealHost."/".$RealPage); $ipath="http://".$VARS["mdomain"]."/template/standart/icons/";
	$UserFrom = array(""=>$VARS["mdomain"], "vk"=>"ВКонтакте", "fb"=>"FaceBook", "ml"=>"Mail.ru", "tw"=>"Twitter", "od"=>"Однoклассники", "gl"=>"Google+", "ya"=>"Яндекс"); 
	$UserIcon = array(""=>"thissite.png", "vk"=>"vkontakte.png", "fb"=>"facebook.png", "ml"=>"mail.png", "tw"=>"twitter.png", "od"=>"odnoklassniki.png", "gl"=>"google.png", "ya"=>"yandex.png");
	$auth=$C5."<div class='UserFrom'><img src='".$ipath.$UserIcon[$ufrom]."' />Текущая авторизация через <b>".$UserFrom[$ufrom]."</b></div>".$C10;
	if ($USER["vkontakte"]!="" && $ufrom!="vk") { $accounts.="<div class='UserFrom' id='UserFrom-vk'><img src='".$ipath.$UserIcon["vk"]."' />Связано. <a href='javascript:void(0);' onClick='EraseSocial(\"vk\");'>Отменить</a></div>".$C; }
	if ($USER["facebook"]!="" && $ufrom!="fb")	{ $accounts.="<div class='UserFrom' id='UserFrom-fb'><img src='".$ipath.$UserIcon["fb"]."' />Связано. <a href='javascript:void(0);' onClick='EraseSocial(\"fb\");'>Отменить</a></div>".$C; }
	if ($USER["mailru"]!="" && $ufrom!="ml") 	{ $accounts.="<div class='UserFrom' id='UserFrom-ml'><img src='".$ipath.$UserIcon["ml"]."' />Связано. <a href='javascript:void(0);' onClick='EraseSocial(\"ml\");'>Отменить</a></div>".$C; }
	if ($USER["twitter"]!="" && $ufrom!="tw") 	{ $accounts.="<div class='UserFrom' id='UserFrom-tw'><img src='".$ipath.$UserIcon["tw"]."' />Связано. <a href='javascript:void(0);' onClick='EraseSocial(\"tw\");'>Отменить</a></div>".$C; }
	if ($USER["odnoklas"]!="" && $ufrom!="od") 	{ $accounts.="<div class='UserFrom' id='UserFrom-od'><img src='".$ipath.$UserIcon["od"]."' />Связано. <a href='javascript:void(0);' onClick='EraseSocial(\"od\");'>Отменить</a></div>".$C; }
	if ($USER["google"]!="" && $ufrom!="gl") 	{ $accounts.="<div class='UserFrom' id='UserFrom-gl'><img src='".$ipath.$UserIcon["gl"]."' />Связано. <a href='javascript:void(0);' onClick='EraseSocial(\"gl\");'>Отменить</a></div>".$C; }
	if ($USER["yandex"]!="" && $ufrom!="ya") 	{ $accounts.="<div class='UserFrom' id='UserFrom-ya'><img src='".$ipath.$UserIcon["ya"]."' />Связано. <a href='javascript:void(0);' onClick='EraseSocial(\"ya\");'>Отменить</a></div>".$C; }
	$text.=$C10."<h3>Связать аккаунт</h3>".$C10."<div class='WhiteBlock'>".$auth."<p>Вы можете объединить все свои учетные записи социальных сетей в рамках одного аккаунта на нашем сайте. 
	Все комментарии, фотографии и материалы, добавленные через любую учетную запись социальных сетей, будут подписываться и отображаться от именни одного аккаунта. Так же общими станут настройки, обновления и подписки на темы сайта.</p>
	".$C10."<b>Выберите социальную сеть, к которой необходимо создать привязку:</b>".$C10."<div id='uLogin' x-ulogin-params='display=panel&fields=first_name,last_name,photo&providers=".$GLOBAL["Providers"]."&redirect_uri=".$URL."'></div>".$C10."
	<div class='Info'>Социальные сети не передают нам логин и пароль, мы получаем имя и аватар пользователя.</div></div>"; if ($accounts!="") { $text.=$C10."<h3>Связанные аккаунты</h3>".$C10."<div class='WhiteBlock'>".$accounts."</div>"; }	
	return(array($text, $cap));
}

#############################################################################################################################################

function GetUsersList() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $table, $C10, $C15, $C5; $cap="Обновления комментариев"; $item=array(); $moth2ago=time()-60*60*24*30*1; $limit=100;
	### Значения трекера за последние 1 месяц
	$data=DB("SELECT `_tracker`.* FROM `_tracker` WHERE (`_tracker`.`uid`='".(int)$GLOBAL["USER"]['id']."' && `data`>'".$moth2ago."') ORDER BY `_tracker`.`data` DESC");
	for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $id=$ar["pid"]; $link=$ar["link"]; $themes[$link][$id]=$ar; }
	### Составляем список по всем таблицам
	$ids=array(); foreach ($themes as $link=>$ar) { foreach ($ar as $key=>$val) { $ids[$link].=$key.","; } $ids[$link]=trim($ids[$link],","); }
	### Выбираем из базы данных
	$news=array(); foreach ($ids as $link=>$pids) { $tab=$link."_lenta"; $data=DB("SELECT `$tab`.`id`, `$tab`.`name`, `$tab`.`comcount`, (select `_comments`.`data` from `_comments` WHERE (`_comments`.`pid`=`$tab`.`id` AND `_comments`.`link`='$link' AND `_comments`.`pid` IN ($pids)) ORDER BY `_comments`.`data` DESC LIMIT 1) as `data` 	, (select concat_ws('|', `_comments`.`uid`, `_users`.`nick`) from `_comments` LEFT JOIN `_users` ON `_comments`.`uid`=`_users`.`id`  WHERE (`_comments`.`pid`=`$tab`.`id` AND `_comments`.`link`='$link' AND `_comments`.`pid` IN ($pids)) ORDER BY `_comments`.`data` DESC  LIMIT 1) as `user`
	FROM `$tab` WHERE (`$tab`.`id` IN ($pids) && `$tab`.`comcount`>0) GROUP BY 1 LIMIT ".$limit); for($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $ar["link"]=$link; $news[]=$ar; }} $ars=array(); foreach($news as $key=>$arr){ $ars[$key]=$arr['data']; } array_multisort($ars, SORT_DESC, $news);
	$text.="<table class='RefreshTable'><tr class='TableHead'><td width='1%'>Статус</td><td>Публикация с комментариями</td><td>Всего</td><td>Обновлено</td><td align='center' width='1%'>Отказ</td></tr>";
	$j=0; foreach($news as $i=>$item) { $j++; $link=$item["link"]; $id=$item["id"]; $path=""; $clas="tdrow".$j%2; $d=ToRusData($item["data"]); list($item["uid"], $item["nick"])=explode("|", $item["user"]); $path="http://".$VARS["mdomain"]."/".$link."/view/".$id."#endcomments";
	if ($themes[$link][$id]["stat"]=="1") { $stat="<img src='/template/standart/new.gif' title='Обновлено'>"; } else { $stat=""; } if ($item["uid"]!=0 && $item["nick"]!="") { $user="<a href='/users/view/$item[uid]/'><u>".$item["nick"]."</u></a>"; } else { $user="Гость сайта"; }
	$del="<a href='javascript:void(0);' onClick=\"EscapeRefresh('$link', '$id')\" title='Не следить за темой' id='I-".$link."-".$id."'><img src='/template/standart/exit.png' width='16' height='16'></a>";
	$text.="<tr class='".$clas."' id='R-".$link."-".$id."'><td align='center'>".$stat."</td><td><a href='".$path."'>".$item["name"]."</a></td><td align='center' class='data' style='font-size:13px;'><a href='".$path."'><u>".$item["comcount"]."</u></a></td><td class='Data' width='1%'>".$user."<br>".$d[4]."</td><td align='center'>".$del."</td></tr>";
	} if ($j==0) { $text.="<tr><td colspan=4><i>Нет обновлений...</i></td></tr>";} $text.="</table>".$C5."<div class='Info'>Отображаются обновления и публикации с комментариями за последний месяц</div>";
	return(array($text, $cap));
}

#############################################################################################################################################
function userCompanies(){
	global $VARS, $GLOBAL, $ROOT, $RealPage, $dir, $Page, $node, $table, $C, $C10, $C15, $C25, $C5;
	$data=DB("SELECT `link` FROM `_pages` WHERE (`module`='companies')"); $text = '';
	if($data['total']){ $query = '';
		for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			$table2 = $ar['link'].'_items';
			//$where=$GLOBAL["USER"]["role"]==0?"AND `".$table2."`.`stat`=1":"";
			if($query) { $query .= " UNION "; } $query .="(SELECT `".$table2."`.id, `".$table2."`.name, `".$table2."`.pic, `".$table2."`.anonce, `".$table2."`.vip, `".$table2."`.consultscats, '".$ar['link']."' as `link` FROM `".$table2."` WHERE (`".$table2."`.`uid`=".$_SESSION['userid']." AND `".$table2."`.`stat`=1))";
		}
		$data=DB($query);
		if ($data["total"]) {
			$text .= '<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><script type="text/javascript" src="/modules/companies/companies.js"></script><link type="text/css" href="/modules/companies/companies.css" rel="stylesheet" /><h2>Компании:</h2>';
			for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $pic = '';						
				if ($ar["pic"]!="") $pic = "<a href='/".$ar['link']."/view/".$ar["id"]."'><img src='/userfiles/picpreview/".$ar["pic"]."' title='".$ar["name"]."' /></a>";
				$text.="<div class='WhiteBlock' id='company_".$ar["id"]."'>";
				$text.="<div class='companyPic'>$pic</div>";
				$text.="<h2><a href='/".$ar['link']."/view/".$ar["id"]."' class='CompanyName'>".$ar["name"]."</a></h2>";						
				if ($ar["anonce"]!="") $text.=$C5."<p>".$ar["anonce"]."</p>";
				
				$data2=DB("SELECT * FROM ".$ar["link"]."_contacts WHERE (`pid`=".$ar["id"].")");
				if ($data2["total"]){
					for ($j=0; $j<$data2["total"]; $j++) { @mysql_data_seek($data2["result"], $j); $ar2=@mysql_fetch_array($data2["result"]);
						$text.="<p class='contacts'><strong class='address'>".$ar2["adres"]."</strong>";
						if($ar2["phone"]) $text.="<strong class='phone'>, тел: <span>".$ar2["phone"]."</span></strong>";
						$text.="<span style='display:none;'><span class='maps'>".$ar2["maps"]."</span><span class='worktime'>".$ar2["worktime"]."</span></span>";
						$text.="<a href='javascript:void(0);' onclick='showMap(".$ar["id"].", $(this))' class='lookOnMap'>Посмотреть на карте</a></p>";
					}				
				}
				$text.=$C10.'<div class="CBG"></div>'.$C10.'<h4>Акции компани</h4>';
				$data2=DB("SELECT * FROM ".$ar["link"]."_actions WHERE (`pid`=".$ar["id"].")");
				if ($data2["total"]){
					$text .= '<table class="actionsTable"><tr><th>Название</th><th style="width:60px">Статус</th><th style="width:60px">Срок</th><th style="width:60px">Действие</th></tr>';
					for ($j=0; $j<$data2["total"]; $j++) { @mysql_data_seek($data2["result"], $j); $ar2=@mysql_fetch_array($data2["result"]);
						$stat = $ar2['stat'] ? 'ВКЛ' : 'ВЫКЛ'; if($ar2["todata"]) $d=ToRusData($ar2["todata"]);
						$text .= '<tr><td><a href="/'.$ar['link'].'/action/view/'.$ar2["id"].'" target="_blank">'.$ar2["name"].'</a></td><td>'.$stat.'</td><td>'.$d[5].'</td><td style="text-align:center;"><a href="/'.$ar['link'].'/action/edit/'.$ar2["id"].'"><img src="/template/standart/edit.png" title="Редактировать" /></a></td></tr>';
					}
					$text .= '</table>';				
				}
				$text .= '<a href="/'.$ar['link'].'/action/add/'.$ar["id"].'" class="addAction"><img src="/template/standart/add.png" style="vertical-align:middle;" /> Добавить акцию</a>';
				
				$table3 = $ar["link"]."_qa"; $table4 = $ar["link"]."_cats";
				$data3=DB("SELECT t3.*, t4.id AS cid, t4.name AS cname, t2.id AS aid FROM ".$table3." AS t3 LEFT JOIN ".$table4." AS t4 ON t4.id=t3.rid LEFT JOIN ".$table3." AS t2 ON t2.pid=t3.id AND t2.cid=".$ar["id"]." WHERE (t3.`pid`=0 AND t3.rid in (".trim($ar["consultscats"], ',').")) GROUP BY 1");
				if ($data3["total"]){
					$text .= '<div class="CBG"></div>'.$C10.'<h4>Вопрос специалисту</h4><table class="actionsTable"><tr><th>Вопрос</th><th style="width:150px">Категория</th><th style="width:60px">Ваш ответ</th><th style="width:60px">Дата</th></tr>';
					for ($j=0; $j<$data3["total"]; $j++) { @mysql_data_seek($data3["result"], $j); $ar3=@mysql_fetch_array($data3["result"]); $d=ToRusData($ar3["data"]);
						$status = $ar3["aid"]  ? '<div id="Act'.$ar3["aid"].'"><a href="/'.$ar['link'].'/answer/edit/'.$ar3["aid"].'"><img src="/template/standart/edit.png" title="Редактировать" /></a> <a href="javascript:void(0);" onclick="ItemDelete('.$ar3["aid"].', \''.$table3.'\', '.$ar3["id"].')"><img src="/template/standart/exit.png" width="16" title="Удалить" /></a></div>' : '<a href="/'.$ar['link'].'/answer/add/'.$ar3["id"].'/"><img src="/template/standart/add.png" title="Ответить" /></a>';
						$text .= '<tr><td><p><strong>'.$ar3["name"].':</strong></p><a href="/'.$ar['link'].'/question/view/'.$ar3["id"].'">'.$ar3["text"].'</a></td><td><a href="/'.$ar['link'].'/consult/'.$ar3["cid"].'" target="_blank">'.$ar3["cname"].'</a></td><td>'.$status.'</td><td>'.$d[5].'</td></tr>';
					}
					$text .= '</table>';				
				}
				
				$text.=$C."</div>".$C25;
			}		
		}
	}
	return $text;
}
#############################################################################################################################################
function userAlbums() {
	global $VARS, $GLOBAL, $dir, $ORDERS, $RealHost, $Page, $node, $link, $UserSetsSite, $C, $C20, $C10, $C25;
	
	$data=DB("SELECT `link` FROM `_pages` WHERE (`module`='photoalbum')");
	if($data['total']){ $query = '';
		for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			$table = $ar['link'].'_albums';
			$table2 = $ar['link'].'_photos';
			if($query) { $query .= " UNION "; } $query .="(SELECT `".$table."`.`id`, `".$table."`.`name`, `".$table."`.`data`, '".$ar['link']."' AS `link`, (SELECT `".$table2."`.`pic` FROM `".$table2."` WHERE (`".$table2."`.`pid`=`".$table."`.`id`) ORDER BY `".$table2."`.`rate` ASC LIMIT 1) AS `photo1`, `".$table2."`.`pic` FROM `".$table."` LEFT JOIN `".$table2."` ON `".$table2."`.`main`=1 AND `".$table2."`.`pid`=`".$table."`.`id` WHERE (`".$table."`.`stat`=1 AND `".$table."`.`uid`=".$_SESSION['userid'].") GROUP BY 1)";
		}
		$data=DB($query);
				
		if($data["total"]) {
			$text='<link type=text/css" href="/modules/photoalbum/photoalbum.css" rel="stylesheet" /><h2>Альбомы:</h2>';
			$text .= '<div class="WhiteBlock">';
			$text .= '<table class="actionsTable"><tr><th>Название</th><th style="width:60px">Дата</th><th style="width:60px">Действие</th></tr>';
			for ($i=0; $i<$data["total"]; $i++) {
				@mysql_data_seek($data["result"], $i); $ar2=@mysql_fetch_array($data["result"]); $d=ToRusData($ar2["data"]);
				$text .= '<tr><td><a href="/'.$ar2['link'].'/view/'.$ar2['id'].'">'.$ar2['name'].'</a></td><td>'.$d[5].'</td><td style="text-align:center;"><a href="/'.$ar2['link'].'/addphoto/'.$ar2['id'].'" title="Добавить фотографию"><img src="/template/standart/add.png" /></a> <a href="/'.$ar2['link'].'/edit/'.$ar2['id'].'" title="Настройки альбома"><img src="/template/standart/edit.png"></a> <a href="javascript:void(0);" onclick=\'JsHttpRequest.query("/modules/photoalbum/photoalbum-JSReq.php",{"id":'.$ar2['id'].',"act":"DELALBUM","link":"'.$ar2['link'].'"},function(result,errors){ if(result){ $("#Act'.$ar2['id'].'").parents("tr").remove();  }},true);\' title="Удалить альбом"><img src="/template/standart/exit.png"></a></td></tr>';
			}
			$text .= '</table>';
			$text .= $C.'</div>';
		}
	}
	return $text;
}
?>