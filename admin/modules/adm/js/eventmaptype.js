function AddNewTask(nid, pid) {
var cap="Добавить тип события"; var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Добавить" onclick="AddNewTaskAct();"></div>'; 
text='<tr class="TRLine0"><td>Название типа<star>*</star></td><td class="NormalInput"><input id="Rl0" type="text" class="JsVerify" placeholder="ДТП"></td></tr>';
text+='<tr class="TRLine1"><td style="vertical-align:top; padding-top:10px;">Иконка</td><td class="NormalInput"><div class="uploaderCon"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';	
text+='</div></td></tr>';	
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>"; ViewBlank(cap, text); createUploader();}

function AddNewTaskAct() {
n=$('#Rl0').val(); p=$('input[name=pic]').val(); error=0; if (n=="" || n=="NULL"){ error=1; $("#Rl0").toggleClass("ErrorInput", true);} 
if (error==0) { $("#JSLoader").html(loader2); JsHttpRequest.query('modules/adm/eventmaptype-add-JSReq.php',{'n':n, 'p':p} , function(result,errors){if(result){ /*s*/ ActionAndUpdate(0,0); /*e*/ }} ,true); }}
 
function ItemEdit(id, n, p) { 
var cap="Редактировать тип"; var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Сохранить" onclick="SaveTaskItem(\''+id+'\');"></div>';
text='<tr class="TRLine0"><td>Название типа<star>*</star></td><td class="NormalInput"><input id="Rl0" type="text" class="JsVerify" placeholder="ДТП" value="'+n+'"></td></tr>';
text+='<tr class="TRLine0"><td style="vertical-align:top; padding-top:10px;">Иконка</td><td class="NormalInput"><div class="uploaderCon" style="'+(p ? 'display:none;' : '')+'"><div class="uploader"></div><div class="Info">Вы можете загрузить фотографию в формате jpg, gif и png</div></div><div class="uploaderFiles">';
if(p) text+='<span class="imgCon"><img src="/userfiles/mapicon/'+p+'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="icon" value="'+p+'" /></span>';
text+='</div></td></tr>';	
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>"; ViewBlank(cap, text);  createUploader();}
 
function SaveTaskItem(id) {
n=$('#Rl0').val(); p=$('input[name=pic]').val(); error=0; if (n=="" || n=="NULL"){ error=1; $("#Rl0").toggleClass("ErrorInput", true);} 
$("#JSLoader").html(loader2); JsHttpRequest.query('modules/adm/eventmaptype-edit-JSReq.php',{'id':id, 'n':n, 'p':p}, function(result,errors){if(result){ /*s*/ ActionAndUpdate(0,0); /*e*/ }} ,true); }

function ItemDelete(id) { caption="Подтвердите удаление"; text='Удалить тип события?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function ItemUp(id) { ActionAndUpdate(id, 'UP'); } function ItemDown(id) { ActionAndUpdate(id, 'DOWN'); }

function ActionAndUpdate(id, act) { CloseBlank(); $("#Msg2").html("Идет сохранение данных..."); $("#Msg2").removeClass(); $("#Msg2").addClass("SaveDiv"); JsHttpRequest.query('modules/adm/eventmaptype-update-JSReq.php',{'id':id, 'act':act}
,function(result,errors){ if(result){  /*s*/ $("#Tgg").html(result["content"]); $("#Msg2").html("Данные успешно сохранены"); $("#Msg2").removeClass(); $("#Msg2").addClass("SuccessDiv"); $('#Tgg input').tzCheckbox({labels:['да ','нет']}
); /*e*/ }},true); }


function createUploader(){
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
		    			$('.uploaderFiles', $(uploader).parents('td')).append('<span class="imgCon"><img src="/userfiles/temp/'+responseJSON.uploadName+'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="'+(index ? 'icon' : 'pic') + '" value="'+responseJSON.uploadName+'" /></span>');
		    			$(uploader).parents('.uploaderCon').hide();
		    		}
		    	}
		    },
		    debug: true
	    });
	});
}

function imgRemove(o){
	o.parents('td').find('.uploaderCon').show();
	o.parents('.imgCon').remove();
}