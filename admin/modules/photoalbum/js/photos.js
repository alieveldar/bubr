var Maps = {}; var GET=parseGetParams(); var pid=GET["id"]; var cat=GET['cat'].split('_'); var ids;
$(document).ready(function() {
	if ($('.texteditors' ).size()!=0) { $('.texteditors' ).ckeditor({customConfig:'/admin/texteditor/config_sm.js'}); }	
	var uploader=new qq.FineUploader({
		element: document.getElementById('uploader'),
		request: {
			paramsInBody: false,
			params: {
				table : cat[0]+'_photos',
				pid: GET['id'],   // номер записи (новости)
				link: cat[0], // название раздела - родителя (auto)
			}
	    },
	    callbacks: {
	    	onComplete: function(id, fileName, responseJSON) {
	    		if(this.getInProgress() == 0) { setTimeout('document.location=document.location;', 1000); }
	    	}
	    },
	    debug: true
    });
});


function ItemDelete(id, pic) { $("#DEL"+id).html(loader); caption="Подтвердите удаление"; text='Удалить запись?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\", \""+pic+"\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank(); ReturnI("+id+", \""+pic+"\")'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function MultiDelete() { ids = []; $('.selectItem:checked').each(function(){ ids.push($(this).attr('id')); }); caption="Подтвердите удаление"; text='Удалить записи?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate(\""+ids.join()+"\", \"DEL\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }

function ItemEdit(id) {
	var o=CKEDITOR.instances['textckedit']; if (o) { o.destroy(); }
	var editbtn = $("#EDIT"+id).html(); $("#EDIT"+id).html(loader);
	JsHttpRequest.query('modules/photoalbum/photos-JSReq.php',{'id':id,'act':'FORM','link':cat[0]}, function(result,errors){ if(result){
		var cap="Редактировать фотографию"; text=""; var button='<div class="CenterText" id="JSLoader"><input type="submit" class="SaveButton" value="Сохранить" onclick="ActionAndUpdate('+id+', \'EDIT\', \'\');"></div>';
		text +="<tr class='TRLine0'><td width='17%'>Название фотографии<star>*</star></td><td class='LongInput' width='83%'><input name='name' type='text' value='"+result['d']['name']+"'></td></tr>"; 
		text +="<tr class='TRLine1'><td>Описание</td><td class='LongInput'><textarea name='text' class='textckedit' id='textckedit'>"+result['d']['text']+"</textarea></td></tr>";
		text +="<tr class='TRLine0'><td>Автор</td><td class='LongInput'><input name='author' type='text' value='"+result['d']['author']+"'></td></tr>";
		text +="<tr class='TRLine1'><td>Настройки</td><td class='DateInput'><input id='datepick' name='data' type='text' readonly value='"+result['d']['data']+"'> <input name='main' type='checkbox'"+(result['d']['main'] == 1 ? " checked" : "")+"> <span style='margin-right:15px;'>Обложка альбома</span> <input name='winner' type='checkbox'"+(result['d']['winner'] == 1 ? " checked" : "")+"> Победитель</td><tr>";
		text +="<!--<tr class='TRLine0'><td>Координаты на карте</td><td class='LongInput'><div id='Map"+id+"' class='Map'></div></td></tr>-->"; 
		text="<div class='RoundText' id='Tgg' style='width:650px;'><form id='editForm' onsubmit='return false'><table>"+text+"</table><div class='C10'></div><input name='maps' class='maps_"+id+"' type='hidden' value='"+result['d']['maps']+"'>"+button+"</form></div>"; ViewBlank(cap, text);
		$('input[name="main"], input[name="winner"]').tzCheckbox({labels:['да ','нет']});
		$("#EDIT"+id).html(editbtn);
		$('#textckedit').ckeditor({customConfig:'/admin/texteditor/config_sm.js'});
		var ht=$("#datepick").val(); $("#datepick").datepicker(); $("#datepick").datepicker("option","dateFormat", "dd.mm.yy"); $("#datepick").val(ht);
		initMap(id);
	}},true);
}

function ActionAndUpdate(id, act, pic) { 
	if(act == 'EDIT') { var Cover = $('input[name="main"]').attr('checked'); var Winner = $('input[name="winner"]').attr('checked'); var d = {'name':$('input[name="name"]').val(), 'text':$('textarea[name="text"]').val(), 'author':$('input[name="author"]').val(), 'data':$('input[name="data"]').val(), 'maps':$('input[name="maps"]').val(), 'main':Cover, 'winner':Winner}; } 
	CloseBlank(); JsHttpRequest.query('modules/photoalbum/photos-JSReq.php',{'id':id,'act':act,'pic':pic,'pid':pid,'link':cat[0],'d':d},function(result,errors){ if(result){  if (act=="DEL"){ /**/ if (act=="DEL"){ if(!$('.loader').size()) $('.MultiDel').hide(); if(/,/.test(id)){ for(var i = 0; i<ids.length; i++) $("#Line"+ids[i]).remove(); } else { $("#Line"+id).remove(); } } /**/ } else if (act=="EDIT"){ $("#Line"+id+" .Img a").attr('title', d.name); if(Cover) { $(".Cover").removeClass('Cover'); $("#Line"+id).addClass('Cover') } if(Winner && !$("#Line"+id+" .winner").size()) { $("#Line"+id).append('<div class="winner">Победитель</div>') } if(!Winner) $("#Line"+id+" .winner").remove(); } }},true); 
}
function ReturnI(id, pic) { $("#DEL"+id).html('<a href="javascript:void(0);" onclick="ItemDelete(\''+id+'\',\''+pic+'\')"><img src="/admin/images/icons/exit.png" valign="middle" title="" style="margin:-2px 3px 0 0;"></a>'); }
function ItemUp(id) { var adiv=$("#Line"+id).prev(); $("#Line"+id).insertBefore(adiv); ActionAndUpdate(id, "UP", ""); }
function ItemDown(id) { var adiv=$("#Line"+id).next(); $("#Line"+id).insertAfter(adiv); ActionAndUpdate(id, "DOWN", ""); }

function initMap(id){
	var coords = $('.maps_' + id).val() ? $('.maps_' + id).val() : $('.maps_default').val();
	coords = coords.split(',');
	// Создаем объект карты, связанный с контейнером:
	Maps[id] = new DG.Map('Map'+id);console.log(Maps[id]);
	// Устанавливаем центр карты, и коэффициент масштабирования:
    Maps[id].setCenter(new DG.GeoPoint(coords[0],coords[1]), $('.maps_' + id).val() ? 15 : 11);
    // Добавляем элемент управления коэффициентом масштабирования:
    Maps[id].controls.add(new DG.Controls.Zoom());
    if($('.maps_' + id).val()) SetMapPoint(id);
    
    Maps[id].geoclicker.disable();  
    Maps[id].addEventListener(Maps[id].getContainerId(), 'DgClick', function(e){
    	var balloons = Maps[id].balloons.getAll();
    	for(var i = 0; i < balloons.length; i++) balloons[i].hide();
    	
    	var balloon = new DG.Balloons.Common({
		   geoPoint: new DG.GeoPoint(e.getGeoPoint().getLon(),e.getGeoPoint().getLat()),
		   contentHtml: '<div class="dgInfocardGeo"><div class="loaderContainer"><img alt="" src="http://maps.api.2gis.ru/images/station-info-loader.gif" height="32px" width="32px"> <span class="loading">Загрузка данных</span></div></div>'
		});
		
		Maps[id].balloons.add(balloon);
    	Maps[id].geocoder.get(e.getGeoPoint(), {
			types: ['city', 'settlement', 'district', 'street', 'house'],
			limit: 1,
			// Обработка успешного поиска
			success: function(geocoderObjects) {
				var geocoderObject = geocoderObjects[0];
				var attributes = geocoderObject.getAttributes();
                info = '<div class="dgInfocardGeo">';
                info += '<div class="dg-map-geoclicker-address">';
                if(attributes && attributes.index) info += attributes.index+', ';
                info += geocoderObject.getName()+'</div>';
                if(attributes && attributes.purpose) info += '<span class="dg-map-geoclicker-purpose">'+attributes.purpose+'</span>';                
                info += '<a href="javascript:void(0)" class="dg-map-geoclicker-firmcount" onclick="SetMapPoint('+id+',\''+balloon.getId()+'\');">Выбрать эту точку</a>';
                info += '</div>';
                balloon.setContent(info);
			},
			failure: function(code, message) {
				balloon.setContent('<div class="dgInfocardGeo">Не удалось найти адрес в этой точке.<br><a href="javascript:void(0)" class="dg-map-geoclicker-firmcount" onclick="SetMapPoint('+id+',\''+balloon.getId()+'\');">Выбрать эту точку</a></div>');
			}
		});
    });           
}


function SetMapPoint(id, balloonId){
	if(balloonId){
		var balloon = Maps[id].balloons.get(balloonId);
		var geoPoint = balloon.getPosition();		
		$('.maps_' + id).val(geoPoint.getLon() + ',' + geoPoint.getLat());
	}
	else{
		var coords = ($('.maps_' + id).val()).split(',');
		var geoPoint = new DG.GeoPoint(coords[0],coords[1]);	
	}
    
	Maps[id].markers.removeAll();
	Maps[id].balloons.removeAll();
	var marker = new DG.Markers.Common({geoPoint: geoPoint });	
	Maps[id].markers.add(marker);
	var markerId = marker.getContainerId();
	$('#'+markerId).parent().addClass('custom');
	marker.setIcon(new DG.Icon($('#Line'+id+' .Img img').attr('src'), new DG.Size(35, 35), function() { return new DG.Point(-7, -51)} ));
}