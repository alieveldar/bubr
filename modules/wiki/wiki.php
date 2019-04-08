<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/modules/wiki/wiki.functions.php";

$forums = array(
    news => array("tms" => "8,42,45,41,39,37,43,38", "bg" => "#1A74BE"),
    auto => array("tms" => "30,31,32,33,34,35,36", "bg" => "#814D34"),
    sport => array("tms" => "25,26,24,23,22,21", "bg" => "#349934"),
    oney => array("tms" => "23,24,15,19,18,20,17,44,16", "bg" => "#EC7D46"),
    business => array("tms" => "9,10,11,12,13,14,8,42,45", "bg" => "#777"),
);

$alias = $link;

if ($start == "") {
    $start = "list";
    $dir[1] = "list";
}

switch ($start) {
    case 'list' :

        $text .= displayAlphabetBlock($dir[0] . "/letter");

        $catsListCacheFile  = $alias . "_lenta-" . $start . "." . $page . ".cats_list";
        $catsBlockText = '';
        //кеш категорий
        if (RetCache($catsListCacheFile) == "true") {
            list($catsBlockText, $cap) = GetCache($catsListCacheFile, 0);
        } else {
            $catsBlockText = displayCatsBlock($dir[0] . "/cat");
            SetCache($catsListCacheFile, $catsBlockText, "");
        }

        $text .= $catsBlockText;
        break;
    case 'letter' :
        $letter = $dir[2];

        $letter = rawurldecode($letter);

        $page = $dir[3] ? $dir[3] : 1;
        $catsListCacheFile  = $alias . "_lenta-" . $start . "." . $page . ".letter_list." . $letter;
        $rubricListText = '';
        //кеш категорий
        if (RetCache($catsListCacheFile) == "true") {
            list($catsBlockText, $captionText) = GetCache($catsListCacheFile, 0);
        } else {

            $captionText = "Определения на букву «{$letter}»";

            $itemsPerPage = $node["onpage"];
            $offset = ($page - 1) * $itemsPerPage;

            $rubricList = getRubricListByLetter($letter, $offset, $itemsPerPage);
            $rubricCount = getCountRubricByLetter($categoryId);

            $rubricListLetterBlock = displayRubricListBlock($rubricList, $dir[0], true);
            $rubricListPagerBlock = Pager2($pg, $itemsPerPage, ceil($rubricCount / $itemsPerPage), $dir[0] . "/" . $dir[1] . "/" . $dir[2] . "/[page]");

            $rubricListText = $rubricListLetterBlock . $rubricListPagerBlock;
            SetCache($catsListCacheFile, $rubricListText, $captionText);
        }


        $Page["Caption"] = $captionText;
        $text .= $rubricListText;

        break;
    case 'view' :
        $itemId = (int)$dir[2];
        $rubricItem = getRubricById($itemId);

        $itemCacheFile  = $alias . "_lenta-" . $itemId . ".view";

        if (RetCache($itemCacheFile) == "true") {
            list($text, $cap) = GetCache($itemCacheFile);
        } else {
            $rubricItem = getRubricById($itemId);

            if ($rubricItem == null) {
                $cap = "Материал не найден";
                $text = @file_get_contents($ROOT . "/template/404.html");
                $Page["Content"] = $text;
                $Page["Caption"] = $cap;
            } else {
                $categoryId = (int)$rubricItem['cat'];
                $category = getWikiCategoryById($categoryId);

                $Page["Caption"] = $rubricItem['name'];
                $Page["Description"] = $rubricItem["ds"];
                $Page["KeyWords"] = $rubricItem["kw"];

                if ($GLOBAL["USER"]["role"] > 1) {
                    $text = $C10 . "<div id='AdminEditItem'><a href='" . $GLOBAL["mdomain"] . "/admin/?cat=" . $link . "_edit&id=" . (int)$dir[2] . "'>Редактировать</a></div>" . $C15 . InitImageMaster($cap, $new["pic"]) . $C15 . $text;
                }

                $text .= displayRubricItem($rubricItem, $dir[0], $forums);
                SetCache($itemCacheFile, $text, $rubricItem['name']);
            }


        }

        break;
    case 'cat' :
        $categoryId = (int)$dir[2];
        $categoryItem = getWikiCategoryById($categoryId);

        $Page["Caption"] = "{$categoryItem['name']}";

        $itemsPerPage = $node["onpage"];
        $pg = $dir[3] ? $dir[3] : 1;
        $offset = ($pg - 1) * $itemsPerPage;

        $rubricList = getRubricListByCategoryId($categoryId, $offset, $itemsPerPage);

        $rubricCount = getCountRubricByCategoryId($categoryId);

        $text .= displayRubricListBlock($rubricList, $dir[0]);

        $text .= Pager2($pg, $itemsPerPage, ceil($rubricCount / $itemsPerPage), $dir[0] . "/" . $dir[1] . "/" . $dir[2] . "/[page]");

        break;
}


