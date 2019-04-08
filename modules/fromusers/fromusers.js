$(document).ready(function() {
	var uploader = new qq.FineUploader({
		element: document.getElementById('uploader'),
		request: {
			endpoint: '/modules/standart/multiupload/server/handler3.php',
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
