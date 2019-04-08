<?
$G=$_GET; if ($G["imagemaster"]=="createimagemaster") { $ROOT=$_SERVER['DOCUMENT_ROOT']; $GLOBAL["sitekey"]=1; @require($ROOT."/modules/standart/DataBase.php"); if ($GLOBAL["database"]==1) { list($picname, $returntext, $logtext)=CreateImageMaster((int)$G["id"], $G["pic"], urldecode($name)); echo "<img src='/userfiles/imagemaster/".$picname."' /><br><br>".$logtext; } else { echo "[BD ERROR]"; }}


//==============================================================================================================================================================================
//==============================================================================================================================================================================


function InitImageMaster($cap, $pic) { if ((int)$_SESSION["userid"]!=0) { $data=DB("SELECT `id`,`name` FROM `_imagemaster`"); if ($data["total"]>0) { $text="<div style='padding:10px; border:1px solid #999; border-radius:5px; font:11px/14px Tahoma !important;'>Получить шаблон: "; for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $text.="<a target='_blank' href='/modules/standart/ImageMaster.php?imagemaster=createimagemaster&id=".$ar["id"]."&pic=".$pic."&name=".urlencode($cap)."'><b><u>".$ar["name"]."</u></b></a>   "; endfor; $text.="</div>"; return $text; }}}


//==============================================================================================================================================================================
//==============================================================================================================================================================================


