$(document).ready(function() {
	var uploaders = {};
	$('.uploader').each(function(index){
		var uploader = this;
		uploaders[index] = new qq.FineUploader({
			element: uploader,
			multiple: false,
			request: {
				endpoint: '/modules/standart/multiupload/server/handler3.php',
				paramsInBody: false,
			},
			callbacks: {
		    	onComplete: function(id, fileName, responseJSON) {
		    		if(responseJSON.success) {
		    			$('.uploaderFiles', $(uploader).parents('td')).append('<span class="imgCon"><img src="/userfiles/temp/'+responseJSON.uploadName+'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="pic" value="'+responseJSON.uploadName+'" /></span>');
		    			$(uploader).parents('.uploaderCon').hide();
		    		}
		    	}
		    },
		    debug: true
	    });
	});
});

function imgRemove(o){
	o.parents('td').find('.uploaderCon').show();
	o.parents('.imgCon').remove();
}


function ItemDelete(id, link, act) { ActionAndUpdate(id, act, link); }
function ActionAndUpdate(id, act, link) { JsHttpRequest.query('/modules/photoalbum/photoalbum-JSReq.php',{'id':id,'act':act,'link':link},function(result,errors){ if(result){ if (act=="DELPHOTO" || act=="DELALBUM"){ $("#Act"+id).parents('.Item').remove(); } }},true); }

function VotingLikes(act, fid, pid, link) {
	JsHttpRequest.query('/modules/photoalbum/photoalbumvote-JSReq.php',{'act':act,'fid':fid,'pid':pid,'link':link}, function(result,errors){ if(result){ console.log(result);
	if (result["act"]=="ok") { $("#like"+fid).html(result["like"]); $("#disl"+fid).html(result["disl"]); $("#ttl"+fid).html(result["ttl"]); }else{ alert("Вы уже голосовали сегодня, спасибо!"); }
}},true); }



