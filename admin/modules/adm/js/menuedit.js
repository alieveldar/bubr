$(".JsVerify").live("focus", function(){ $(this).toggleClass("ErrorInput",false);}); function JsVerify(){ error=0; $(".JsVerify").each(function (i) { $(this).toggleClass("ErrorInput", false); var val=$(this).val(); for(i=0; i<NotAvaliable.length; i++){ if (NotAvaliable[i]==val){ error=1; $(this).toggleClass("ErrorInput",true); }} if (val=="" || val=="NULL"){ error=1; $(this).toggleClass("ErrorInput", true);} if ((/^[a-z0-9_]+$/g).test(val)==false) { error=1; $(this).toggleClass("ErrorInput", true); } }); if (error!=0) { return false; } else { return true; }}

function AddNewMenu(nid, pid) { var cap="Добавить ссылку в меню"; var text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Добавить" onclick="AddNewMenuAct(\''+nid+'\', \''+pid+'\');"></div>';
text=text+'<tr class="TRLine0"><td>Текст ссылки<star>*</star></td><td class="NormalInput"><input id="Rn0" type="text" class="JsVerify"></td></tr>';
text=text+'<tr class="TRLine1"><td>Адрес ссылки<star>*</star></td><td class="NormalInput"><input id="Rl0" type="text" class="JsVerify" value="/index/"></td></tr>';
text=text+'<tr class="TRLine0"><td>Классы CSS</td><td class="NormalInput"><input id="Rc0" type="text"></td></tr>';
text=text+'<tr class="TRLine0"><td>Включено</td><td class="NormalInput"><input id="Rb0" type="checkbox" checked></td></tr>';
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>"; ViewBlank(cap, text); $('#Rb0').tzCheckbox({labels:['да ','нет']}); }

function ItemEdit(id, nid, pid, name, link, css) { var cap="Редактировать ссылку меню"; text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Сохранить" onclick="SaveMenuItem(\''+id+'\', \''+nid+'\', \''+pid+'\');"></div>';
text=text+"<tr class='TRLine0'><td>Текст ссылки<star>*</star></td><td class='NormalInput'><input id='Rned' type='text' class='JsVerify' value='"+name+"'></td></tr>";
text=text+"<tr class='TRLine1'><td>Адрес ссылки<star>*</star></td><td class='NormalInput'><input id='Rled' type='text' class='JsVerify' value='"+link+"'></td></tr>";
text=text+"<tr class='TRLine0'><td>Классы CSS</td><td class='NormalInput'><input id='Rced' type='text' value='"+css+"'></td></tr>";
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>"; ViewBlank(cap, text); }

function SaveMenuItem(id, nid, pid) { name=$('#Rned').val(); link=$('#Rled').val(); css=$('#Rced').val(); error=0; if (name=="" || name=="NULL"){ error=1; $("#Rned").toggleClass("ErrorInput", true);} if (link=="" || link=="NULL"){ error=1; $("#Rled").toggleClass("ErrorInput", true);} $("#JSLoader").html(loader2); JsHttpRequest.query('modules/adm/menuedit-edit-JSReq.php',{'nid':nid, 'pid':pid, 'id':id, 'name':name, 'link':link, 'css':css},function(result,errors){if(result){ /*s*/ ActionAndUpdate(0, nid, pid, 'NULL'); /*e*/ }},true); }

function AddNewMenuAct(nid, pid) { chk=$('#Rb0').attr('checked'); if(chk=='checked'){chk=1;}else{chk=0;} name=$('#Rn0').val(); link=$('#Rl0').val(); css=$('#Rc0').val(); error=0; if (name=="" || name=="NULL"){ error=1; $("#Rn0").toggleClass("ErrorInput", true);} if (link=="" || link=="NULL"){ error=1; $("#Rl0").toggleClass("ErrorInput", true);} if (error==0) { $("#JSLoader").html(loader2); JsHttpRequest.query('modules/adm/menuedit-add-JSReq.php',{'nid':nid, 'pid':pid, 'chk':chk, 'name':name, 'link':link, 'css':css},function(result,errors){if(result){ /*s*/  ActionAndUpdate(0, nid, pid, 'NULL'); /*e*/ }},true); }}

function ActionAndUpdate(id, nid, pid, act) { CloseBlank(); $("#Msg2").html("Идет сохранение данных..."); $("#Msg2").removeClass(); $("#Msg2").addClass("SaveDiv"); JsHttpRequest.query('modules/adm/menuedit-update-JSReq.php',{'nid':nid, 'pid':pid, 'id':id, 'act':act},function(result,errors){ if(result){  /*s*/ $("#Tgg").html(result["content"]); $("#Msg2").html("Данные успешно сохранены"); $("#Msg2").removeClass(); $("#Msg2").addClass("SuccessDiv"); $('#Tgg input').tzCheckbox({labels:['да ','нет']});  /*e*/ }},true); }

function ItemDelete(id, nid, pid) { caption="Подтвердите удаление"; text='Удалить пункт меню и все его дочерние элементы?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", "+nid+", "+pid+", \"DEL\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }

function ItemUp(id, nid, pid) { ActionAndUpdate(id, nid, pid, 'UP'); }
function ItemDown(id, nid, pid) { ActionAndUpdate(id, nid, pid, 'DOWN'); }





