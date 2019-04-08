function ItemDelete(id, pic) { $("#Act"+id).html(loader); caption="Подтвердите удаление"; text='Удалить запись?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\", \""+pic+"\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank(); ReturnI("+id+", \""+pic+"\")'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function MultiDelete(table) { 
ids = []; 
$('.selectItem:checked').each(function(){ 
ids.push($(this).attr('id')); }); 
caption="Подтвердите удаление"; 
text='Удалить записи?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate(\""+ids.join()+"\", \"DEL\",\""+table+"\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function ActionAndUpdate(id, act, pic) { 
CloseBlank();
 JsHttpRequest.query('modules/tests/result-JSReq.php',{'id':id,'act':act,'pic':pic},
 
 function(result,errors){ if(result){ /**/ 
 if (act=="DEL"){ if(!$('.loader').size()) $('.MultiDel').hide(); 
 if(/,/.test(id)){ for(var i = 0; i<ids.length; i++) $("#Line"+ids[i]).remove(); } else { $("#Line"+id).remove(); } } /**/ }},true); } 
 function ReturnI(id, pic) { 
 $("#Act"+id).html('<a href="javascript:void(0);" onclick="ItemDelete(\''+id+'\',\''+pic+'\')"><img src="/admin/images/icons/exit.png" valign="middle" title="" style="margin:-2px 3px 0 0;"></a>');

 }
