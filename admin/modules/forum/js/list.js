/* ДОБАВИТЬ ФОРУМ */  var ids, fids;
function AddNewForum(alias) { var cap="Добавить новый форум"; var text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Добавить" onclick="AddNewForumAct(\''+alias+'\');"></div>';
text=text+'<tr class="TRLine0"><td>Название форума<star>*</star></td><td class="NormalInput"><input id="Rn0" type="text"></td></tr>';
text=text+'<tr class="TRLine1"><td>Описание форума</td><td class="NormalInput"><textarea id="Rl0"></textarea></td></tr>';
text=text+'<tr class="TRLine0"><td>Активный форум</td><td class="NormalInput"><input id="Rb0" type="checkbox" checked></td></tr>';
text=text+'<tr class="TRLine1"><td>Темы пользователей</td><td class="NormalInput"><input id="Rb1" type="checkbox" checked></td></tr>';
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>";
ViewBlank(cap, text); $('#Rb0').tzCheckbox({labels:['да ','нет']}); $('#Rb1').tzCheckbox({labels:['да ','нет']}); }
function AddNewForumAct(alias) { 
	chk1=$('#Rb0').attr('checked'); if(chk1=='checked'){chk1=1;}else{chk1=0;} chk2=$('#Rb1').attr('checked'); if(chk2=='checked'){chk2=1;}else{chk2=0;}
	name=$('#Rn0').val(); text=$('#Rl0').val(); error=0; if (name=="" || name=="NULL"){ error=1; $("#Rn0").toggleClass("ErrorInput", true);} 
	if (error==0) { $("#JSLoader").html(loader2); 
	JsHttpRequest.query('modules/forum/list-add-JSReq.php',{'alias':alias,'chk1':chk1,'chk2':chk2,'name':name,'text':text},function(result,errors){
	if(result){ /*s*/  console.log(result["log"]); ActionAndUpdate(alias, 0, 0, ''); /*e*/ }},true); }
}
/* РЕДАКТИРОВАТЬ  ФОРУМ */
function ForumEdit(alias, id, name, textf, c1, c2) { var cap="Редактировать форум"; text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Сохранить" onclick="SaveForumItem(\''+alias+'\',\''+id+'\');"></div>';
text=text+"<tr class='TRLine0'><td>Название форума<star>*</star></td><td class='NormalInput'><input id='Rn0' type='text' class='JsVerify' value='"+name+"'></td></tr>";
text=text+"<tr class='TRLine1'><td>Описание форума</td><td class='NormalInput'><textarea id='Rl0'>"+textf+"</textarea></td></tr>";
text=text+'<tr class="TRLine0"><td>Активный форум</td><td class="NormalInput"><input id="Rb0" type="checkbox" '+c1+'></td></tr>';
text=text+'<tr class="TRLine1"><td>Темы пользователей</td><td class="NormalInput"><input id="Rb1" type="checkbox" '+c2+'></td></tr>';
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>";
ViewBlank(cap, text); $('#Rb0').tzCheckbox({labels:['да ','нет']}); $('#Rb1').tzCheckbox({labels:['да ','нет']}); }
function SaveForumItem(alias, id) { 
	chk1=$('#Rb0').attr('checked'); if(chk1=='checked'){chk1=1;}else{chk1=0;} chk2=$('#Rb1').attr('checked'); if(chk2=='checked'){chk2=1;}else{chk2=0;}
	name=$('#Rn0').val(); texte=$('#Rl0').val(); error=0; if (name=="" || name=="NULL"){ error=1; $("#Rn0").toggleClass("ErrorInput", true);} 
	if (error==0) { $("#JSLoader").html(loader2);
	JsHttpRequest.query('modules/forum/list-edit-JSReq.php',{'id':id,'alias':alias,'chk1':chk1,'chk2':chk2,'name':name,'text':texte}, function(result,errors){
	if(result){ /*s*/ ActionAndUpdate(alias, 0, 0, ''); /*e*/ }},true); }
}
/* ДОБАВИТЬ КАТЕГОРИЮ */
function AddNewCat(alias, fid) { var cap="Добавить новую категорию"; var text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Добавить" onclick="AddNewCatAct(\''+alias+'\', \''+fid+'\');"></div>';
text=text+'<tr class="TRLine0"><td>Название категории<star>*</star></td><td class="NormalInput"><input id="Rn0" type="text"></td></tr>';
text=text+'<tr class="TRLine1"><td>Описание категории</td><td class="NormalInput"><textarea id="Rl0"></textarea></td></tr>';
text=text+'<tr class="TRLine0"><td>Активная категория</td><td class="NormalInput"><input id="Rb0" type="checkbox" checked></td></tr>';
text=text+'<tr class="TRLine1"><td>Темы пользователей</td><td class="NormalInput"><input id="Rb1" type="checkbox" checked></td></tr>';
text=text+'<tr class="TRLine0"><td>Закрепить в списке</td><td class="NormalInput"><input id="Rb2" type="checkbox"></td></tr>';
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>";
ViewBlank(cap, text); $('#Rb0').tzCheckbox({labels:['да ','нет']}); $('#Rb1').tzCheckbox({labels:['да ','нет']}); $('#Rb2').tzCheckbox({labels:['да ','нет']}); }
function AddNewCatAct(alias, fid) { 
	chk1=$('#Rb0').attr('checked'); if(chk1=='checked'){chk1=1;}else{chk1=0;} chk2=$('#Rb1').attr('checked'); if(chk2=='checked'){chk2=1;}else{chk2=0;} chk3=$('#Rb2').attr('checked'); if(chk3=='checked'){chk3=1;}else{chk3=0;}
	name=$('#Rn0').val(); text=$('#Rl0').val(); error=0; if (name=="" || name=="NULL"){ error=1; $("#Rn0").toggleClass("ErrorInput", true);}
	if (error==0) { $("#JSLoader").html(loader2); 
	JsHttpRequest.query('modules/forum/list-addcat-JSReq.php',{'alias':alias,'fid':fid,'chk1':chk1,'chk2':chk2,'chk3':chk3,'name':name,'text':text},function(result,errors){
	if(result){ /*s*/  console.log(result["log"]); ActionAndUpdate(alias, 0, 0, ''); /*e*/ }},true); }
}
/* РЕДАКТИРОВАТЬ КАТЕГОРИЮ */
function CatEditF(alias, id, fid, raz, name, texte, c1, c2, c3) { var cap="Редактировать категорию"; var text="";
var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Сохранить" onclick="SaveCatI(\''+alias+'\', \''+id+'\', \''+fid+'\');"></div>';
text=text+'<tr class="TRLine0"><td>Название категории<star>*</star></td><td class="NormalInput"><input id="Rn0" type="text" value="'+name+'"></td></tr>';
text=text+'<tr class="TRLine1"><td>Родитель категории</td><td class="NormalInput"><div class="sdiv">'+raz+'</div></td></tr>';
text=text+'<tr class="TRLine0"><td>Описание категории</td><td class="NormalInput"><textarea id="Rl0">'+texte+'</textarea></td></tr>';
text=text+'<tr class="TRLine1"><td>Активная категория</td><td class="NormalInput"><input id="Rb0" type="checkbox" '+c1+'></td></tr>';
text=text+'<tr class="TRLine0"><td>Темы пользователей</td><td class="NormalInput"><input id="Rb1" type="checkbox" '+c2+'></td></tr>';
text=text+'<tr class="TRLine1"><td>Закрепить в списке</td><td class="NormalInput"><input id="Rb2" type="checkbox" '+c3+'></td></tr>';
text="<div class='RoundText' id='Tgg' style='width:500px;'><table>"+text+"</table><div class='C10'></div>"+button+"</div>";
ViewBlank(cap, text); $("#allraz [value='"+fid+"']").attr("selected", "selected");
$('#Rb0').tzCheckbox({labels:['да ','нет']}); $('#Rb1').tzCheckbox({labels:['да ','нет']}); $('#Rb2').tzCheckbox({labels:['да ','нет']}); }
function SaveCatI(alias, id, fid) {
	chk1=$('#Rb0').attr('checked'); if(chk1=='checked'){chk1=1;}else{chk1=0;} chk2=$('#Rb1').attr('checked'); if(chk2=='checked'){chk2=1;}else{chk2=0;} chk3=$('#Rb2').attr('checked'); if(chk3=='checked'){chk3=1;}else{chk3=0;}
	name=$('#Rn0').val(); texte=$('#Rl0').val(); error=0; if (name=="" || name=="NULL"){ error=1; $("#Rn0").toggleClass("ErrorInput", true);} var nfid=$("#allraz :selected").val();
	if (error==0) { $("#JSLoader").html(loader2);
	JsHttpRequest.query('modules/forum/list-editcat-JSReq.php',{'alias':alias,'id':id,'fid':fid,'chk1':chk1,'chk2':chk2,'chk3':chk3,'name':name,'text':texte,'nfid':nfid},function(result,errors){
	if(result){ ActionAndUpdate(alias, 0, 0, ''); }},true); }
}
/* ОБНОВИТЬ ВЕСЬ СПИСОК */
function ActionAndUpdate(alias, id, fid, act) {
	CloseBlank(); $("#Msg2").html("Идет сохранение данных..."); $("#Msg2").removeClass(); $("#Msg2").addClass("SaveDiv");
	JsHttpRequest.query('modules/forum/list-update-JSReq.php',{'alias':alias,'id':id,'fid':fid,'act':act},function(result,errors){ 
		if(result){  /*s*/ 
			 $("#Tgg").html(result["content"]); $("#Msg2").html("Данные успешно сохранены"); $("#Msg2").removeClass(); $("#Msg2").addClass("SuccessDiv"); 
			$('#Tgg input:not(.selectItem)').tzCheckbox({labels:['да ','нет']}); console.log(result["log"]);  /*e*/ }
	},true);
}
/* ДЕЙСТВИЯ С ЭЛЕМЕНТАМИ */
function ItemDelete(alias, id, fid) { caption="Подтвердите удаление"; text='Удалить раздел и все его дочерние элементы?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate(\""+alias+"\","+id+","+fid+",\"DEL\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function MultiDelete(alias) { ids = []; fids = []; $('.selectItem:checked').each(function(){ var id = $(this).attr('id').split('-'); ids.push(id[0]); fids.push(id[1]); }); caption="Подтвердите удаление"; text='Удалить записи?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate(\""+alias+"\", \""+ids.join()+"\", \""+fids.join()+"\", \"DEL\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function ItemUp(alias, id, fid) { ActionAndUpdate(alias, id, fid, 'UP'); } function ItemDown(alias, id, fid) { ActionAndUpdate(alias, id, fid, 'DOWN'); }