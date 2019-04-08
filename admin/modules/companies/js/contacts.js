var Maps = {};
var Val;

$(document).ready(function(){ 
	$('.Map').each(function(){
    	var id = $(this).attr('id').replace('Map', '');
    	if(id > 0) initMap(id);	
    });
}); 

function ToggleBlock2(id) {
	$(id).toggle("normal", function(){
		if(!Maps[0]) initMap(0);			
	}); 
}

function adresInpFocus(Inp){
	Val = Inp.val();
}

function adresInpBlur(Inp){
	if(Inp.val() != Val && Inp.val() != '') Inp.next('a').show();
}

function GetGeoObjects(a, id){	
	Maps[id].geocoder.get(a.prev('input').val(), {
		types: ['city', 'settlement', 'district', 'street', 'house'],
		limit: 10,
		// Обработка успешного поиска
		success: function(geocoderObjects) {
			Maps[id].markers.removeAll();
			var gen_coords = {longitudes:0, latitudes:0};
			// Обходим циклом все полученные геообъекты
			for(var i = 0, len = geocoderObjects.length; i < len; i++) {
				var geocoderObject = geocoderObjects[i];
				var centerGeoPoint = geocoderObject.getCenterGeoPoint();
				gen_coords.longitudes += parseFloat(centerGeoPoint.getLon());
		       	gen_coords.latitudes += parseFloat(centerGeoPoint.getLat());

				var marker = geocoderObject.getMarker(null, (function(geocoderObject) {
	                return function(){
		                var centerGeoPoint = geocoderObject.getCenterGeoPoint();
		                var balloon = new DG.Balloons.Common({
						   geoPoint: centerGeoPoint,
						   contentHtml: ''
						});
						
						var attributes = geocoderObject.getAttributes();
		                info = '<div class="dgInfocardGeo">';
		                info += '<div class="dg-map-geoclicker-address">';
		                if(attributes && attributes.index) info += attributes.index+', ';
		                info += geocoderObject.getName()+'</div>';
		                if(attributes && attributes.purpose) info += '<span class="dg-map-geoclicker-purpose">'+attributes.purpose+'</span>';                
		                info += '<a href="javascript:void(0)" class="dg-map-geoclicker-firmcount" onclick="SetMapPoint('+id+',\''+balloon.getId()+'\');">Выбрать эту точку</a>';
		                info += '</div>';
		                
		                if (! Maps[id].balloons.getDefaultGroup().contains(balloon)) { Maps[id].balloons.add(balloon); balloon.setContent(info); }
					    else balloon.show();
				    }
	                
	             })(geocoderObject));
                
                Maps[id].markers.add(marker);	
                																
			}
			
			Maps[id].setCenter(new DG.GeoPoint(gen_coords.longitudes / geocoderObjects.length, gen_coords.latitudes / geocoderObjects.length),13);
			Maps[id].redraw();
		},
		// Обработка ошибок
		failure: function(code, message) {
			alert('Не удалось установить координаты на карте по этому адресу.'+"\n"+'Вы можете вручную установить координаты кликнув по карте в нужном месте');
		}
	});
}

function initMap(id){
	var coords = $('.maps_' + id).val() ? $('.maps_' + id).val() : $('.maps_default').val();
	coords = coords.split(',');
	// Создаем объект карты, связанный с контейнером:
	Maps[id] = new DG.Map('Map'+id);
	// Устанавливаем центр карты, и коэффициент масштабирования:
    Maps[id].setCenter(new DG.GeoPoint(coords[0],coords[1]), id > 0 && $('.maps_' + id).val() ? 16 : 11);
    // Добавляем элемент управления коэффициентом масштабирования:
    Maps[id].controls.add(new DG.Controls.Zoom());
    if(id > 0 && $('.maps_' + id).val()) SetMapPoint(id);
    
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
				alert('Не удалось найти адрес в этой точке.');
			}
		});
    });           
}


function SetMapPoint(id, balloonId){
	if(balloonId){
		var balloon = Maps[id].balloons.get(balloonId);
		var geoPoint = balloon.getPosition();
		var ballonHtml = $(balloon.getContent());
		var address = ballonHtml.find('.dg-map-geoclicker-address').html();
		
		$('.maps_' + id).val(geoPoint.getLon() + ',' + geoPoint.getLat());
	}
	else{
		var coords = ($('.maps_' + id).val()).split(',');
		var geoPoint = new DG.GeoPoint(coords[0],coords[1]);
		var address = $('.adres_' + id).val();		
	}
	
	info = '<div class="dgInfocardGeo">';
	info += '<h1>'+$('.companyName').html()+'</h1>';
    info += '<div class="dg-map-geoclicker-address">'+address+'</div>';
    if($('.phone_' + id).val()) info += '<div style="margin:10px 0;"><b>Телефон:</b> '+$('.phone_' + id).val()+'</div>';
	var worktime = '';
	workt = false;
	$('.worktime_' + id).each(function(){
		worktime += '<td>'+$(this).val()+'</td>';
		if($(this).val() != '') workt = true;
	});
	if(workt) {
		worktime = '<table class="worktimeTable"><tr><th>Понедельник</th><th>Вторник</th><th>Среда</th><th>Четверг</th><th>Пятница</th><th>Суббота</th><th>Воскресенье</th></tr><tr>'+worktime+'</tr></table>'
		info += '<div style="margin:10px 0;"><b>Время работы:</b>'+worktime+'</div>';
	}	
	info += '</div>';
	
	var markerOptions = {
        geoPoint: geoPoint,
        balloonOptions: {
            contentHtml: info
        }
    }
    
	Maps[id].markers.removeAll();
	Maps[id].balloons.removeAll();
	var marker = new DG.Markers.MarkerWithBalloon(markerOptions);
	Maps[id].markers.add(marker); 
}

function ItemDelete(id, tab) { $("#Act"+id).html(loader); caption="Подтвердите удаление"; text='Удалить запись?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\", \""+tab+"\");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank(); ReturnI("+id+", \""+tab+"\")'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); }
function ActionAndUpdate(id, act, tab) { CloseBlank(); JsHttpRequest.query('modules/companies/contacts-JSReq.php',{'id':id,'act':act,'tab':tab},function(result,errors){ if(result){ if (act=="DEL"){ $("#Line"+id).remove(); if(!$('.Line').size()) window.location = window.location; }  }},true); } function ReturnI(id, tab) { $("#Act"+id).html('<a href="javascript:void(0);" onclick="ItemDelete(\''+id+'\',\''+tab+'\')"><img src="/admin/images/icons/exit.png" valign="middle" title="" style="margin:-2px 3px 0 0;"></a>'); }