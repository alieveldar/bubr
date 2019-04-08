<?
### НАСТРОЙКИ САЙТА
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { $AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); $GLOBAL["error"]=1; } else {
	@mysql_data_seek($data["result"], 0); $raz=@mysql_fetch_array($data["result"]);

	// ВЫВОД ПОЛЕЙ И ФОРМ
	$data=DB("SELECT * FROM `".$alias."_items` WHERE (`id`='".(int)$id."') LIMIT 1"); 
	if ($data["total"]!=1) { $AdminText=ATextReplace('ItemError', $raz["shortname"]." (".$alias.")", $id); $GLOBAL["error"]=1; } else {
		
	### Заполнение данных
	@mysql_data_seek($data["result"], 0); $node=@mysql_fetch_array($data["result"]);
	$todata=ToRusData($node["todata"]);
	$cats=explode(",", trim($node["cats"], ","));
	if ($node["stat"]==1) { $chk="checked"; }
	if ($node["vip"]==1) { $chk1="checked"; }
	
	// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	$P=$_POST;
	if (isset($P["savebutton"])) {
		require 'actionsResizePhoto.php';
		$cats=","; foreach ($P["cats"] as $k=>$v) { $cats.=$k.","; }
		$ar=explode(".", $P["todata"]); $sdata1=mktime(0, 0, 0, $ar[1], $ar[0], $ar[2]);
		$pic = $node['pic'];
		
		if($pic != $P["pic"]){				
			if($pic) { foreach ($GLOBAL['AutoPicPaths'] as $path=>$size) { @unlink($ROOT."/userfiles/".$path."/".$pic); }}
			$pic = $P["pic"];				
			if($pic) { actionsResizePhoto($pic); rename($ROOT."/userfiles/temp/".$pic, $ROOT."/userfiles/picoriginal/".$pic); }
		}
		
		$q="UPDATE `".$alias."_items` SET 
		`todata`='".$sdata1."',
		`name`='".$P["name"]."',		
		`uid`=".(int)$P["uid"].", 
		`vip`=".(int)$P["vip"].", 
		`pic`='".$pic."',
		`site`='".$P["site"]."',
		`urik`='".$P["urik"]."',
		`cats`='".$cats."'
		WHERE (id='".(int)$id."')";
		
		///echo $q;
		DB($q); $_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно сохранена!</div>"; @header("location: ".$_SERVER["REQUEST_URI"]); exit();
	}
		
	$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."' enctype='multipart/form-data' method='post'>";

	### Основные данные	
	$AdminText.="<div class='RoundText'><table>".'
	<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Название компании</td><td class="LongInput"><input name="name" id="name" type="text" value=\''.$node["name"].'\'></td><tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:10px;">Логотип компании</td><td class="LongInput"><div class="uploaderCon" style="'.($node['pic'] ? 'display:none;' : '').'"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
	if($node['pic']) $AdminText.='<span class="imgCon"><img src="/userfiles/picpreview/'.$node['pic'].'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="pic" value="'.$node['pic'].'" /></span>';
	$AdminText.='</div></td></tr>';
	$AdminText.='<tr class="TRLine1"><td class="VarText">Сайт</td><td class="LongInput"><input name="site" type="text" value="'.$node["site"].'"></td><tr>
	<tr class="TRLine0"><td class="VarText">ОГРН и ЮрИнфо</td><td class="LongInput"><input name="urik" type="text" value=\''.$node["urik"].'\'></td><tr>
	<tr class="TRLine0"><td class="VarText">ID пользователя</td><td class="SmallInput"><input name="uid" type="text" value="'.$node["uid"].'"></td><tr>
	<tr class="TRLine1"><td class="VarText">VIP позиция</td><td class="LongInput"><input type="checkbox" name="vip" id="vip" value="1" '.$chk1.'></td><tr>	
	<tr class="TRLine0"><td class="VarText">Дата действия</td><td class="DateInput"><input id="datepick" name="todata" type="text" readonly value="'.$todata[5].'"></td><tr>
	'."</table></div>";
	
	### Рубрикатор
	$catsText=""; $ul=0; $mvl=array(); $items=array(); $prlvl=0;
	$data=DB("SELECT * FROM `".$alias."_cats` WHERE (`type`=1) ORDER BY `name`");
	$items[0]["id"]=0; $items[0]["pid"]=0; for ($i=1; $i<=$data["total"]; $i++): @mysql_data_seek($data["result"], ($i-1));
	$ar=@mysql_fetch_array($data["result"]); $idr=$ar["id"]; $items[$idr]["id"]=$ar["id"]; $items[$idr]["pid"]=$ar["pid"]; $items[$idr]["name"]=$ar["name"]; $items[$idr]["text"]=$ar["text"]; 
	$items[$idr]["pic"]=$ar["pic"]; $items[$idr]["stat"]=$ar["stat"]; $items[$idr]["link"]="/".$alias."/cat/".$ar["id"]."/"; endfor; $stotal=$data["total"]+1; 
	GetChild_(0); $AdminText.="<h2>Категории</h2><div class='InfoH2'>Выберите категории, в которых будет размещаться компания:</div><div class='RoundText Catalog'>".$catsText."</div>";
	
	/*
	### Список тэгов публикцаций
	$tags=""; $data=DB("SELECT `id`, `name` FROM `_tags` ORDER BY `name` ASC"); $line=1; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]);
	if (in_array($ar["id"], $utags)) { $chkt="checked"; } else { $chkt=""; }$tags.="<td width='1%'><input name='tags[".$ar["id"]."]' id='tags[".$ar["id"]."]' type='checkbox' class='tags' value='1' $chkt></td>
	<td width='20%'>".$ar["name"]."</td>"; if (($i+1)%3==0) { $tags.="</tr><tr class='TRLine".($line%2)."'>"; $line++; if ($line==3) { $line=1; }} endfor;
	$AdminText.="<h2>Тэги публикации</h2><div class='InfoH2'>Выберите 2-4 темы, самые подходящие по смыслу публикации:</div><div class='RoundText TagsList'><table><tr class='TRLine0'>".$tags."</tr></table></div>";
	 * 
	 */

	### Сохранение
	$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА
	$AdminRight="<br><br>
	<div class='SecondMenu2'><a href='?cat=".$alias."_edit&id=".$id."'>Основные настройки</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_text&id=".$id."'>Основное содержание</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_contacts&id=".$id."'>Контакты и часы работы</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_pics&id=".$id."'>Фотографии компании</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_consults&id=".$id."'>Консультации</a></div>
	<div class='SecondMenu'><a href='?cat=".$alias."_actions&id=".$id."'>Акции</a></div>
	$C5<div class='SecondMenu'><a href='/$alias/view/$id/' target='_blank'>Просмотр</a></div>
	<br><div class='RoundText'><table><tr class='TRLine'><td class='CheckInput'><input type='checkbox' id='RS-".$id."-".$alias."_items' ".$chk."></td><td><b>Материал опубликован</b></td></tr></table></div>";
	}
	}
}

