$(".JsVerify").live("focus", function(){ $(this).toggleClass("ErrorInput",false);}); function JsVerify(){ error=0; $(".JsVerify").each(function (i) { $(this).toggleClass("ErrorInput", false); var val=$(this).val(); for(i=0; i<NotAvaliable.length; i++){ if (NotAvaliable[i]==val){ error=1; $(this).toggleClass("ErrorInput",true); }} if (val=="" || val=="NULL"){ error=1; $(this).toggleClass("ErrorInput", true);} if ((/^[a-z0-9_]+$/g).test(val)==false) { error=1; $(this).toggleClass("ErrorInput", true); } }); if (error!=0) { return false; } else { return true; }}

function AddNewMenu(pid, tab, cap) { var text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" name="addbutton" class="SaveButton" value="Добавить"></div><input type="hidden" name="pid" value="'+pid+'"/>';
text=text+'<tr class="TRLine0"><td>Название</td><td class="NormalInput"><input name="name" type="text" class="JsVerify"></td></tr>';
text=text+'<tr class="TRLine1"><td>Текст</td><td class="NormalInput"><textarea name="text" style="outline:none;" class="texteditors"></textarea></td></tr>';
text=text+'<tr class="TRLine0"><td>Изображение</td><td class="NormalInput">';
text=text+'<div title="Нажмите для выбора файла" id="Podstava3" class="Podstava4"><input type="file" id="photo" name="photo" accept="image/jpeg,image/gif,image/x-png" onChange="$(\'#FileName\').html($(this).val());" /></div><div id="FileName" style="float:left;"></div>';
text=text+'</td><tr>';
text=text+'<tr class="TRLine1"><td>Включено</td><td class="NormalInput"><input name="stat" id="Rb0" type="checkbox" checked></td></tr>';
text="<form action='' enctype='multipart/form-data' method='post'><div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div></form>"; ViewBlank(cap, text); $('#Rb0').tzCheckbox({labels:['да ','нет']}); }

function ItemEdit(id, name, content, pic, tab, cap) { text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" name="savebutton" class="SaveButton" value="Сохранить"></div><input type="hidden" name="id" value="'+id+'"/><input type="hidden" name="pic" value="'+pic+'"/>';
text=text+'<tr class="TRLine0"><td>Название</td><td class="NormalInput"><input name="name" type="text" value="'+name+'" class="JsVerify"></td></tr>';
text=text+'<tr class="TRLine1"><td>Текст</td><td class="NormalInput"><textarea name="text" style="outline:none;" class="texteditors">'+content+'</textarea></td></tr>';
text=text+'<tr class="TRLine0"><td>Изображение</td><td class="NormalInput">';
text=text+'<div title="Нажмите для выбора файла" id="Podstava3" class="Podstava4"><input type="file" id="photo" name="photo" accept="image/jpeg,image/gif,image/x-png" onChange="$(\'#FileName\').html($(this).val());" /></div><div id="FileName" style="float:left;">'+pic+'</div>';
text=text+'</td><tr>';
text="<form action='' enctype='multipart/form-data' method='post'><div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div></form>"; ViewBlank(cap, text); }

function ItemDelete(id, tab) { $("#Act"+id).html(loader); caption="Подтвердите удаление"; text='Удалить запись?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\", \""+tab+"\", \"\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank(); ReturnI("+id+", \""+tab+"\")'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }

function ItemUp(id, tab, pid) { ActionAndUpdate(id, "UP", tab, pid); }
function ItemDown(id, tab, pid) { ActionAndUpdate(id, "DOWN", tab, pid); }

function ActionAndUpdate(id, act, tab, pid) {CloseBlank(); JsHttpRequest.query('modules/companies/cats-JSReq.php',{'id':id,'act':act,'tab':tab,'pid':pid},function(result,errors){ if(result){ $("#Tgg").html(result["content"]); $("#Msg2").html("Данные успешно сохранены"); $("#Msg2").removeClass(); $("#Msg2").addClass("SuccessDiv"); $('#Tgg input').tzCheckbox({labels:['да ','нет']}); }},true); }
function ReturnI(id, tab) { $("#Act"+id).html('<a href="javascript:void(0);" onclick="ItemDelete(\''+id+'\',\''+tab+'\')"><img src="/admin/images/icons/exit.png" valign="middle" title="" style="margin:-2px 3px 0 0;"></a>'); }





