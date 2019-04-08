<?	
	$settings = explode('|', $sets['sets']);

	if ($settings[2]==1) { $schk="checked"; }
	if ($settings[3]==1) { $schk1="checked"; }

	### Основные данные
	$Settings='<table><tr class="TRLine0"><td style="width:25%;"></td><td style="width:75%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Размеры карты</td><td class="SmallInput">ширина: <input name="settings[0]" type="text" value="'.$settings[0].'">     высота: <input name="settings[1]" type="text" value="'.$settings[1].'"></td></tr>	
	<tr class="TRLine1"><td class="VarText">Пользовательские альбомы</td><td class="NormalInput"><input name="settings[2]" type="hidden" value="0"><input name="settings[2]" type="checkbox" value="1" '.$schk.'></td></tr>
	<tr class="TRLine0"><td class="VarText">Треб. одоб. альбома</td><td class="NormalInput"><input name="settings[3]" type="hidden" value="0"><input name="settings[3]" type="checkbox" value="1" '.$schk1.'></td></tr>
	<tr class="TRLine1"><td class="VarText">E-mail для уведомлений</td><td class="LongInput"><input name="settings[4]" id="name" type="text" value="'.$settings[4].'"></td><tr>
	</table>';
?>