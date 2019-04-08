$(document).ready(function() {
	var uploaders = {};
	$('.uploader').each(function(index){
		var uploader = this;
		uploaders[index] = new qq.FineUploader({
			element: uploader,
			multiple: index,
			request: {
				endpoint: '/modules/standart/multiupload/server/handler3.php',
				paramsInBody: false,
			},
			callbacks: {
		    	onComplete: function(id, fileName, responseJSON) {
		    		if(responseJSON.success) {
		    			$('.uploaderFiles', $(uploader).parents('td')).append('<span class="imgCon"><img src="/userfiles/temp/'+responseJSON.uploadName+'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="'+(index ? 'attachment[]' : 'pic') + '" value="'+responseJSON.uploadName+'" /></span>');
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