function GetChild_($i, $lvl=-1) {
	global $catsText, $items, $mvl; if ($i!=0) { $catsText.=HtmlChild_($lvl, $i); }
	foreach ($items as $key=>$item) { if ($item["pid"]==$items[$i]["id"]) { $pid=$item["pid"]; if ($mvl[$pid]==0) { $lvl++; $mvl[$pid]=1; } if ($key!=0) { GetChild_($key, $lvl); }}} 
} 

function HtmlChild_($lvl, $idi) {
	global $items, $cats, $prid, $prpid, $prlvl, $ul, $r, $am; $pid=$items[$idi]["pid"]; if ($prlvl>$lvl) { for ($k=0; $k<($prlvl-$lvl); $k++) { $text.="</p></ins>"; }} if (is_array($cats) && in_array($idi, $cats)) { $chk="checked"; }
	if($lvl > 0) {
		if($items[$idi]["stat"]) $text.="<ins class='CatalogLvl".$lvl."'><input name='cats[".$idi."]' id='cats[".$idi."]' value='1' type='checkbox' ".$chk."><a href='".$items[$idi]["link"]."' title='".trim($items[$idi]["name"])."' class='CatalogLvl".$lvl."' target='_blank'>".trim($items[$idi]["name"])."</a>";
		else $text.="<ins class='CatalogLvl".$lvl."'><input name='cats[".$idi."]' id='cats[".$idi."]' value='1' type='checkbox' ".$chk."><span title='".trim($items[$idi]["name"])."' class='CatalogLvl".$lvl."'>".trim($items[$idi]["name"])."</span>";
	}
	else $text.="<ins class='CatalogLvl".$lvl."'><span title='".trim($items[$idi]["name"])."' class='CatalogLvl".$lvl."'>".trim($items[$idi]["name"])."</span>";
	if (HaveChild_($idi)==1) { $text.=$r."<p>".$r; } else { $text.="</ins>".$r; } $prid=$idi; $prpid=$pid; $prlvl=$lvl; return $text;
}

function HaveChild_($id) { global $items; foreach ($items as $key=>$item) {  if ($item["pid"]==$id) { return 1; }} return 0; }

$_SESSION["Msg"]="";
?>