<?

# Добавь свою статью на Bubr.ru - получи за это деньги. Что писать? Добавить Пост!


# ПРАВЫЙ БЛОК # Переменная $Page["RightContent"] может быть определена в запрашиваемых файлах # Если определен файл в папке /modules/page_mods/right_block/[поддомен].php берем его, иначе берем дефолтный правый блок /modules/page_mods/right_block/default.php
if ($link=="index") { /* Правая колонка главной страницы задается отдельно в index.php */ } else {
	if (is_file("modules/page_mods/right_block/new-".$Domains[$SubDomain].".php")) { @require("modules/page_mods/right_block/new-".$Domains[$SubDomain].".php"); $GLOBAL["log"].="<i>Подключение PHP</i>: правый блок &laquo;modules/page_mods/right_block/new-".$Domains[$SubDomain].".php&raquo; подключен<hr>";
	} elseif (is_file("modules/page_mods/right_block/default.php")) { @require("modules/page_mods/right_block/default.php"); $GLOBAL["log"].="<i>Подключение PHP</i>: правый блок &laquo;modules/page_mods/right_block/default.php&raquo; подключен<hr>";
	} else { $GLOBAL["log"].="<u>Подключение PHP</u>: правый блок не подключен<hr>"; }
}
# ЛЕВЫЙ БЛОК - СТАРТ # $Page["LeftContent"] может быть определена в запрашиваемых файлах # Если определен файл в папке /modules/page_mods/left_block/[поддомен].php берем его, иначе берем дефолтный левый блок /modules/page_mods/left_block/default.php
if ($link=="index") { /* Левая колонка главной страницы задается отдельно в index.php */ } else {
	if (is_file("modules/page_mods/left_block/new-".$Domains[$SubDomain].".php")) { @require("modules/page_mods/left_block/new-".$Domains[$SubDomain].".php"); $GLOBAL["log"].="<i>Подключение PHP</i>: левый блок &laquo;modules/page_mods/left_block/new-".$Domains[$SubDomain].".php&raquo; подключен<hr>";
	} elseif (is_file("modules/page_mods/left_block/default.php")) { @require("modules/page_mods/left_block/default.php"); $GLOBAL["log"].="<i>Подключение PHP</i>: левый блок &laquo;modules/page_mods/left_block/default.php&raquo; подключен<hr>";
	} else { $GLOBAL["log"].="<u>Подключение PHP</u>: левый блок не подключен<hr>"; }
}
# ВЕРХНИЙ БЛОК # Переменная $Page["TopContent"] может быть определена в запрашиваемых файлах # Если определен файл в папке /modules/page_mods/top_block/[поддомен].php берем его, иначе берем дефолтный правый блок /modules/page_mods/top_block/default.php
if ($link=="index") { /* Верхняя колонка главной страницы задается отдельно в index.php */ } else {
	if (is_file("modules/page_mods/top_block/new-".$Domains[$SubDomain].".php")) { @require("modules/page_mods/top_block/new-".$Domains[$SubDomain].".php"); $GLOBAL["log"].="<i>Подключение PHP</i>: верхний блок &laquo;modules/page_mods/top_block/new-".$Domains[$SubDomain].".php&raquo; подключен<hr>";
	} elseif (is_file("modules/page_mods/top_block/default.php")) { @require("modules/page_mods/top_block/default.php"); $GLOBAL["log"].="<i>Подключение PHP</i>: верхний блок &laquo;modules/page_mods/top_block/default.php&raquo; подключен<hr>";
	} else { $GLOBAL["log"].="<u>Подключение PHP</u>: верхний блок не подключен<hr>"; }
}