function CreateImageMaster($id=1, $picname, $name) {
	$ROOT=$_SERVER['DOCUMENT_ROOT']; $art=explode("/", $name); $name=$art[0];
	$imagick=extension_loaded("imagick"); if (!$imagick){$picpath='';$returntext="<div class='ErrorDiv'>Вам необходимо установить <b>imagick</b>!</div>".$C10;}else{$data=DB("SELECT * FROM `_imagemaster` WHERE (`id`='".(int)$id."') LIMIT 1");
	if ($data["total"]!=1) { $picpath=''; $returntext="<div class='ErrorDiv'>Ошибка определения шаблона социальных иллюстраций!</div>".$C10; } else { @mysql_data_seek($data["result"], 0); $ar=@mysql_fetch_array($data["result"]);
	//==============================================================================================================================================================================
		list($wpd, $hpd)=getimagesize($ROOT."/userfiles/imagemaster/podlog/".$ar["podlogpic"]); list($wpc, $hpc)=getimagesize($ROOT."/userfiles/".$ar["typepic"]."/".$picname);
		$color=str_replace("#", "", $ar["fontcolor"]); $ar["typeform"]=str_replace(",", ".", $ar["typeform"]);
				
		### Накладываем картинку на подложку
		$image=new Imagick(); $image->readImage($ROOT."/userfiles/imagemaster/podlog/".$ar["podlogpic"]); $wm=new Imagick(); $wm->readImage($ROOT."/userfiles/".$ar["typepic"]."/".$picname); 
		$wm->resizeImage(round($wpc*$ar["typeform"]), round($hpc*$ar["typeform"]), Imagick::FILTER_LANCZOS, 1); $wm->adaptiveSharpenImage(4,1); 
		if ((int)$ar["round"]!=0) { $wm->roundCorners((int)$ar["round"], (int)$ar["round"]); } $image->compositeImage($wm, imagick::COMPOSITE_OVER, (int)$ar["posx"], (int)$ar["posy"]);
		
		### определяем размеры текстового поля
		if ($ar["posit"]=="r" || $ar["posit"]=="l") { $textw=round($wpd-3*$ar["posx"]-$wpc*$ar["typeform"]); $texth=$hpd-2*$ar["posy"];
		} elseif ($ar["posit"]=="d" || $ar["posit"]=="u") { $textw=$wpd-2*$ar["posx"]; $texth=round($hpd-3*$ar["posy"]-$wph*$ar["typeform"]);
		} else { $textw=$wpd-2*$ar["posx"]; $texth=$hpd-2*$ar["posy"]; }
		$logtext.="Размер текстового поля: $textw * $texth пикселей [ положение = ".$ar["posit"]." ]<br>";
		
		$imagetext=new Imagick(); $imagetext->newImage($textw, $texth, 'none'); $lines=array(); $words=explode(" ", $name); $stlen=mb_strlen($name,'UTF-8');
		
		if (count($words)<2 || $stlen<$ar["fontchars"]) { $lines[]=$name; } else { $k=0; foreach($words as $i=>$word) { if(mb_strlen($lines[$k].$word,'UTF-8')<($ar["fontchars"]-1)){ $lines[$k].=$word.' ';}else{ $k++; $lines[$k].=$word.' '; }}} 
		$logtext.="Размер текстового поля: $stlen символов, ".count($lines)." линий по $ar[fontchars] знаков на линии<br>";
		foreach ($lines as $k=>$line) { $dx=0;
			$text = new ImagickDraw(); $text->setFontSize((int)$ar["fontsize"]); $text->setFont($ROOT."/userfiles/imagemaster/fonts/".$ar["fontfamily"]); $text->setFillColor('#'.$color);
			//$ee=$text->getImageWidth(); 
			if ($ar["posit"]=="d" || $ar["posit"]=="u") {
				$dx=round(($wpd-2*$ar["posx"])/2-($ar["fontsize"]*0.48*mb_strlen($line,'UTF-8'))/2);
				$logtext.="Ширина линии ".($k+1)." = ".mb_strlen($line,'UTF-8')." символов = ".($ar["fontsize"]*0.56*mb_strlen($line,'UTF-8'))." пикселей<br />";
			}
			
			/* спец условия по определенным сайтам и шаблонам */
			if ($id==3) { 
				$dx=round(($wpd-2*$ar["posx"])/2-($ar["fontsize"]*0.56*mb_strlen($line,'UTF-8'))/2);
				//$text->setStrokeColor("rgb(255,255,255)"); $text->setStrokeAntialias(true); $text->setStrokeWidth(1);
			}
			
			$imagetext->annotateImage($text, 0+$dx, round((int)$ar["fontsize"]+6)*($k+1), 0, $line); $text->destroy();
		}
		
		if ($ar["posit"]=="r") { 
			
			$tposx=round(2*$ar["posx"]+$wpc*$ar["typeform"]);
			$tposy=round($hpd/2-($ar["fontsize"]+3)*count($lines)/2);
			if ($id==2) { $tposy=$tposy-20; }
			
		} elseif ($ar["posit"]=="l") { $tposx=round($ar["posx"]); $tposy=round($hpd/2-($ar["fontsize"]+3)*count($lines)/2);
		} elseif ($ar["posit"]=="d") { $tposx=$ar["posx"]; $tposy=round(1.8*$ar["posy"]+$hpc*$ar["typeform"]);
		//} elseif ($ar["posit"]=="d") { $tposx=$ar["posx"]; $tposy=round($hpd-(($ar["fontsize"])*(count($lines)+1)));
		} elseif ($ar["posit"]=="u") { $tposx=$ar["posx"]; $tposy=round($ar["fontsize"]+5);
		} else { $tposx=$ar["posx"]; $tposy=round($hpd/2-($ar["fontsize"]+5)*count($lines)); }
		$logtext.="Положение текстового поля: x=".$tposx.", y=".$tposy;
		
		$image->compositeImage($imagetext, imagick::COMPOSITE_OVER, $tposx, ($tposy-7)); $image->setImageCompression(Imagick::COMPRESSION_JPEG); $image->setImageCompressionQuality(98);
		$image->writeImage($ROOT."/userfiles/imagemaster/".$picname); $image->destroy(); $returntext="<div class='SuccessDiv'>Иллюстрация создана по вашим настройкам!</div>".$C10;
	
	}} //=============================================================================================================================================================================
	return array($picname, $returntext, $logtext);
}
//==============================================================================================================================================================================
//==============================================================================================================================================================================
?>