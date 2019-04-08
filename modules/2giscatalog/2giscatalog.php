<? 
	$data=DB("SELECT `sets`, `stat` FROM `_pages` WHERE (`link`='".$dir[0]."')");
	@mysql_data_seek($data["result"],0); $ar=@mysql_fetch_array($data["result"]);
	if(!$ar["stat"]){
		$cap="Материал не найден";
		$text=@file_get_contents($ROOT."/template/404.html");
		$Page["Content"]=$text; $Page["Caption"]=$cap;
	}
	else{
	$sets = explode('|', $ar["sets"]);
	
	$Page['Content'] = '<div class="C15"></div><script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script><script src="http://catalog.api.2gis.ru/assets/apitracker.js?version=1.3"></script><script src="http://catalog.api.2gis.ru/assets/apitracker.js?version=1.3"></script><div class="WhiteBlock" style="background:#d9e6f0;"><div style="float:right; margin-top:-30px;"><div style="float:left;"><a href="http://api.2gis.ru/">Информация предоставлена </div><img src="http://altapress.ru/cityguide/img/2gis.png" hspace="3" /></a></div><form action="/modules/SubmitForm.php?bp='.$RealPage.'" id="search-form" name="word" method="POST"><table cellpadding="0" cellspacing="0"><tbody><tr><td class="Gis2Form"><div class="title">Что ищем?</div><input type="text" name="what" id="what" placeholder="Например такси..."><input type="hidden" name="what_submit" value="1"></td><td class="Gis2Form"><div class="title">Где ищем?</div><input type="text" name="where" id="where" placeholder="Например Горького..."><input type="hidden" name="what_submit" value="2"></td><td valign="bottom" class="Gis3Form"><input type="submit" id="submit" class="SaveButton" value="Найти!"></td></tr></tbody></table></form><div class="C"></div></div><div class="C10"></div>';	
	if (isset($_SESSION['Data']["what"])) { $P = $_SESSION['Data']; SD(); @header("location: /".$dir[0]."/search/".$P["what"]."/1/".$P["where"]); exit(); }
	

// #######################################################################################################################################

if ($dir[1]=='search') {
	$what=urldecode($dir[2]); 
	$page=urldecode($dir[3]); 
	$where=urldecode($dir[4]); 
	if ($page=='') { $page = 1; }
	$arr_na_kartu = array('arr_data' => array('name' => '','addres' => '', 'lon' => '','lat' => ''));
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='/".$dir[0]."'>Каталог</a> &raquo; Поиск по компаниям Казани</div>";
	$Page['Content'] .= '<h2>Результаты поиска:</h2>';
	$zapros = 'http://catalog.api.2gis.ru/search?what='.$what.'&where='.$sets[0].' '.$where.'&page='.$page.'&pagesize=25&key='.$sets[1].'&version=1.3&sort=relevance&output=xml';
	$xml = simplexml_load_file($zapros);
	if (!empty($xml) and $xml->response_code==200) { 
		$Page['Content'] .= '<div id="myMapId"></div><div class="C10"></div>';
		if($xml->advertising){
			$Page['Content'] .= "<div class='WhiteBlock AdvGis'>";
			$i = 1;
			foreach($xml->advertising->advertisement as $adv){
				$Page['Content'] .= "<h3><span>".$i.".</span> <a href='/".$dir[0]."/info/".$adv->firm_id."/".$adv->hash."'>".$adv->title."</a></h3><div class='Satelite2Gis'>".$adv->text."</div>";
				if($i < sizeof($xml->advertising->advertisement)) $Page['Content'] .= "<div class='C5'></div><div class='CBG'></div>";
				$i++;
			}
			$Page['Content'] .= "</div><div class='C25'></div>"; 
		}
		$count_str = count($xml->result->filial); 
		$i = 0;
		while ($i <= ($count_str-1)): 
			$name=$xml->result->filial[$i]->name;
			$num = ($i+1)+(($page-1)*25); 
			$Page['Content'] .= "<div class='WhiteBlock MainItem1Gis'><h3><span>".$num.
			".</span> <a href='/".$dir[0]."/info/".$xml->result->filial[$i]->id."/".$xml->result->filial[$i]->hash."'>".$name."</a></h3><div class='C5'></div>";
			if ($xml->result->filial[$i]->micro_comment) {
				$Page['Content'] .= "<div class='micro_comment'>" . $xml->result->filial[$i]->micro_comment . "</div>";
			}
			$Page['Content'] .= "<div class='Satelite2Gis'>Адрес: ".$xml->result->filial[$i]->address;
			if ($xml->result->filial[$i]->firm_group->count>1) { $Page['Content'] .= ", <a href='/".$dir[0]."/filials/".$xml->result->filial[$i]->firm_group->id."' class='filials'> Филиалов: ".$xml->result->filial[$i]->firm_group->count."</a>"; }
			$Page['Content'] .= "</div><div class='C'></div></div><div class='C10'></div>";
			$arr_na_kartu['arr_data'][$i]['name'] = "".$xml->result->filial[$i]->name; $arr_na_kartu['arr_data'][$i]['adress'] = "".$xml->result->filial[$i]->adress;
			$arr_na_kartu['arr_data'][$i]['lon'] = "".$xml->result->filial[$i]->lon; $arr_na_kartu['arr_data'][$i]['lat'] = "".$xml->result->filial[$i]->lat;
			$i++; 
		endwhile;
		$page_total = $xml->result['total']; $Page['Content'].=Pager2($page, 25, ceil($page_total/25), $dir[0].'/search/'.$what.'/[page]/'.$where);
		
		$Page['Content'] .= '<script type="text/javascript">DG.autoload(function() { var myMap = new DG.Map("myMapId"); myMap.setCenter(new DG.GeoPoint('.$arr_na_kartu['arr_data'][0]['lon'].','.$arr_na_kartu['arr_data'][0]['lat'].'), 12); myMap.controls.add(new DG.Controls.Zoom());';
		$i = 0; while ($i <= ($count_str-1)): { if (($arr_na_kartu['arr_data'][$i]['lon'] <> '') or ($arr_na_kartu['arr_data'][$i]['lat'] <> '')){
			$Page['Content'] .= 'var myBalloon'.$i.' = new DG.Balloons.Common({ geoPoint: new DG.GeoPoint('.$arr_na_kartu['arr_data'][$i]['lon'].','.$arr_na_kartu['arr_data'][$i]['lat'].'), contentHtml: "<a href=\'/'.$dir[0].'/info/'.$xml->result->filial[$i]->id.'/'.$xml->result->filial[$i]->hash.'\'>'.$arr_na_kartu['arr_data'][$i]['name'].'</a>"}); var myMarker'.$i.' = new DG.Markers.Common({ geoPoint: new DG.GeoPoint('.$arr_na_kartu['arr_data'][$i]['lon'].','.$arr_na_kartu['arr_data'][$i]['lat'].'), clickCallback: function() { if (! myMap.balloons.getDefaultGroup().contains(myBalloon'.$i.')) { myMap.balloons.add(myBalloon'.$i.'); } else { myBalloon'.$i.'.show(); }}}); myMap.markers.add(myMarker'.$i.');';
		}} $i++; endWhile;
		$Page['Content'] .= '});</script>';
		} else { $Page['Content'] .= "По Вашему запросу ничего не найдено"; }
}
	
// #######################################################################################################################################

if ($dir[1]=="") {
	$Page['Content'] .= "<div class='C10'></div>";
	$rubrics = 'http://catalog.api.2gis.ru/rubricator?where='.$sets[0].'&version=1.3&parent_id=&output=xml&key='.$sets[1];
	$xml_rubrics = @simplexml_load_file($rubrics); #var_dump($xml_rubrics);
	if (!empty($xml_rubrics)){ $count_str = count($xml_rubrics->result->rubric); $i=0;
	while ($i<=($count_str-1)): $Page['Content'] .= "<div class='WhiteBlock MainItem1Gis'>";
	$Page['Content'] .= "<h3><a href='/".$dir[0]."/rubrik/".$xml_rubrics->result->rubric[$i]->id."'>".$xml_rubrics->result->rubric[$i]->name."</a></h3><div class='C5'></div>";
	$rubrics_add = 'http://catalog.api.2gis.ru/rubricator?where='.$sets[0].'&version=1.3&parent_id='.$xml_rubrics->result->rubric[$i]->id.'&output=xml&key='.$sets[1];
	//$Page['Content'] .= $rubrics_add;
	$xml_rubrics_add = @simplexml_load_file($rubrics_add); 
	if (!empty($xml_rubrics_add)) { 
		$Page['Content'] .= '<div class="Satelite2Gis">'; 
		$i1=0; 
		while ($i1<=2):
			if ($xml_rubrics_add->result->rubric[$i1]->id>0) { $Page['Content'] .= "<a href='/".$dir[0]."/podrubrik/".$xml_rubrics_add->result->rubric[$i1]->name."'>".$xml_rubrics_add->result->rubric[$i1]->name."</a>, "; } 
		$i1++; 
		endwhile; 
		$Page['Content'] .= '<a href="/'.$dir[0].'/rubrik/'.$xml_rubrics->result->rubric[$i]->id.'">еще&#8230;</a></div>'; 
	} $i++;
	$Page['Content'] .= "<div class='C'></div></div><div class='C10'></div>"; endwhile; }
}
	
// #######################################################################################################################################
	
if ($dir[1]=='rubrik') { $rub=$dir[2];
	$zapros = 'http://catalog.api.2gis.ru/rubricator?where='.$sets[0].'&version=1.3&id='.$rub.'&output=xml&key='.$sets[1]; $xml = simplexml_load_file($zapros);
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>Каталог</a> &raquo; ".$xml->result->rubric[0]->name."</div>";
	
	$Page['Content'] .= '<h2>Подрубрики раздела:</h2><ul type="square">';
	$rubrics = 'http://catalog.api.2gis.ru/rubricator?where='.$sets[0].'&version=1.3&parent_id='.$rub.'&output=xml&key='.$sets[1]; 
	$xml_rubrics = simplexml_load_file($rubrics);
	if (!empty($xml_rubrics)){ 
		$count_str = count($xml_rubrics->result->rubric); 
		$i=0; 
		while ($i <= ($count_str-1)): 
			$name = $xml_rubrics->result->rubric[$i]->name;
			if ($xml_rubrics->result->rubric[$i]->id > 0) { 
				$Page['Content'] .= "<li class='GIS2Rub1'><a href='/".$dir[0]."/podrubrik/".$name."' title='".$name."'>".$name."</a></li>"; 
			} 
			$i++; 
		endwhile; 
	} 
	$Page['Content'] .= '</ul><div class="C"></div>';
}	

// #######################################################################################################################################

if ($dir[1]=='podrubrik') { 
	$podrub=urldecode($dir[2]); 
	$page=urldecode($dir[3]); 
	if ($page=='') $page = 1;  
	$arr_na_kartu = array('arr_data' => array('name' => '','addres' => '', 'lon' => '','lat' => ''));
	$zapros = 'http://catalog.api.2gis.ru/search?what='.$podrub.'&where='.$sets[0].'&page='.$page.'&pagesize=25&key='.$sets[1].'&version=1.3&sort=relevance&output=xml'; $xml = simplexml_load_file($zapros);
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>Каталог</a> &raquo; ".$podrub."</div>"; 
	if (!empty($xml) and $xml->response_code==200){ $Page['Content'] .= "<h2>Список компаний раздела:</h2><div id='myMapId'></div><div class='C15'></div>";
	$page_total = $xml->result['total']; $count_str = count($xml->result->filial); $i = 0; while ($i <= ($count_str-1)): $num = ($i+1)+(($page-1)*25);
	$Page['Content'] .= "<div class='WhiteBlock MainItem1Gis'>";
	$Page['Content'] .= '<h3><span>'.$num.".</span> <a href='/".$dir[0]."/info/".$xml->result->filial[$i]->id."/".$xml->result->filial[$i]->hash."'>".$xml->result->filial[$i]->name."</a></h3><div class='C5'></div>";
	$Page['Content'] .= "<div class='Satelite2Gis'>".($xml->result->filial[$i]->address ? "Адрес: ".$xml->result->filial[$i]->address : "");
	if ($xml->result->filial[$i]->firm_group->count>1) { $Page['Content'] .= ", <a href='/".$dir[0]."/filials/".$xml->result->filial[$i]->firm_group->id."' class='filials'> Филиалов: ".$xml->result->filial[$i]->firm_group->count."</a>"; }
	$arr_na_kartu['arr_data'][$i]['name'] = "".$xml->result->filial[$i]->name; $arr_na_kartu['arr_data'][$i]['adress'] = "".$xml->result->filial[$i]->adress;
	$arr_na_kartu['arr_data'][$i]['lon'] = "".$xml->result->filial[$i]->lon; $arr_na_kartu['arr_data'][$i]['lat'] = "".$xml->result->filial[$i]->lat;
	$i++; $Page['Content'] .= "</div><div class='C'></div></div><div class='C10'></div>"; endwhile; $Page['Content'] .= "<div class='C10'></div>";
	$Page['Content'].=Pager2($page, 25, ceil($page_total/25), $dir[0]."/podrubrik/".$podrub."/[page]");
	$Page['Content'] .= '<script type="text/javascript">DG.autoload(function() { var myMap = new DG.Map("myMapId"); myMap.setCenter(new DG.GeoPoint('.$arr_na_kartu['arr_data'][0]['lon'].','.$arr_na_kartu['arr_data'][0]['lat'].'), 12); myMap.controls.add(new DG.Controls.Zoom());';
	$i = 0; while ($i <= ($count_str-1)): { if (($arr_na_kartu['arr_data'][$i]['lon'] <> '') or ($arr_na_kartu['arr_data'][$i]['lat'] <> '')) {
		$Page['Content'] .= 'var myBalloon'.$i.' = new DG.Balloons.Common({ geoPoint: new DG.GeoPoint('.$arr_na_kartu['arr_data'][$i]['lon'].','.$arr_na_kartu['arr_data'][$i]['lat'].'), contentHtml: "<a href=\'/'.$dir[0].'/info/'.$xml->result->filial[$i]->id.'/'.$xml->result->filial[$i]->hash.'\'>'.$arr_na_kartu['arr_data'][$i]['name'].'</a>"}); var myMarker'.$i.' = new DG.Markers.Common({ geoPoint: new DG.GeoPoint('.$arr_na_kartu['arr_data'][$i]['lon'].','.$arr_na_kartu['arr_data'][$i]['lat'].'), clickCallback: function() { if (! myMap.balloons.getDefaultGroup().contains(myBalloon'.$i.')) { myMap.balloons.add(myBalloon'.$i.'); } else { myBalloon'.$i.'.show(); }}}); myMap.markers.add(myMarker'.$i.');';
	}} $i++; endWhile;
	$Page['Content'] .= '});</script>';	
	} else { $Page['Content'] .= "По Вашему запросу ничего не найдено"; }
}

// #######################################################################################################################################

if ($dir[1]=='info') { 
	$info=urldecode($dir[2]); $has=urldecode($dir[3]);
	$page=1;
	$zapros = "http://catalog.api.2gis.ru/profile?id=".$info."&output=xml&key=".$sets[1]."&version=1.3&hash=".$has; $xml = simplexml_load_file($zapros);
    	
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>Каталог</a> &raquo; ".$xml->profile->name."</div>";	
	
	if (!empty($xml)){ $res=@file_get_contents($xml->profile->register_bc_url); $Page['Content'] .= "<h2>".$xml->profile->name."</h2>";  ### РЕГИСТРАЦИЯ	
	if ($xml->profile->article) { $Page['Content'] .= "<div class='MainItem1Gis' style='font-size:12px; font-weight:normal;'>".$xml->profile->article."</div><div class='C15'></div>";	}
	if($xml->profile->lon && $xml->profile->lat) $Page['Content'] .= "<div id='myMapId'></div><div class='C15'></div>";	
	$Page['Content'] .= "<div class='MainItem1Gis'>Адрес: ".$xml->profile->city_name.", ".$xml->profile->address."<div class='C5'></div>"; $tel = 0; while ($tel <= 5):  
	if ($xml->profile->contacts->group->contact[$tel]['type']=='phone') $Page['Content'] .= "тел.: ".$xml->profile->contacts->group->contact[$tel]->value."<div class='C5'></div>";
	if ($xml->profile->contacts->group->contact[$tel]['type']=='fax') $Page['Content'] .= "факс.: ".$xml->profile->contacts->group->contact[$tel]->value."<div class='C5'></div>";
	if ($xml->profile->contacts->group->contact[$tel]['type']=='website') $Page['Content'] .= "<noindex>Сайт: <a target='_blank  ' href='".$xml->profile->contacts->group->contact[$tel]->value."' rel='nofollow'>".$xml->profile->contacts->group->contact[$tel]->alias."</a><noindex><div class='C5'></div>";
	if ($xml->profile->contacts->group->contact[$tel]['type']=='email') $Page['Content'] .= "<noindex>E-mail: <a href='mailto:".$xml->profile->contacts->group->contact[$tel]->value."' rel='nofollow'>".$xml->profile->contacts->group->contact[$tel]->value."</a><noindex><div class='C5'></div>";
	
	$tel++;	endwhile; 
	$Page['Content'] .= "</div><h2>Время работы:</h2><div class='C5'></div><table cellpadding=7 cellspacing=0 border='1' class='Table2Gis'>";
	$Page['Content'] .= "<tr class='Gis2TRNmaes'><td>Понедельник</td><td>Вторник</td><td>Среда</td><td>Четверг</td><td>Птяница</td><td>Суббота</td><td>Воскресенье</td></tr><tr class='Gis2TRDays'>";

	$Page['Content'] .= "<td>".$xml->profile->schedule->day[0]->working_hours[0]['from']." - ".$xml->profile->schedule->day[0]->working_hours[0]['to']; if ($xml->profile->schedule->day[0]->working_hours[1]['from']) {
	$Page['Content'] .= "<br />".$xml->profile->schedule->day[0]->working_hours[1]['from']." - ".$xml->profile->schedule->day[0]->working_hours[1]['to']; } $Page['Content'] .= "</td>";

	$Page['Content'] .= "<td>".$xml->profile->schedule->day[1]->working_hours[0]['from']." - ".$xml->profile->schedule->day[1]->working_hours[0]['to']; if ($xml->profile->schedule->day[1]->working_hours[1]['from']) {
	$Page['Content'] .= "<br />".$xml->profile->schedule->day[1]->working_hours[1]['from']." - ".$xml->profile->schedule->day[1]->working_hours[1]['to']; } $Page['Content'] .= "</td>";

	$Page['Content'] .= "<td>".$xml->profile->schedule->day[2]->working_hours[0]['from']." - ".$xml->profile->schedule->day[2]->working_hours[0]['to']; if ($xml->profile->schedule->day[2]->working_hours[1]['from']) {
	$Page['Content'] .= "<br />".$xml->profile->schedule->day[2]->working_hours[1]['from']." - ".$xml->profile->schedule->day[2]->working_hours[1]['to']; } $Page['Content'] .= "</td>";

	$Page['Content'] .= "<td>".$xml->profile->schedule->day[3]->working_hours[0]['from']." - ".$xml->profile->schedule->day[3]->working_hours[0]['to']; if ($xml->profile->schedule->day[3]->working_hours[1]['from']) {
	$Page['Content'] .= "<br />".$xml->profile->schedule->day[3]->working_hours[1]['from']." - ".$xml->profile->schedule->day[3]->working_hours[1]['to']; } $Page['Content'] .= "</td>";

	$Page['Content'] .= "<td>".$xml->profile->schedule->day[4]->working_hours[0]['from']." - ".$xml->profile->schedule->day[4]->working_hours[0]['to']; if ($xml->profile->schedule->day[4]->working_hours[1]['from']) {
	$Page['Content'] .= "<br />".$xml->profile->schedule->day[4]->working_hours[1]['from']." - ".$xml->profile->schedule->day[4]->working_hours[1]['to']; } $Page['Content'] .= "</td>";

	if ($xml->profile->schedule->day[5]->working_hours['from']<>'') { $Page['Content'] .= "<td>".$xml->profile->schedule->day[5]->working_hours['from']." - ".$xml->profile->schedule->day[5]->working_hours['to']."</td>";
	} else { $Page['Content'] .= "<td>выходной</td>"; }
	if ($xml->profile->schedule->day[6]->working_hours['from']<>'') { $Page['Content'] .= "<td>".$xml->profile->schedule->day[6]->working_hours['from']." - ".$xml->profile->schedule->day[6]->working_hours['to']."</td>";
	} else { $Page['Content'] .= "<td>выходной</td>"; }
	
	$Page['Content'] .= "</tr></table>";
	if ($xml->profile->comment) { $Page['Content'] .= "<div class='WhiteBlock MainItem1Gis' style='font-size:12px; font-weight:normal;'>".$xml->profile->comment."</div><div class='C15'></div>";	}
	$Page['Content'] .= "<div class='C15'></div>";
	if ($xml->profile->link->link) {
		$Page['Content'] .= '<div class="WhiteBlock MainItem1Gis" style="font-size:12px; font-weight:normal;"><a href="'.$xml->profile->link->link.'" target="_blank">'.$xml->profile->link->text.'</a></div><div class="C15"></div>';
	}
	if ($xml->profile->fas_warning) { $Page['Content'] .= "<div class='CenterText'>Предупереждение ФАС: ".$xml->profile->fas_warning."</div>";	}
	
	$Page['Content'] .= "<script type='text/javascript'>DG.apitracker.regBC('".$xml->profile->register_bc_url."');</script>";
	$Page['Content'] .= '<script type="text/javascript">DG.autoload(function() { var myMap = new DG.Map("myMapId"); myMap.setCenter(new DG.GeoPoint('.$xml->profile->lon.','.$xml->profile->lat.'), 15); myMap.controls.add(new DG.Controls.Zoom()); var myBalloon = new DG.Balloons.Common({ geoPoint: new DG.GeoPoint('.$xml->profile->lon.','.$xml->profile->lat.'), contentHtml: "'.$xml->profile->name.'"}); var myMarker = new DG.Markers.Common({ geoPoint: new DG.GeoPoint('.$xml->profile->lon.','.$xml->profile->lat.'), clickCallback: function() { if (! myMap.balloons.getDefaultGroup().contains(myBalloon)) { myMap.balloons.add(myBalloon); } else { myBalloon.show(); }}}); myMap.markers.add(myMarker); });</script>';
 }
	$zapros_other = "http://catalog.api.2gis.ru/search?what=".$xml->profile->rubrics->rubric."&where=".$sets[0]."&pagesize=25&key=".$sets[1]."&version=1.3&sort=relevance&output=xml"; $xml_other = simplexml_load_file($zapros_other); 
	if (!empty($xml_other) and $xml_other->response_code==200){ $Page['Content'] .= "<p>&nbsp;</p><h2>Другие компании раздела:</h2>";
	$count_str = count($xml_other->result->filial) > 3 ? 3 : $xml_other->result->filial; $num = 1; while ($num <= $count_str):
	$item = $xml_other->result->filial[rand(0, count($xml_other->result->filial)-1)];
	if($item->id == $info) continue;
	$Page['Content'] .= "<div class='WhiteBlock MainItem1Gis'>";
	$Page['Content'] .= '<h3><span>'.$num.".</span> <a href='/".$dir[0]."/info/".$item->id."/".$item->hash."'>".$item->name."</a></h3><div class='C5'></div>";
	$Page['Content'] .= "<div class='Satelite2Gis'>Адрес: ".$item->address;
	if ($item->firm_group->count>1) { $Page['Content'] .= ", <a href='/".$dir[0]."/filials/".$item->firm_group->id."' class='filials'> Филиалов: ".$item->firm_group->count."</a>"; }
	$num++; $Page['Content'] .= "</div><div class='C'></div></div><div class='C10'></div>"; endwhile; }}

// #######################################################################################################################################

if ($dir[1]=="filials") {
	$filials=urldecode($dir[2]); $hash=urldecode($dir[3]); $arr_na_kartu = array("arr_data" => array("name" => "","addres" => "", "lon" => "","lat" => ""));
	$Page['Content'] .= "<div id='myMapId'></div><div class='C15'></div>";
	$zapros = 'http://catalog.api.2gis.ru/firmsByFilialId?firmid='.$filials.'&where='.$sets[0].'&page=1&key='.$sets[1].'&version=1.3&sort=relevance&output=xml'; $xml=@simplexml_load_file($zapros);
	$Page["Crumbs"]="<div class='Crumbs'><a href='http://".$VARS['mdomain']."'>Главная</a> &raquo; <a href='http://".$RealHost."/".$dir[0]."'>Каталог</a> &raquo; ".$xml->result->filial[0]->name."</div>"; 
	if (!empty($xml)){ $count_str = count($xml->result->filial); $i = 0; while ($i <= ($count_str-1)): $num = $i+1; 
	$Page['Content'] .= "<div class='WhiteBlock MainItem1Gis'><h3><span>".$num.".</span> <a href='/".$dir[0]."/info/".$xml->result->filial[$i]->id."/".$xml->result->filial[$i]->hash."'>".$xml->result->filial[$i]->name."</a></h3><div class='Satelite2Gis'>".$xml->result->filial[$i]->address."</div>";	
	$arr_na_kartu['arr_data'][$i]['name'] = "".$xml->result->filial[$i]->name; $arr_na_kartu['arr_data'][$i]['adress'] = "".$xml->result->filial[$i]->adress;
	$arr_na_kartu['arr_data'][$i]['lon'] = "".$xml->result->filial[$i]->lon; $arr_na_kartu['arr_data'][$i]['lat'] = "".$xml->result->filial[$i]->lat;
	$i++; $Page['Content'] .= "<div class='C'></div></div><div class='C10'></div>"; endwhile; }

	$Page['Content'] .= '<script type="text/javascript">DG.autoload(function() { var myMap = new DG.Map("myMapId"); myMap.setCenter(new DG.GeoPoint('.$arr_na_kartu['arr_data'][0]['lon'].','.$arr_na_kartu['arr_data'][0]['lat'].'), 12); myMap.controls.add(new DG.Controls.Zoom()); ';
	$i = 0; while ($i <= ($count_str-1)): { if (($arr_na_kartu['arr_data'][$i]['lon'] <> '') or ($arr_na_kartu['arr_data'][$i]['lat']<>'')){
		$Page['Content'] .= 'var myBalloon'.$i.'= new DG.Balloons.Common({geoPoint: new DG.GeoPoint('.$arr_na_kartu['arr_data'][$i]['lon'].','.$arr_na_kartu['arr_data'][$i]['lat'].'), contentHtml: "<a href=\'/'.$dir[0].'/info/'.$xml->result->filial[$i]->id.'/'.$xml->result->filial[$i]->hash.'\'>'.$arr_na_kartu['arr_data'][$i]['name'].'</a>" }); var myMarker'.$i.'= new DG.Markers.Common({ geoPoint: new DG.GeoPoint('.$arr_na_kartu['arr_data'][$i]['lon'].','.$arr_na_kartu['arr_data'][$i]['lat'].'),  clickCallback: function() { if (! myMap.balloons.getDefaultGroup().contains(myBalloon'.$i.')) { myMap.balloons.add(myBalloon'.$i.'); } else { myBalloon'.$i.'.show(); }} });myMap.markers.add(myMarker'.$i.');';
	}} $i++; endWhile;
	$Page['Content'].= '});</script>';
	}
	}
?>