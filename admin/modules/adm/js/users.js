var ids;
function ItemInfo(id, ip, role, login, vkontakte, mailru, twitter, facebook, odnoklas, google, yandex, mail, spectitle, signature, created, lasttime, avatar) { 
	caption=login;
	text="";
	if(spectitle) text += "<div class='spectitle'><i>"+spectitle+"</i></div>";
	if(avatar) text += "<div class='avatar'><img src='"+avatar+"' /></div>";
	text += "<div class='id'><b>ID пользователя:</b> "+id+"</div>";
	if(ip) text += "<div class='ip'><b>IP пользователя:</b> "+ip+"</div>";
	text += "<div class='role'><b>Роль:</b> "+role+"</div>";
	if(mail) text += "<div class='mail'><b>Почта:</b> "+mail+"</div>";
	if(signature) text += "<div class='signature'><b>Подпись:</b> "+signature+"</div>";
	text += "<div class='created'><b>Дата регистрации:</b> "+created+"</div>";
	text += "<div class='lasttime'><b>Последний вход:</b> "+lasttime+"</div>";
	text += "<div class='social'>";
    if(vkontakte || mailru || twitter || facebook || odnoklas || google || yandex){
    	text += "<b>Социальные сети</b><br />";
    	if(vkontakte) text += "<a href='http://vk.com/id"+vkontakte+"'><img src='/admin/images/icons/social_vkontakte.gif' width='45' height='45' /></a> ";
    	if(mailru) text += "<a href='http://my.mail.ru/mail/"+mailru+"'><img src='/admin/images/icons/social_mailru.gif' width='45' height='45' /></a> ";
    	if(twitter) text += "<a href='https://twitter.com/"+twitter+"'><img src='/admin/images/icons/social_twitter.gif' width='45' height='45' /></a> ";
    	if(facebook) text += "<a href='http://www.facebook.com/profile.php?id="+facebook+"'><img src='/admin/images/icons/social_facebook.gif' width='45' height='45' /></a> ";
    	if(odnoklas) text += "<a href='http://www.odnoklassniki.ru/profile/"+odnoklas+"'><img src='/admin/images/icons/social_odnoklassniki.gif' width='45' height='45' /></a> ";
    	if(google) text += "<a href='https://plus.google.com/u/0/"+google+"'><img src='/admin/images/icons/social_google.png' width='45' height='45' /></a> ";
    	if(yandex) text += "<a href='"+yandex+"'><img src='/admin/images/icons/social_yandex.png' width='45' height='45' /></a> ";
    	text += "</div>";
    }
	ViewBlank(caption,text); 
}

function ItemDelete(id, pg) { caption="Подтвердите удаление"; text='Удалить пользователя?<br>Все комментарии от этого пользователя останутся и станут от анонима.<br>Если необходимо удалить и комментарии, пройдите по <a href="?cat=adm_clearcomms&id='+id+'"><b>ссылке</b></a><br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\", "+pg+");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function MultiDelete() { ids = []; $('.selectItem:checked').each(function(){ ids.push($(this).attr('id')); }); caption="Подтвердите удаление"; text='Удалить записи?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate(\""+ids.join()+"\", \"DEL\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); } 

function ActionAndUpdate(id, act, pg) { CloseBlank(); $("#Msg2").html("Идет сохранение данных..."); $("#Msg2").removeClass(); $("#Msg2").addClass("SaveDiv"); JsHttpRequest.query('modules/adm/users-update-JSReq.php',{'id':id, 'act':act, 'pg':pg},
function(result,errors){ $("#Msg2").html("Данные успешно сохранены"); $("#Msg2").removeClass(); $("#Msg2").addClass("SuccessDiv"); if(result){  /**/ if (act=="DEL"){ if(!$('.loader').size()) $('.MultiDel').hide(); if(/,/.test(id)){ for(var i = 0; i<ids.length; i++) $("#Line"+ids[i]).remove(); } else { $("#Line"+id).remove(); } } /**/ }},true); }