$(".JsVerify").live("focus", function(){ $(this).toggleClass("ErrorInput", false); });
$(document).ready(function() { if ($("#uploader").html()=="") { var uploader = new qq.FineUploader({ element: document.getElementById('uploader'), request: { endpoint: '/modules/standart/multiupload/server/handler3.php', paramsInBody: false, },
callbacks: { onComplete: function(id, fileName, responseJSON) {	if(responseJSON.success) { $('#uploader').append('<input type="hidden" name="attachment[]" value="'+responseJSON.uploadName+'" />'); }}}, debug: true });  } });
function JsVerify() { var error=0; $(".JsVerify").each(function (i) { $(this).toggleClass("ErrorInput", false); var val=$(this).val(); if (val=="" || val=="NULL") { error=1; $(this).toggleClass("ErrorInput", true); }}); if (error!=0) { return false; } else { return true; }}



 