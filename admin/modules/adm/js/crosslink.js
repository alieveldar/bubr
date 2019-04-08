$(".JsVerify").live("focus", function(){ $(this).toggleClass("ErrorInput",false);}); function JsVerify(){ error=0; $(".JsVerify").each(function (i) { $(this).toggleClass("ErrorInput", false); var val=$(this).val(); for(i=0; i<NotAvaliable.length; i++){ if (NotAvaliable[i]==val){ error=1; $(this).toggleClass("ErrorInput",true); }} if (val=="" || val=="NULL"){ error=1; $(this).toggleClass("ErrorInput", true);} if ((/^[a-z0-9_]+$/g).test(val)==false) { error=1; $(this).toggleClass("ErrorInput", true); } }); if (error!=0) { return false; } else { return true; }}

function AddNewCross(nid, pid) { var cap="Добавить кросс-ссылку"; var text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Добавить" onclick="AddNewCrossAct(\''+nid+'\', \''+pid+'\');"></div>';
text=text+'<tr class="TRLine0"><td>Текст ссылки<star>*</star></td><td class="NormalInput"><input id="Rn0" type="text" class="JsVerify"></td></tr>';
text=text+'<tr class="TRLine1"><td>Адрес ссылки<star>*</star></td><td class="NormalInput"><input id="Rl0" type="text" class="JsVerify" value="/index/"></td></tr>';
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>"; ViewBlank(cap, text); }

function ItemEdit(name, link, id) { var cap="Редактировать кросс-ссылку"; text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Сохранить" onclick="SaveCrossItem(\''+id+'\');"></div>';
text=text+"<tr class='TRLine0'><td>Текст ссылки<star>*</star></td><td class='NormalInput'><input id='Rned' type='text' class='JsVerify' value='"+name+"'></td></tr>";
text=text+"<tr class='TRLine1'><td>Адрес ссылки<star>*</star></td><td class='NormalInput'><input id='Rled' type='text' class='JsVerify' value='"+link+"'></td></tr>";
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>"; ViewBlank(cap, text); }


function SaveCrossItem(id) { name=$('#Rned').val(); link=$('#Rled').val(); error=0; if (name=="" || name=="NULL"){ error=1; $("#Rned").toggleClass("ErrorInput", true);} if (link=="" || link=="NULL"){ error=1; $("#Rled").toggleClass("ErrorInput", true);} $("#JSLoader").html(loader2); JsHttpRequest.query('modules/adm/crosslink-edit-JSReq.php',{'id':id, 'name':name, 'link':link},function(result,errors){if(result){ /*s*/ ActionAndUpdate(0,0); /*e*/ }},true); }

function AddNewCrossAct() { name=$('#Rn0').val(); link=$('#Rl0').val(); error=0; if (name=="" || name=="NULL"){ error=1; $("#Rn0").toggleClass("ErrorInput", true);} if (link=="" || link=="NULL"){ error=1; $("#Rl0").toggleClass("ErrorInput", true);} if (error==0) { $("#JSLoader").html(loader2); JsHttpRequest.query('modules/adm/crosslink-add-JSReq.php',{'name':name, 'link':link},function(result,errors){if(result){ /*s*/ ActionAndUpdate(0,0); /*e*/ }},true); }}

function ActionAndUpdate(id, act) { CloseBlank(); $("#Msg2").html("Идет сохранение данных..."); $("#Msg2").removeClass(); $("#Msg2").addClass("SaveDiv"); JsHttpRequest.query('modules/adm/crosslink-update-JSReq.php',{'id':id, 'act':act},function(result,errors){ if(result){  /*s*/ $("#Tgg").html(result["content"]); $("#Msg2").html("Данные успешно сохранены"); $("#Msg2").removeClass(); $("#Msg2").addClass("SuccessDiv"); /*e*/ }},true); }

function ItemDelete(id) { caption="Подтвердите удаление"; text='Удалить запись?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }




