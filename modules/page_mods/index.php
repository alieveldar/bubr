<?	
//$file="_index-indexpage"; if (RetCache($file)=="true") { list($Page["Content"], $cap)=GetCache($file, 0); } else { list($Page["Content"], $cap)=CreateIndexPage(); SetCache($file, $Page["Content"]); } 
$Page["Caption"]=$VARS["sitename"]; if ($_SESSION["userid"]==1) { list($Page["Content"], $cap)=CreateIndexAdmin(); } else { list($Page["Content"], $cap)=CreateIndexPage(); }



// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###
// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###
// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###

function CreateIndexAdmin() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $UserSetsSite, $C, $C20, $C10, $C25, $C30, $C15, $USER; $text=''; $notin=array(0);
	$comrs=array(0=>2,3=>3,5=>4,8=>5,10=>6,13=>7,15=>8,18=>9,20=>10,23=>11,25=>12,28=>13,30=>14,33=>15,35=>16,38=>17,40=>18,43=>19,45=>20,48=>21,50=>22,53=>23,55=>24,58=>25);
	$start1="SELECT `post_lenta`.`id`,`post_lenta`.`name`,`post_lenta`.`lname`,`post_lenta`.`sname`,`post_lenta`.`pic`,`post_lenta`.`data`, `_users`.`role` FROM `post_lenta` LEFT JOIN `_users` ON `_users`.`id`=`post_lenta`.`uid` WHERE ";
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	/* выбираем коммерческие новости */
	$promo=array(); $data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`promo`=1) GROUP BY 1  ORDER BY `post_lenta`.`data` DESC LIMIT 30");
	if ($data["total"]>0) { for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $left=floor((time()-$ar["data"])/(24*60*60)); $promo[$left]=$ar; endfor; }
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	
		### Одна большая фотка - телевизор
		if ((int)$promo[0]["id"]!=0) {
			/* коммерческая */
			$ar=$promo[0]; $d=ToRusData($ar["data"]); $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } 
			if ($ar["pic"]!="") { $notin[]=$ar["id"]; $pic="<img src='/userfiles/picbig/".$ar["pic"]."' title='".$ar["name"]."' />"; if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
			$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaMainPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>".$C; }
		} else {
			/* обычная в телевизор */
			$data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`onind`=1 && `post_lenta`.`promo`!=1) GROUP BY 1  ORDER BY `post_lenta`.`data` DESC LIMIT 1");
			if ($data["total"]==1) { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } 
			if ($ar["pic"]!="") { $notin[]=$ar["id"]; $pic="<img src='/userfiles/picbig/".$ar["pic"]."' title='".$ar["name"]."' />"; if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
			$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaMainPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>".$C; }}
		}
		

		### Три верхние фотки - спецразмещение
		/*if ($GLOBAL["USER"]["id"]==1) { $text.="<div class='IndexLentaLists'>"; $table="post_lenta";  $old=0;
		$data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`spec`=1 && `post_lenta`.`promo`!=1 && `post_lenta`.`id` NOT IN (".implode(',',$notin).")) GROUP BY 1 ORDER BY `post_lenta`.`data` DESC LIMIT 3");
		for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			$notin[]=$ar["id"]; $d=ToRusData($ar["data"]); $old=$ar["data"]; $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; }
			if ($ar["pic"]!="") { $pic="<img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' />"; } else { $pic=""; } if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
			if (($i+1)%2==0) { $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='Banner-2-1' style='display:inline-block !important; padding:0 5px;'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
			} else { $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='NewsOneList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>"; }
		endfor; $text.="</div>".$C; }*/
				
		### Две большие фотки - спецразмещение
		$data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`spec`=1 && `post_lenta`.`promo`!=1 && `post_lenta`.`id` NOT IN (".implode(',',$notin).")) GROUP BY 1  ORDER BY `post_lenta`.`data` DESC LIMIT 3");
		if ($data["total"]>=2) {
			$text.="<div class='IndexLentaSpecPics'>";
			@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); if ((int)$promo[1]["id"]!=0) { $ar=$promo[1]; } else { $notin[]=$ar["id"]; } 
			$d=ToRusData($ar["data"]); $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
			$pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaSpecPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
			@mysql_data_seek($data["result"], 1); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
			$notin[]=$ar["id"];  $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaSpecPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
			$text.="</div>".$C;	
		}
		if ($data["total"]==3) { @mysql_data_seek($data["result"], 2); $ak=@mysql_fetch_array($data["result"]); }
		
		$text.='<noindex><div id="Banner-3-1"></div></noindex>';		
		
		
		### ФОТОАЛЬБОМ
		$data=DB("SELECT `id`,`name`,`photofromusers` FROM `photos_albums` WHERE (`stat`=1 && `spec`=1) ORDER BY `id` DESC LIMIT 1");
		if ($data["total"]==1) { @mysql_data_seek($data["result"], 0); $alb=@mysql_fetch_array($data["result"]); $mainpic=''; $allpics=''; $addpics='';
		$datap=DB("SELECT `id`,`pic` FROM `photos_photos` WHERE (`stat`='1' && `pid`='$alb[id]') ORDER BY `main` DESC, `id` DESC LIMIT 5"); 
		if ($datap["total"]>0) {
			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
			if ($ak!="") { $d=ToRusData($ak["data"]); $role=""; if ($ak["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } if ($ak["sname"]!="") {$kryshka="<b>".$ak["sname"]."</b>"; } else { $kryshka="";}
			$notin[]=$ak["id"]; $text.="<div class='IndexLentaSpecPics' style='float:left; width:500px;'>"; $pic="<img src='/userfiles/picmiddle/".$ak["pic"]."' title='".$ak["name"]."' />";
			$text.="<a href='/post/view/".$ak["id"]."' class='IndexLentaSpecPic' id='NewsLentaList-".$ak["id"]."' style='margin-bottom:0px;'>".$pic.$kryshka.GetSpanCaption($ak["name"], $ak["lname"]).$role."</a></div>"; }
			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
			for ($i=0; $i<$datap["total"]; $i++): @mysql_data_seek($datap["result"], $i); $ar=@mysql_fetch_array($datap["result"]); if (($i+1)==$datap["total"]) {
			if ($alb["photofromusers"]==1) { $allpics.='<a href="/photos/addphoto/'.$alb["id"].'"><img src="/modules/photoalbum/add.png"></a>';	} else { $allpics.='<a href="/photos/view/'.$alb["id"].'#pic'.$ar["id"].'"><img src="/userfiles/picsquare/'.$ar["pic"].'"></a>'; }
			} else { $allpics.='<a href="/photos/view/'.$alb["id"].'#pic'.$ar["id"].'"><img src="/userfiles/picsquare/'.$ar["pic"].'"></a>'; } endfor;
			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
		$text.='<div class="AlbOnMain"><div class="AddPicsOnMain"><div class="TheOne" style="position:relative;overflow:hidden;">'. $allpics. $addpics.'</div><a href="/photos/view/'.$alb["id"].'" id="AlbumLink">'.GetSpanCaption($alb["name"]).'</a></div></div>'.$C.$C30;
		$text.='<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><!-- Bubr.ru - 970x90 --><ins class="adsbygoogle" style="display:inline-block;width:970px;height:90px" data-ad-client="ca-pub-2073806235209608" data-ad-slot="3548861810"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'.$C30; }}
		
		
		### Остальные фотки - обычные записи
		$text.="<div class='IndexLentaLists' id='IndexLentaLists'>"; $old=0; $table="post_lenta"; $limit=60; $ggl=0; $news=array();
		$data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`promo`!=1 && `post_lenta`.`id` NOT IN (".implode(',',$notin).")) GROUP BY 1 ORDER BY `post_lenta`.`data` DESC LIMIT ".$limit);
			
		$j=0; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
		if ((int)$comrs[$j]!=0) { $idp=$comrs[$j]; if((int)$promo[$idp]["id"]!=0) { $news[]=$promo[$idp]; $j++; }} $news[]=$ar; $j++; endfor;	
			
		foreach($news as $ar):
			$d=ToRusData($ar["data"]);	$old=$ar["data"]; $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; }
			if ($ar["pic"]!="") {
				if (in_array(($i+1)%5, array(4,0))) { $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; }
				if (in_array(($i+1)%5, array(1,2,3))) { $pic="<img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' />"; }
			} else { $pic=""; }
			if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}	
			$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
			if (($i+1)%5==0) {
				$text.=$C; $ggl++;
				if ($ggl==2) { $text.='<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><!-- Bubr.ru - 970x90 --><ins class="adsbygoogle" style="display:inline-block;width:970px;height:90px" data-ad-client="ca-pub-2073806235209608" data-ad-slot="3548861810"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'.$C30; }
			}
		endforeach;	
		$text.="</div>".$C;
		
		if ($data["total"]==$limit) { $text.="<div id='LoadMore'><a href='javascript:void(0);' onclick=\"IndexLoadMore('$old','$limit');\">Показать ещё</a></div>"; }
		$text.=$C20.$C20."<div style='height:70px;'>".$node["text"]."</div>".$C10;
		
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	return(array($text, ""));
}

// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###
// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###
// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###// ###

function CreateIndexPage() {
	global $VARS, $GLOBAL, $dir, $Page, $node, $UserSetsSite, $C, $C20, $C10, $C25, $C30, $C15, $USER; $text=''; $notin=array(0);
	$start1="SELECT `post_lenta`.`id`,`post_lenta`.`name`,`post_lenta`.`lname`,`post_lenta`.`sname`,`post_lenta`.`pic`,`post_lenta`.`data`, `_users`.`role` FROM `post_lenta` LEFT JOIN `_users` ON `_users`.`id`=`post_lenta`.`uid` WHERE ";
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
		### Одна большая фотка - телевизор
		$data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`onind`=1 && `post_lenta`.`promo`!=1) GROUP BY 1  ORDER BY `post_lenta`.`data` DESC LIMIT 1");
		if ($data["total"]==1) { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);
			$role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } 
			if ($ar["pic"]!="") {
				$notin[]=$ar["id"]; $pic="<img src='/userfiles/picbig/".$ar["pic"]."' title='".$ar["name"]."' />";
				if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
				$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaMainPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>".$C;
			}
		}

		### Три верхние фотки - спецразмещение
		if ($GLOBAL["USER"]["id"]==1) { $text.="<div class='IndexLentaLists'>"; $table="post_lenta";  $old=0;
		$data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`spec`=1 && `post_lenta`.`promo`!=1 && `post_lenta`.`id` NOT IN (".implode(',',$notin).")) GROUP BY 1 ORDER BY `post_lenta`.`data` DESC LIMIT 3");
		for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]);
			$notin[]=$ar["id"]; $d=ToRusData($ar["data"]); $old=$ar["data"]; $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; }
			if ($ar["pic"]!="") { $pic="<img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' />"; } else { $pic=""; } if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
			if (($i+1)%2==0) { $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='Banner-2-1' style='display:inline-block !important; padding:0 5px;'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
			} else { $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='NewsOneList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>"; }
		endfor; $text.="</div>".$C; }
		
				
		### Две большие фотки - спецразмещение
		$data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`spec`=1 && `post_lenta`.`promo`!=1 && `post_lenta`.`id` NOT IN (".implode(',',$notin).")) GROUP BY 1  ORDER BY `post_lenta`.`data` DESC LIMIT 3");
		if ($data["total"]>=2) {
			$text.="<div class='IndexLentaSpecPics'>";
			@mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
			$notin[]=$ar["id"];  $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaSpecPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
			@mysql_data_seek($data["result"], 1); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]); $role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}
			$notin[]=$ar["id"];  $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; $text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaSpecPic' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
			$text.="</div>".$C;	
		}
		if ($data["total"]==3) { @mysql_data_seek($data["result"], 2); $ak=@mysql_fetch_array($data["result"]); }
		
		$text.='<noindex><div id="Banner-3-1"></div></noindex>';		
		
		
		### ФОТОАЛЬБОМ
		$data=DB("SELECT `id`,`name`,`photofromusers` FROM `photos_albums` WHERE (`stat`=1 && `spec`=1) ORDER BY `id` DESC LIMIT 1");
		if ($data["total"]==1) { @mysql_data_seek($data["result"], 0); $alb=@mysql_fetch_array($data["result"]); $mainpic=''; $allpics=''; $addpics='';
		$datap=DB("SELECT `id`,`pic` FROM `photos_photos` WHERE (`stat`='1' && `pid`='$alb[id]') ORDER BY `main` DESC, `id` DESC LIMIT 5"); 
		if ($datap["total"]>0) {
			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
			if ($ak!="") { $d=ToRusData($ak["data"]); $role=""; if ($ak["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; } if ($ak["sname"]!="") {$kryshka="<b>".$ak["sname"]."</b>"; } else { $kryshka="";}
			$notin[]=$ak["id"]; $text.="<div class='IndexLentaSpecPics' style='float:left; width:500px;'>"; $pic="<img src='/userfiles/picmiddle/".$ak["pic"]."' title='".$ak["name"]."' />";
			$text.="<a href='/post/view/".$ak["id"]."' class='IndexLentaSpecPic' id='NewsLentaList-".$ak["id"]."' style='margin-bottom:0px;'>".$pic.$kryshka.GetSpanCaption($ak["name"], $ak["lname"]).$role."</a></div>"; }
			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
			for ($i=0; $i<$datap["total"]; $i++): @mysql_data_seek($datap["result"], $i); $ar=@mysql_fetch_array($datap["result"]); if (($i+1)==$datap["total"]) {
			if ($alb["photofromusers"]==1) { $allpics.='<a href="/photos/addphoto/'.$alb["id"].'"><img src="/modules/photoalbum/add.png"></a>';	} else { $allpics.='<a href="/photos/view/'.$alb["id"].'#pic'.$ar["id"].'"><img src="/userfiles/picsquare/'.$ar["pic"].'"></a>'; }
			} else { $allpics.='<a href="/photos/view/'.$alb["id"].'#pic'.$ar["id"].'"><img src="/userfiles/picsquare/'.$ar["pic"].'"></a>'; } endfor;
			// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
		$text.='<div class="AlbOnMain"><div class="AddPicsOnMain"><div class="TheOne" style="position:relative;overflow:hidden;">'. $allpics. $addpics.'</div><a href="/photos/view/'.$alb["id"].'" id="AlbumLink">'.GetSpanCaption($alb["name"]).'</a></div></div>'.$C.$C30;
		$text.='<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><!-- Bubr.ru - 970x90 --><ins class="adsbygoogle" style="display:inline-block;width:970px;height:90px" data-ad-client="ca-pub-2073806235209608" data-ad-slot="3548861810"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'.$C30; }}
		
		
		### Остальные фотки - обычные записи
		$text.="<div class='IndexLentaLists' id='IndexLentaLists'>"; $old=0; $table="post_lenta"; $limit=60; $ggl=0;
			$data=DB($start1."(`post_lenta`.`stat`=1 && `post_lenta`.`promo`!=1 && `post_lenta`.`id` NOT IN (".implode(',',$notin).")) GROUP BY 1 ORDER BY `post_lenta`.`data` DESC LIMIT ".$limit);
			for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $d=ToRusData($ar["data"]);	$old=$ar["data"];
			$role=""; if ($ar["role"]==0) { $role="<role>Добавлено<br>читателем</role>"; }
			if ($ar["pic"]!="") {
				if (in_array(($i+1)%5, array(4,0))) { $pic="<img src='/userfiles/picmiddle/".$ar["pic"]."' title='".$ar["name"]."' />"; }
				if (in_array(($i+1)%5, array(1,2,3))) { $pic="<img src='/userfiles/picsquare/".$ar["pic"]."' title='".$ar["name"]."' />"; }
			} else { $pic=""; }
			if ($ar["sname"]!="") {$kryshka="<b>".$ar["sname"]."</b>"; } else { $kryshka="";}	
			$text.="<a href='/post/view/".$ar["id"]."' class='IndexLentaList' id='NewsLentaList-".$ar["id"]."'>".$pic.$kryshka.GetSpanCaption($ar["name"], $ar["lname"]).$role."</a>";
			if (($i+1)%5==0) {
				$text.=$C; $ggl++;
				if ($ggl==2) { $text.='<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><!-- Bubr.ru - 970x90 --><ins class="adsbygoogle" style="display:inline-block;width:970px;height:90px" data-ad-client="ca-pub-2073806235209608" data-ad-slot="3548861810"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>'.$C30; }
			}
			endfor;
		$text.="</div>".$C;
		
		if ($data["total"]==$limit) { $text.="<div id='LoadMore'><a href='javascript:void(0);' onclick=\"IndexLoadMore('$old','$limit');\">Показать ещё</a></div>"; }
		$text.=$C20.$C20."<div style='height:70px;'>".$node["text"]."</div>".$C10;
		
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	return(array($text, ""));
}
?>
