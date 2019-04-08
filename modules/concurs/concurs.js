function voteForm(itemId, nodeId, link, social_title, social_description, social_url, social_image) { 
    var votingForm ='<div id="voteForm">Оставьте свой голос через аккаунт одной из социальных сетей';
	votingForm+='<div class="socials">';
	votingForm+='<img src="/template/standart/voting/vk.gif" alt="" onclick="socials(\'http://vk.com/share.php?title=TITLE&amp;description=DESCRIPTION&amp;url=URL&amp;image=IMAGE\');">';
	votingForm+='<img src="/template/standart/voting/fb.gif" alt="" onclick="socials(\'http://www.facebook.com/sharer.php?s=100&amp;p[url]=URL&amp;p[title]=TITLE&amp;p[summary]=DESCRIPTION&amp;p[images][0]=IMAGE\');">';
	votingForm+='<img src="/template/standart/voting/tt.gif" alt="" onclick="socials(\'https://twitter.com/intent/tweet?text=TITLE DESCRIPTION&amp;url=URL\');">';
	votingForm+='<img src="/template/standart/voting/ok.gif" alt="" onclick="socials(\'http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st.s=1&amp;st.comments=TITLE DESCRIPTION&amp;st._surl=URL\');">';
	votingForm+='<img src="/template/standart/voting/ml.gif" alt="" onclick="socials(\'http://connect.mail.ru/share?url=URL&amp;title=TITLE&amp;description=DESCRIPTION&amp;imageurl=IMAGE\');">';
	votingForm+='</div>';
	votingForm+='<input id="itemId" type="hidden" value="'+itemId+'"><input id="nodeId" type="hidden" value="'+nodeId+'"><input id="link" type="hidden" value="'+link+'"><input id="social-title" type="hidden" value="'+social_title+'"><input id="social-description" type="hidden" value="'+social_description+'"><input id="social-url" type="hidden" value="'+social_url+'"><input id="social-image" type="hidden" value="'+social_image+'">';
	votingForm+='</div>';
	ViewBlank('Голосование', votingForm);
} 


function socials(url) {
	var itemId = encodeURIComponent($("#itemId").val());
	var nodeId = encodeURIComponent($("#nodeId").val());
	var link = encodeURIComponent($("#link").val());	
    var share_title = encodeURIComponent($("#social-title").val());
    var share_description = encodeURIComponent($("#social-description").val());
    var share_url = encodeURIComponent($("#social-url").val());
    var share_image = encodeURIComponent($("#social-image").val());
    var top = parseInt($(window).height() / 2 - 225);
    var left = parseInt($(window).width() / 2 - 225);
    // Обработка адреса;
    var social_url = url.replace("TITLE", share_title).replace("DESCRIPTION", share_description).replace("URL", share_url).replace("IMAGE", share_image);
    // Вывод окна;
	window.open(social_url, "displayWindow", "width=450, height=450, top=" + top + ", left=" + left + " location=0, status=0, toolbar=0, menubar=0, scrollbars=0, resizable=0");
    // Закрыть всплывающее окно;
      
	voteSave(itemId,nodeId, link);
	return false;
}


function voteSave(itemId, nodeId, link){
	JsHttpRequest.query('/modules/concurs/VoteSave-JSReq.php',{'vid':itemId, 'pid':nodeId, 'link':link},function(result,errors){ 
		if(result){ /*s*/ $("#voteForm").html(result["Text"]); if (result["Code"]==1) { 
			$('.votingButton').each(function(index){
				$('.votes', $(this)).text(result["Votes"][index]);
				$('a', $(this)).remove();
			}); 
		} /*e*/ }
	console.log(result["Log"]);
	},true);
}