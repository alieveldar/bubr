var GET=parseGetParams(); var pid=GET["id"];
$(document).ready(function() {
	var cat=GET['cat'].split('_');
	var uploader=new qq.FineUploader({
		element: document.getElementById('uploader'),
		request: {
			endpoint: '/modules/standart/multiupload/server/handler2.php',
			paramsInBody: false,
		},
		callbacks: {
	    	onComplete: function(id, fileName, responseJSON) {
	    		if(responseJSON.success) $('#uploader').append('<input type="hidden" name="attachment[]" value="'+responseJSON.uploadName+'" />');
	    	}
	    },
	    debug: true
    });
});
