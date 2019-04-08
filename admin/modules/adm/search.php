<?
### КРОССЛИНКОВКА СТРАНИЦ
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

	$AdminText.="
	<h2>Поиск по сайту</h2>
	Для уменьшения нагрузки на сервер и для более релевантного поиска по сайту, ProCMS использует <a href='http://site.yandex.ru/searches/' target='_blank'><b>Яндекс поиск</b></a>.<br>
	Кроме того, в настоящий момент система поиска Яндекс позволяет значительно увеличить скорость индексации страниц сайта, что, конечно, очень удобно.<br><br>
	
	<div class='RoundText' id='Tgg'><h2>Для функционирования поиска необходимо:</h2><ul>
	<li><a href='http://site.yandex.ru/searches/new/' target='_blank'>Добавить сайт</a> в систему Яндекс Поиск</li>
	<li>Настроить параметры сайта, а так же страницу выдачи результатов</li>
	<li>Страница выдачи результатов должна иметь вид: http://<имя_вашего_сайта>/search/</li>
	<li>Переменную с формой поиска - ".'$Page["SiteSearch"]'.", можно изменить в файле /modules/StaticBlocks.php</li>
	<li>Оформление формы поиска и результатов можно изменить в файле /template/standart/standart.css</li>
	<li>Оформление формы поиска и результатов можно не менять, будет использоваться тема по-умолчанию</li>
	</ul></div>
	
	<h2>Примеры поисковых настроек сайта:</h2>
	<img src='/admin/images/search/1.gif' /><div class='C15'></div><hr><div class='C15'></div>
	<img src='/admin/images/search/2.gif' /><div class='C15'></div><hr><div class='C15'></div>
	<img src='/admin/images/search/3.gif' /><div class='C15'></div><hr><div class='C15'></div>
	<img src='/admin/images/search/4.gif' />";
	
	// ПРАВАЯ КОЛОНКА
	$AdminRight="";
	
}

//=============================================
$_SESSION["Msg"]="";
?>