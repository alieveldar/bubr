function AddField(){
	$('#Answers').append('<div>');
	$('#Answers').append('<input name="votes[]" type="text" value="" placeholder="Вариант ответа" style="float:left;width:400px;">');
	$('#Answers').append('<input name="points[]" type="text" value="" style="float:right;margin-right:15px;width:120px;" placeholder="Баллы за ответ">');
	$('#Answers').append('<a title="Удалить" onclick="RemoveField($(this))" href="javascript:void(0);"><img style="margin:2px 0 0 3px; width:14px;" valign="middle" src="/admin/images/icons/exit.png"></a><div class="C5"/>');
	$('#Answers').append('</div>');
}

function RemoveField(o){
	o.prev('input').val('').parent('div').hide();
}