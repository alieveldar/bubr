$(document).ready(function() {
	$('#datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		onSelect:function(date, init){
			$('input', $(this).parent()).val(date);
		}
	});
	
	if($('input', $('#datepicker').parent()).val()){
		$('#datepicker').datepicker('setDate', $('input', $('#datepicker').parent()).val());
	}
	
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
	
	$('#textedit').ckeditor({customConfig:'/admin/texteditor/config_sm.js'});
	
	$('.Map').each(function(){
    	var id = $(this).attr('id').replace('Map', '');
    	initMap(id);	
    });
});

var Maps = {};
var Val;

function imgRemove(o){
	o.parents('td').find('.uploaderCon').show();
	o.parents('.imgCon').remove();
}


function JsVerify(){
	if(!$('input[name=maps]').val()){
		alert('Не выбрана точка на карте');
		return false;
	}
}

function clearCalendar(){
	$('input', $('#datepicker').parent()).val('');
	$('#datepicker').datepicker('setDate', 0);
}

function clearMap(id){
	$('.maps_' + id).val('');
	Maps[id].markers.removeAll();
	Maps[id].balloons.removeAll();
}


function initMap(id){
	var coords = $('.maps_' + id).val() ? $('.maps_' + id).val() : $('.maps_default').val();
	coords = coords.split(',');
	// Создаем объект карты, связанный с контейнером:
	Maps[id] = new DG.Map('Map'+id);
	// Устанавливаем центр карты, и коэффициент масштабирования:
    Maps[id].setCenter(new DG.GeoPoint(coords[0],coords[1]), id > 0 && $('.maps_' + id).val() ? 15 : 11);
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
	if($('input[name=icon]').val()){
		var markerId = marker.getContainerId();
		$('#'+markerId).parent().addClass('custom');
		marker.setIcon(new DG.Icon($('.img', $('input[name=icon]').parents('.imgCon')).attr('src'), new DG.Size(35, 35), function() { return new DG.Point(-7, -51)} ));
	}
}