# НИЖНИЙ БЛОК # Переменная $Page["BottomContent"] может быть определена в запрашиваемых файлах # Если определен файл в папке /modules/page_mods/bottom_block/[поддомен].php берем его, иначе берем дефолтный правый блок /modules/page_mods/bottom_block/default.php
if ($link=="index") { /* Верхняя колонка главной страницы задается отдельно в index.php */ } else {
	if (is_file("modules/page_mods/bottom_block/new-".$Domains[$SubDomain].".php")) { @require("modules/page_mods/bottom_block/new-".$Domains[$SubDomain].".php"); $GLOBAL["log"].="<i>Подключение PHP</i>: нижний блок &laquo;modules/page_mods/bottom_block/new-".$Domains[$SubDomain].".php&raquo; подключен<hr>";
	} elseif (is_file("modules/page_mods/bottom_block/default.php")) { @require("modules/page_mods/bottom_block/default.php"); $GLOBAL["log"].="<i>Подключение PHP</i>: нижний блок &laquo;modules/page_mods/bottom_block/default.php&raquo; подключен<hr>";
	} else { $GLOBAL["log"].="<u>Подключение PHP</u>: нижний блок не подключен<hr>"; }
}

# ПОИСК ЯНДЕКС
//$Page["SiteSearch"]="<div class='ya-site-form ya-site-form_inited_no' onclick=\"return {'bg': 'transparent', 'target': '_self', 'language': 'ru', 'suggest': false, 'tld': 'ru', 'site_suggest': true, 'action': 'http://".$VARS["mdomain"]."/search/', 'webopt': false, 'fontsize': 11, 'arrow': false, 'fg': '#000000', 'searchid': '2043787', 'logo': 'rb', 'websearch': false, 'type': 3}\"><form action=\"http://yandex.ru/sitesearch\" method=\"get\" target=\"_self\"><input type=\"hidden\" name=\"searchid\" value=\"2043787\" /><input type=\"hidden\" name=\"l10n\" value=\"ru\" /><input type=\"hidden\" name=\"reqenc\" value=\"utf-8\" /><input type=\"text\" name=\"text\" value=\"\" /><input type=\"submit\" value=\"Найти\" /></form></div><style type=\"text/css\">.ya-page_js_yes .ya-site-form_inited_no { display: none; }</style><script type=\"text/javascript\">(function(w,d,c){var s=d.createElement('script'),h=d.getElementsByTagName('script')[0],e=d.documentElement;(' '+e.className+' ').indexOf(' ya-page_js_yes ')===-1&&(e.className+=' ya-page_js_yes');s.type='text/javascript';s.async=true;s.charset='utf-8';s.src=(d.location.protocol==='https:'?'https:':'http:')+'//site.yandex.net/v2.0/js/all.js';h.parentNode.insertBefore(s,h);(w[c]||(w[c]=[])).push(function(){Ya.Site.Form.init()})})(window,document,'yandex_site_callbacks');</script>";
$Page["SiteSearch"]="<div class=\"ya-site-form ya-site-form_inited_no\" onclick=\"return {'action':'http://bubr.ru/search/','arrow':false,'bg':'transparent','fontsize':12,'fg':'#000000','language':'ru','logo':'rb','publicname':'Yandex Site Search #2154055','suggest':false,'target':'_self','tld':'ru','type':3,'usebigdictionary':true,'searchid':2154055,'webopt':false,'websearch':false,'input_fg':'#000000','input_bg':'#ffffff','input_fontStyle':'normal','input_fontWeight':'normal','input_placeholder':'поиск по сайту','input_placeholderColor':'#000000','input_borderColor':'#7f9db9'}\"><form action=\"http://yandex.ru/sitesearch\" method=\"get\" target=\"_self\"><input type=\"hidden\" name=\"searchid\" value=\"2154055\"/><input type=\"hidden\" name=\"l10n\" value=\"ru\"/><input type=\"hidden\" name=\"reqenc\" value=\"\"/><input type=\"text\" name=\"text\" value=\"\"/><input type=\"submit\" value=\"Найти\"/></form></div><style type=\"text/css\">.ya-page_js_yes .ya-site-form_inited_no { display: none; }</style><script type=\"text/javascript\">(function(w,d,c){var s=d.createElement('script'),h=d.getElementsByTagName('script')[0],e=d.documentElement;if((' '+e.className+' ').indexOf(' ya-page_js_yes ')===-1){e.className+=' ya-page_js_yes';}s.type='text/javascript';s.async=true;s.charset='utf-8';s.src=(d.location.protocol==='https:'?'https:':'http:')+'//site.yandex.net/v2.0/js/all.js';h.parentNode.insertBefore(s,h);(w[c]||(w[c]=[])).push(function(){Ya.Site.Form.init()})})(window,document,'yandex_site_callbacks');</script>";

