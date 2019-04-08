function AddField(name, container){
	container.append('<div><input name="'+name+'[]" type="text" value=""><a title="Удалить" onclick="RemoveField($(this))" href="javascript:void(0);"><img style="margin:2px 0 0 3px; width:14px;" valign="middle" src="/admin/images/icons/exit.png"></a><div class="C5"/></div>');
}

function RemoveField(o){
	o.prev('input').val('').parent('div').hide();
}