$Page["Content"] = $text;

function displayCatsBlock($baseUrl)
{
    $text = '';
    $categoryList = getWikiCategoryListSorted();
    if (!empty($categoryList)) {
        $text .= "<ul class=\"wikiCat\">";
        foreach ($categoryList as $categoryItem) {
            $text .= "<li><a href=\"/$baseUrl/{$categoryItem['id']}/\">{$categoryItem['name']}</a></li>";
        }
        $text .= "</ul>";
    }
    return "<h2>Определения по категориям</h2>$text";
}

function displayAlphabetBlock($baseUrl)
{
    $alphaRowSize = 11;
    $indexer = 1;
    $text = '';
    $linkText = '';
    foreach (getAlphabetList() as $letter => $code) {
        $linkText .= "<a href=\"/$baseUrl/$code/\">$letter</a>\r\n";

        if ($indexer == $alphaRowSize) {
            $indexer = 1;
            $linkText .= "<div class=\"C30\"></div>";
        }
        ++$indexer;
    }
    $text .= "<h2>Определения по алфавиту</h2><div class=\"wikiLetter\" style=\"text-align:center; margin:30px 0px;\">$linkText</div>";
    return $text;
}

function displayRubricListBlock(array $rubricList, $baseUrl, $displayCat = false)
{
    global $VARS, $C10, $dir;
    $text = "";
    if (!empty($rubricList)) {
        foreach ($rubricList as $rubricItem) {
            $createTimeData = ToRusData($rubricItem["data"]);
            if ($rubricItem["uid"] != 0 && $rubricItem["nick"] != "") {
                $auth = "<a href='http://" . $VARS["mdomain"] . "/users/view/" . $rubricItem["uid"] . "/'>" . $rubricItem["nick"] . "</a>";
            } else {
                $auth = "<a href='http://" . $VARS["mdomain"] . "/add/2/'>Народный корреспондент</a>";
            }

            $pic = '';
            if ($rubricItem["pic"] != "") {
                $pic = "<img src=\"/userfiles/pictavto/{$rubricItem['pic']}\" title=\"{$rubricItem['name']}\" />";
            } else {
                $pic = "<img src=\"/userfiles/pictavto/default.jpg\" title=\"{$rubricItem['name']}\"/>";
            }

            $catText = '';
            if ($displayCat) {
                $catUrl = $dir[0] . "/cat/" . $rubricItem['cat'];
                $catText .= "<a href=\"/$catUrl\" class=\"Info\">{$rubricItem['categoryName']}</a>";
            }

            $text .=
                "<div class=\"NewsLentaList\" id=\"NewsLentaList-{$rubricItem['itemId']}\">
                        <a href=\"/$baseUrl/view/{$rubricItem['itemId']}/\">
                            $pic
                        </a>
                        <h2>
                            <a href=\"/$baseUrl/view/{$rubricItem['itemId']}/\">{$rubricItem['name']}</a>
                        </h2>
                        <p>{$rubricItem['lid']}</p>
                        <div class=\"Info\">
                            <div class=\"Other\">
                                $catText
                            </div>
                        </div>
                    </div>$C10
                ";
        }
    }
    return $text;
}