# ПОИСК ВСЕХ ТАБЛИЦ $modules=array("lenta", "concurs", "tatbrand");
function ToLocalDay($data) { return(str_replace(array(date("d.m.Y"), date("d.m.Y", time()-60*60*24)), array("Сегодня", "Вчера"), $data)); }
function NormalCaption($cap) { $cap=str_replace(array("\r","\n"), '', $cap); $cap=str_replace(array(':',',','.','!','-','?'), array(': ',', ','. ','! ','- ','? '), $cap); return trim($cap);}
function getLentasOnModules() { global $lentas; if (sizeof($lentas)==0) { $modules=array("lenta", "concurs", "tatbrand"); $q="SELECT `link` FROM `_pages` WHERE (`module` IN ('".implode("','", $modules)."')) LIMIT 50"; $data=DB($q); for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $lentas[$ar["link"]]=$ar["link"]."_lenta"; }} return $lentas;}

function getNewsFromLentas( $q = '', $endq = '' ) {
    global $used;
    $lentas = getLentasOnModules();
    $query = '';
    if ( false !== ( $limwpos = strpos( strtoupper( $endq ), 'LIMIT' ) ) &&
         ( $colonpos = strpos( $endq, ',', $limwpos ) ) ) {
        $oldlimit = explode( 'LIMIT', strtoupper( $endq ) );
        if ( 1 > (int) substr( $endq, $limwpos + 5, $colonpos - strlen( $endq ) ) ) {
            $newlimit = substr( $endq, $colonpos + 1 );
            $single_endq = str_replace( $oldlimit, ' ' . trim( $newlimit ), $endq );
        } else {
            $single_endq = str_replace('LIMIT' . $oldlimit[count($oldlimit) - 1], '', $endq);
        }
    } else {
        $single_endq = $endq;
    }
    foreach ( $lentas as $l => $t ) {
        $usedtext = "";
        if ( sizeof( $used[ $l ] ) > 0 ) {
            $usedtext = " && `" . $t . "`.`id` NOT IN (0, " . implode( ",", $used[ $l ] ) . ")";
        }
        $qitem = "(" . str_replace( array( "[table]", "[link]" ), array( $t, $l ), $q ) . $single_endq . ") UNION ALL ";
        $query .= str_replace( "[used]", $usedtext, $qitem );
    }
    $query = trim( $query, "UNION ALL" ) . ' ' . $endq;
    $data = DB( $query );

    return $data;
}
function GetVKGroup($w=300, $h=250) { global $C20, $C25, $C15, $C;  $id=rand(11111, 3333); $text='<div id="vk_groups_'.$id.'"></div><script type="text/javascript">VK.Widgets.Group("vk_groups_'.$id.'", {mode:0, width:"'.$w.'", height:"'.$h.'", color1:"FFFFFF", color2:"5E5E5E", color3:"385F88"},72226137);</script>'.$C20; return $text; }

function GetSpanCaption($cap, $ds='') {
	$cap="<span>".nl2br(trim($cap, "."))."</span>"; $cap=str_replace("<br />", "</span><br /><span>", $cap); $cap=str_replace("<span></span>", "", $cap); $cap=str_replace("<span> </span>", "", $cap);
	if ($ds!="") { $ds="<i>".nl2br(trim($ds, "."))."</i>"; $ds=str_replace("<br />", "</i><br /><i>", $ds); $ds=str_replace("<i></i>", "", $ds); $ds=str_replace("<i> </i>", "", $ds); $ds="<br>".$ds;  }
	$cap="<capt>".$cap.$ds."</capt>"; $cap=str_replace(array("\r","\n"), "", $cap); return $cap;
}
?>