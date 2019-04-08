<?	
	$settings = explode('|', $sets['sets']);

	if ($settings[0]==1) { $schk0="checked"; }
	
	### Основные данные
	$Settings='<table><tr class="TRLine0"><td style="width:25%;"></td><td style="width:75%;"></td></tr>
	<tr class="TRLine1"><td class="VarText">Экспорт</td><td class="NormalInput"><input name="settings[0]" type="hidden" value="0"><input name="settings[0]" type="checkbox" value="1" '.$schk0.'> разрешить другим сайтам на системе ProCMS брать материалы из этого раздела</td></tr>
	<tr class="TRLine1"><td class="VarText">Разрешения</td><td class="LongInput"><textarea name="settings[1]" placeholder="Укажите адреса сайтов через запятую (например: yandex.ru, mail.ru)">'.$settings[1].'</textarea></td></tr>
	</table>';
?>