function displayRubricItem($rubricItem, $baseUrl, $forums)
{
    global $GLOBAL, $VARS, $ROOT, $RealHost, $C, $C5, $C15, $C20, $C10, $link;
    $old = 0;

    $id = (int)$rubricItem['itemId'];

    $cap = $rubricItem["name"];
    $text = '';


    $ban = '';
    if ($rubricItem["promo"] != 1) {
        $ban = "<div class=\"banner\" id=\"Banner-11-1\"></div>";
    }

    $pic = '';
    $path = '';
    // Фотография
    if ($rubricItem["pic"] != "") {
        $pic = "<div class='PicItem' title='$cap'>";
        if (strpos($rubricItem["pic"], "old") != 0) {
            $path = '/' . $rubricItem["pic"];
        } else {
            $path = '/userfiles/picitem/' . $rubricItem["pic"];
        }
        $pic .= "<img src='" . $path . "' title='$cap' alt='$cap' />";
        if ($rubricItem["cens"] != "") {
            $pic .= "<div class='Cens'>" . $rubricItem["cens"] . "</div>";
        }
        if ($rubricItem["picauth"] != "") {
            $pic .= "<div class='PicAuth'>Автор: " . $rubricItem["picauth"] . "</div>";
        }
        $pic .= "</div>" . $C20;
    }

    ### Претекст текст
    if ($rubricItem["lid"] != "") {
        $lid = "<div class='ItemLid'>" . $rubricItem["lid"] . "</div>" . $C15;
    }

    //Контакты
    $catsToShowContactWidget = array(1, 2, 3);
    $catId = (int)$rubricItem['cat'];

    if (in_array($catId, $catsToShowContactWidget)) {
        $contacts = '';
        $contactsData = getWikiContactsById($id);
        if ($contactsData != null) {
            if (isset($contactsData['phone'])) {
                $contacts .=
                    "<tr>
                        <td>Телефон</td><td>{$contactsData['phone']}</td>
                    </tr>";
            }
            if (isset($contactsData['site_url'])) {
                $contacts .=
                    "<tr>
                        <td>Сайт</td><td><a href=\"{$contactsData['site_url']}\">{$contactsData['site_url']}</a></td>
                    </tr>";
            }
            if (isset($contactsData['email'])) {
                $contacts .=
                    "<tr>
                        <td>E-mail</td><td><a href=\"mailto:{$contactsData['email']}\">{$contactsData['email']}</a></td>
                    </tr>";
            }
            if ($contacts != '') {
                $contacts = "<noindex>$contacts</noindex>";
            }
        }
    }

    $tableAdditionalText = '<table class="wikiTable">';
    $paragraphAdditionalText = '';
    switch ($rubricItem['cat']) {
        case 1:

            if ($rubricItem['birth_date'] !== null) {
                $birthDateString = getFormattedWikiDate($rubricItem['birth_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата рождения</td>
                    <td>{$birthDateString}</td>
                </tr>";
            }

            if ($rubricItem['death_date'] !== null) {
                $deathDateString = getFormattedWikiDate($rubricItem['death_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата смерти</td>
                    <td>{$deathDateString}</td>
                </tr>";
            }

            $tableAdditionalText .=
                "<tr>
                    <td>Место рождения</td>
                    <td>{$rubricItem['birth_location']}</td>
                </tr>
                <tr>
                    <td>Место смерти</td>
                    <td>{$rubricItem['death_location']}</td>
                </tr>";
            $paragraphAdditionalText =
                "<h3>Образование</h3>
                <p>{$rubricItem['education']}</p>
                <h3>Карьера</h3>
                <p>{$rubricItem['carrier']}</p>";
            break;
        case 2:
            if ($rubricItem['start_date'] !== null) {
                $startDateString = getFormattedWikiDate($rubricItem['start_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата начала</td>
                    <td>{$startDateString}</td>
                </tr>";
            }

            if ($rubricItem['end_date'] !== null) {
                $endDateString = getFormattedWikiDate($rubricItem['end_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата окончания</td>
                    <td>{$endDateString}</td>
                </tr>";
            }
            break;
        case 3:
            $tableAdditionalText .=
                "<tr>
                    <td>Владелец</td>
                    <td>{$rubricItem['owner']}</td>
                </tr>
                <tr>
                    <td>Автор</td>
                    <td>{$rubricItem['author']}</td>
                </tr>
                <tr>
                    <td>Год начала строительства</td>
                    <td>{$rubricItem['construct_start_year']}</td>
                </tr>
                <tr>
                    <td>Год окончания строительства</td>
                    <td>{$rubricItem['construct_end_year']}</td>
                </tr>";

            if ($rubricItem['release_date'] !== null) {
                $releaseDateString = getFormattedWikiDate($rubricItem['release_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата официального открытия</td>
                    <td>{$releaseDateString}</td>
                </tr>";
            }
            break;
        case 4:
            $tableAdditionalText .= '';
            break;
        case 5:
            $acceptDateString = getFormattedWikiDate($rubricItem['accept_date']);
            $tableAdditionalText .=
                "<tr>
                    <td>Автор</td>
                    <td>{$rubricItem['author']}</td>
                </tr>";

            if ($rubricItem['accept_date'] !== null) {
                $acceptDateString = getFormattedWikiDate($rubricItem['accept_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата официального принятия</td>
                    <td>{$acceptDateString}</td>
                </tr>";
            }
            break;
        case 6:
            $tableAdditionalText .=
                "<tr>
                    <td>Руководители</td>
                    <td>{$rubricItem['managers']}</td>
                </tr>
                ";

            if ($rubricItem['foundation_date'] !== null) {
                $foundationDateString = getFormattedWikiDate($rubricItem['foundation_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата создания</td>
                    <td>{$foundationDateString}</td>
                </tr>";
            }

            if ($rubricItem['decay_date'] !== null) {
                $decayDateString = getFormattedWikiDate($rubricItem['decay_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата распада</td>
                    <td>{$decayDateString}</td>
                </tr>";
            }
            break;
        case 7:
            $tableAdditionalText .=
                "<tr>
                    <td>Автор</td>
                    <td>{$rubricItem['author']}</td>
                </tr>";

            if ($rubricItem['invention_date'] !== null) {
                $inventionDateString = getFormattedWikiDate($rubricItem['invention_date']);
                $tableAdditionalText .=
                    "<tr>
                    <td>Дата изобретения или открытия</td>
                    <td>{$inventionDateString}</td>
                </tr>";
            }
            break;
    }

    if ($contacts != '') {
        $tableAdditionalText .= $contacts;
    }


    $tableAdditionalText .= '</table>';

    // Основной текст
    $mainText = CutEmptyTags($rubricItem["text"]);

    // Фото-отчет
    $photoReportList = getPhotoReportList($id, $baseUrl);
    $report = '';
    if (!empty($photoReportList)) {
        $report .=
            "<table class=\"ItemOrder\">";
        foreach ($photoReportList as $photoReportItem) {
            if ($photoReportItem["sets"] == 0) {
                $report .=
                    "<tr>
                            <td width=\"1%\" valign=\"top\" class=\"type{$photoReportItem['sets']}\">
                                <a href=\"/userfiles/picoriginal/{$photoReportItem['pic']}\" title=\"{$photoReportItem['name']}\" rel=\"prettyPhoto[gallery]\">
                                    <img src=\"/userfiles/picpreview/{$photoReportItem['pic']}\" title=\"{$photoReportItem['name']}\" alt=\"{$photoReportItem['name']}\">
                                </a>
                            </td>
                            <td width=\"99%\" valign=\"top\" class=\"type{$photoReportItem['sets']}\">
                                <h4>{$photoReportItem['name']}</h4>
                                {$photoReportItem['text']}
                            </td>
                        </tr>";
            } else {
                $report .=
                    "<tr>
                            <td width=\"100%\" valign=\"top\" colspan=\"2\" class=\"type{$photoReportItem['sets']}\">
                                <h4>{$photoReportItem['name']}</h4>
                                <a href=\"/userfiles/picoriginal/{$photoReportItem['pic']}\" title=\"{$photoReportItem['name']}\" rel='prettyPhoto[gallery]'>
                                    <img src=\"/userfiles/picoriginal/{$photoReportItem['pic']}\" title=\"{$photoReportItem['name']}\" alt=\"{$photoReportItem['name']}\">
                                </a>{$C5}{$photoReportItem['text']}
                            </td>
                        </tr>";
            }
        }
        $report .=
            "</table>" . $C10;
    }

    //Фото-альбом
    $photoAlbumList = getPhotoAlbumList($id, $baseUrl);

    $album = '';
    if (!empty($photoAlbumList)) {
        $album = "<h3>Фотоальбом:</h3>$C10<div class='ItemAlbum'>";
        foreach ($photoAlbumList as $photoAlbumItem) {
            $album .= "<a href='/userfiles/picoriginal/" .
                $photoAlbumItem["pic"] . "' title='" .
                $photoAlbumItem["name"] .
                "' rel='prettyPhoto[gallery]'><img src='/userfiles/pictavto/" .
                $photoAlbumItem["pic"] . "' title='" . $photoAlbumItem["name"] .
                "' alt='" . $photoAlbumItem["name"] . "'></a>";
        }
        $album .= "</div>" . $C;
    }

    //Видео
    $videoItem = getVideoItem($id, $baseUrl);
    $video = '';
    if (isset($videoItem)) {
        if ($videoItem["text"] != "") {
            if ($videoItem["name"] != "") {
                $video .= "<h2>" . $videoItem["name"] . "</h2>";
            }
            $vid = GetNormalVideo($videoItem["text"]);
            $video .= $vid . $C10;
        }
    }

    //Тэги и ошибки
    $tagIdsString = trim($rubricItem["tags"], ",");
    $tags = '';
    $tags2 = '';
    if ($tagIdsString != "") {
        $tagList = getTagListByIdsString($tagIdsString);
        foreach ($tagList as $tagItem) {
            $tags .= "<a href=\"/tags/{$tagItem['id']}\">{$tagItem['name']}</a>, ";
        }
        $tags2 = trim($tags, ", ");
        $tags = "Тэги:" . $tags2;
    }
    $tags .=
        "<div style=\"line-height:13px; margin:5px 0;\">
            Если Вы нашли ошибку в тексте,<br>
            <u>выделите слово или предложение</u> с ошибкой и нажмите Ctrl+Enter
        </div>";

    // Карта событий
    $eventMap = getEventMap($id, $baseUrl);
    $event = '';
    if (isset($eventMap)) {
        if ($eventMap['maps']) {
            $event = '<script type="text/javascript" src="	http://maps.api.2gis.ru/1.0"></script><div id="Map" style="width:710px; height:300px;"></div>';
            $event .= '<script type="text/javascript">initMap([' . $eventMap['id'] . ', "' . htmlspecialchars($eventMap['name']) . '", "' . $eventMap['maps'] . '", "' . ($eventMap['icon'] ? '/userfiles/mapicon/' . $eventMap['icon'] : '') . '"]);</script>';
            $event .= "<h3>Как проехать</h3><p>{$eventMap['name']}</p>";
        } else if ($eventMap['data']) {
            $event_month_days = date('t', $eventMap['data']);
            $event_day = date('j', $eventMap['data']);
            $event_month = date('n', $eventMap['data']);
            $event_first_day = getdate(mktime(0, 0, 0, date('m', $eventMap['data']), 1, date('Y', $eventMap['data'])));
            $event_last_day = getdate(mktime(0, 0, 0, date('m', $eventMap['data']), $event_month_days, date('Y', $eventMap['data'])));

            $calendar = '<div class="Calendar"><table>';
            $calendar .= '<tr><th colspan="7">' . $GLOBAL["mothi"][date('n', $eventMap['data'])] . ' ' . date('Y', $eventMap['data']) . '</th></tr>';
            $calendar .= '<tr><th>ПН</th><th>ВТ</th><th>СР</th><th>ЧТ</th><th>ПТ</th><th>СБ</th><th>ВС</th></tr><tr>';
            for ($i = 2 - $event_first_day['wday'], $j = 1; $i <= $event_month_days + (7 - ($event_last_day['wday'] == 0 ? 7 : $event_last_day['wday'])); $i++, $j++) {
                $calendar .= '<td><span' . ($i == $event_day ? ' class="active" title="Начало"' : '') . '>' . ($i > 0 && $i <= $event_month_days ? $i : '') . '</span></td>';
                if ($j % 7 == 0) $calendar .= '</tr><tr>';
            }
            $calendar .= '</tr></table></div>';
            $event = $calendar;
        }
    }


    // Лайки
    $likes = "<div class='Likes'>" . Likes(Hsc($cap), "", "http://" . $RealHost . $path, Hsc(strip_tags($lid))) . $C . "</div>" . $C10;

    // Вывод автора и тэгов
    $mixBlock = "<div><div class='ItemTags'>" . $tags . "</div>" . $C . "</div>" . $C5;
    if ($rubricItem["pay"] != "") {
        $mixBlock .= $C10 . "<div class='WhiteBlock PayBlock'>" . $rubricItem["pay"] . "</div>";
    }

    // Платные ссылки
    if ($rubricItem["adv"] != "") {
        $mixBlock .= $C20 . "<div class=\"CBG\"></div>$C5<div class=\"AdvBlock\">{$rubricItem['adv']}</div>$C";
    }

    //Заключительный текст
    $endText = '';
    if ($rubricItem["endtext"] != "") {
        $endText = $C5 . "<div class=\"hiteBlock EndText\">{$rubricItem['endtext']}</div>" . $C;
    }


    $text =
        "$pic$ban
        <div class=\"ArticleContent\">
            $lid
            <div class=\"ItemText\">$tableAdditionalText$paragraphAdditionalText$mainText</div>
            $report$video$album$endText$event$C10
            <!--ADS-->
            $likes$mixBlock
         </div>";


    return $text;
}

function getAlphabetList()
{
    $letterList = array("А", "Б", "В", "Г", "Д", "Е", "Ж", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Э", "Ю", "Я");
    $abc = null;
    foreach ($letterList as $item) {
        $abc[$item] = urlencode($item);
    }
    return $abc;
}