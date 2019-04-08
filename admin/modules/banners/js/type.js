function AddNewTask(nid, pid) { var cap="Добавить тип баннера"; var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Добавить" onclick="AddNewTaskAct();"></div>'; 
text='<tr class="TRLine0"><td>Название типа<star>*</star></td><td class="NormalInput"><input id="Rl0" type="text" class="JsVerify" placeholder="Верхний боковой"></td></tr>';
text=text+'<tr class="TRLine1"><td>Размер: ширина<star>*</star></td><td class="NormalInput"><input id="Rl1" type="text" class="JsVerify" placeholder="50px или 100%"></td></tr>';
text=text+'<tr class="TRLine0"><td>Размер: высота<star>*</star></td><td class="NormalInput"><input id="Rl2" type="text" class="JsVerify" placeholder="50px или 100%"></td></tr>';
text=text+'<tr class="TRLine1"><td>В ротации<star>*</star></td><td class="NormalInput"><input id="Rl3" type="text" class="JsVerify" placeholder="5"></td></tr>';
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>"; ViewBlank(cap, text); }

function AddNewTaskAct() { n=$('#Rl0').val(); w=$('#Rl1').val(); h=$('#Rl2').val(); r=$('#Rl3').val(); error=0; if (n=="" || n=="NULL"){ error=1; $("#Rl0").toggleClass("ErrorInput", true);} if (h=="" || h=="NULL"){ h="100%"; } if (w=="" || w=="NULL"){ w="100%"; } if (r=="" || r=="NULL"){ r=5; }
if (error==0) { $("#JSLoader").html(loader2); JsHttpRequest.query('modules/banners/type-add-JSReq.php',{'n':n, 'w':w, 'h':h, 'r':r} , function(result,errors){if(result){ /*s*/ ActionAndUpdate(0,0); /*e*/ }} ,true); }}
 
function ItemEdit(id, n, w, h, r) { var cap="Редактировать тип"; var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Сохранить" onclick="SaveTaskItem(\''+id+'\');"></div>';
text='<tr class="TRLine0"><td>Название типа<star>*</star></td><td class="NormalInput"><input id="Rl0" type="text" class="JsVerify" placeholder="Верхний боковой" value="'+n+'"></td></tr>';
text=text+'<tr class="TRLine1"><td>Размер: ширина<star>*</star></td><td class="NormalInput"><input id="Rl1" type="text" class="JsVerify" placeholder="50px или 100%" value="'+w+'"></td></tr>';
text=text+'<tr class="TRLine0"><td>Размер: высота<star>*</star></td><td class="NormalInput"><input id="Rl2" type="text" class="JsVerify" placeholder="50px или 100%" value="'+h+'"></td></tr>';
text=text+'<tr class="TRLine1"><td>В ротации<star>*</star></td><td class="NormalInput"><input id="Rl3" type="text" class="JsVerify" placeholder="5" value="'+r+'"></td></tr>';
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>"; ViewBlank(cap, text); }
 
function SaveTaskItem(id) { n=$('#Rl0').val(); w=$('#Rl1').val(); h=$('#Rl2').val(); r=$('#Rl3').val(); error=0; if (n=="" || n=="NULL"){ error=1; $("#Rl0").toggleClass("ErrorInput", true);} if (h=="" || h=="NULL"){ h="100%"; } if (w=="" || w=="NULL"){ w="100%"; } if (r=="" || r=="NULL"){ r=5; }
$("#JSLoader").html(loader2); JsHttpRequest.query('modules/banners/type-edit-JSReq.php',{'id':id, 'n':n, 'w':w, 'h':h, 'r':r}, function(result,errors){if(result){ /*s*/ ActionAndUpdate(0,0); /*e*/ }} ,true); }

function ItemDelete(id) { caption="Подтвердите удаление"; text='Удалить тип баннера?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function ItemUp(id) { ActionAndUpdate(id, 'UP'); } function ItemDown(id) { ActionAndUpdate(id, 'DOWN'); }

function ActionAndUpdate(id, act) { CloseBlank(); $("#Msg2").html("Идет сохранение данных..."); $("#Msg2").removeClass(); $("#Msg2").addClass("SaveDiv"); JsHttpRequest.query('modules/banners/type-update-JSReq.php',{'id':id, 'act':act}
,function(result,errors){ if(result){  /*s*/ $("#Tgg").html(result["content"]); $("#Msg2").html("Данные успешно сохранены"); $("#Msg2").removeClass(); $("#Msg2").addClass("SuccessDiv"); $('#Tgg input').tzCheckbox({labels:['да ','нет']}
); /*e*/ }
}
,true); }
