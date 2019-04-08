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


$(".JsVerify").live("focus", function(){ $(this).toggleClass("ErrorInput", false); });  
$(".JsVerify2").live("focus", function(){ $(this).toggleClass("ErrorInput", false); });
 
function JsVerify() { 
	var error=0; 
	$(".JsVerify2").each(function (i) { 
		$(this).toggleClass("ErrorInput", false); 
		var val=$(this).val(); 
		if (val=="" || val=="NULL") { error=1; $(this).toggleClass("ErrorInput", true); } 
	}); 
	$(".JsVerify").each(function (i) { 
		$(this).toggleClass("ErrorInput", false); 
		var val=$(this).val(); 
		if (val!="" && val!="NULL") { 
			for(var i=0; i<NotAvaliable.length; i++) { if (NotAvaliable[i]==val) { error=1; $(this).toggleClass("ErrorInput", true); }}
		} 
	}); 
	if (error!=0) { return false; } else { return true; }
} 
