$(document).ready(function() {
	var uploaders = {};
	$('.uploader').each(function(index){
		var uploader = this;
		uploaders[index] = new qq.FineUploader({
			element: uploader,
			multiple: parseInt($(uploader).attr('data-multiple')),
			request: {
				endpoint: '/modules/standart/multiupload/server/handler3.php',
				paramsInBody: false,
			},
			callbacks: {
		    	onComplete: function(id, fileName, responseJSON) {
		    		if(responseJSON.success) {
		    			$('.uploaderFiles', $(uploader).parents('td')).append('<span class="imgCon"><img src="/userfiles/temp/'+responseJSON.uploadName+'" class="img" /><img src="/template/standart/exit.png" class="remove" onclick="imgRemove($(this))" /><input type="hidden" name="'+$(uploader).attr('data-name') + '" value="'+responseJSON.uploadName+'" /></span>');
		    			if(!parseInt($(uploader).attr('data-multiple'))) $(uploader).parents('.uploaderCon').hide();
		    		}
		    	}
		    },
		    debug: true
	    });
	});
	var ht=$("#datepick").val(); $("#datepick").datepicker(); $("#datepick").datepicker("option","dateFormat", "dd.mm.yy"); $("#datepick").val(ht);
});


function imgRemove(o){
	o.parents('td').find('.uploaderCon').show();
	o.parents('.imgCon').remove();
}

function ShowSets(o) {
	if(o.value == 0) $('.ShowSets').slideDown();
	else $('.ShowSets').slideUp();
}

var Maps = {};
function showMap(id, o) {
	var text = '<div id="Map'+id+'" style="width:600px; height:400px;"></div>';
	ViewBlank($('#company_' + id + ' .CompanyName').html(), text);
	Maps[id] = null;
	initMap(id, o.parents('.contacts'));
}

function initMap(id, contacts){
	if(!contacts) contacts = $('#company_' + id + ' .contacts');
	if(!$('.maps', contacts).size()) return;
	// Создаем объект карты, связанный с контейнером:
	$('#Map'+id).width(530).height(400);
	Maps[id] = new DG.Map('Map'+id);
	
	var gen_coords = {longitudes:[], latitudes:[]};	
	
    contacts.each(function(){
    	gen_coords = SetMapPoint(id, $(this), gen_coords);
    });
           
    var longitude = latitude = 0;
    var scale = 16;
    
    for(i in gen_coords.longitudes){
    	longitude += parseFloat(gen_coords.longitudes[i]); latitude += parseFloat(gen_coords.latitudes[i]);
    }
         
	// Устанавливаем центр карты, и коэффициент масштабирования:
    Maps[id].setCenter(new DG.GeoPoint(gen_coords.longitudes[0], gen_coords.latitudes[0]),scale);
    
    /*
    var mapPoint = Maps[id].converter.coordinatesToMapPixels(new DG.GeoPoint(gen_coords.longitudes[0], gen_coords.latitudes[0]));
    
    var min_x = max_x = mapPoint.x; var min_y = max_y = mapPoint.y;    
    
    for(i in gen_coords.longitudes){    	    
    	var mapPoint = Maps[id].converter.coordinatesToMapPixels(new DG.GeoPoint(gen_coords.longitudes[i], gen_coords.latitudes[i]));
    	if(mapPoint.x < min_x) min_x = mapPoint.x;
    	if(mapPoint.x > max_x) max_x = mapPoint.x;
    	if(mapPoint.y < min_y) min_y = mapPoint.y;
    	if(mapPoint.y > max_y) max_y = mapPoint.y;
    }
    
    if(min_x < 0 || max_x > $('#Map'+id).width() || min_y < 0 || max_y > $('#Map'+id).height()){
    	var dif_min_x = min_x < 0 ? Math.abs(min_x) : 0;
    	var dif_max_x = max_x > $('#Map'+id).width() ? max_x - $('#Map'+id).width() : 0;
    	var dif_min_y = min_y < 0 ? Math.abs(min_y) : 0;
    	var dif_max_y = max_y > $('#Map'+id).height() ? max_y - $('#Map'+id).height() : 0;
    	var dif_x = dif_min_x + dif_max_x;
    	var dif_y = dif_min_y + dif_max_y;
    	if(dif_x >= dif_y) scale = scale * $('#Map'+id).width() / (max_x + Math.abs(min_x));
    	else if(dif_x <= dif_y) scale = scale / $('#Map'+id).height() / (max_y + Math.abs(min_y));
    	console.log(scale);
    }
    
    
    
    /*
    if(min_lon < geoPoint_l.lon && Math.abs(min_lon - geoPoint_l.lon) > max_lon - geoPoint_r.lon) dif_lon = Math.abs(min_lon - geoPoint_l.lon);
    if(max_x > $('#Map'+id).width() - 10 && max_x - $('#Map'+id).width() + 10 > Math.abs(min_x - 10)) dif_x = Math.abs(max_x - $('#Map'+id).width() + 10);
    
    if(min_y < 10 && Math.abs(min_y - 10) > max_y - $('#Map'+id).height() + 10) dif_y = Math.abs(min_y - 10);
    if(max_y > $('#Map'+id).height() - 10 && max_y - $('#Map'+id).height() + 10 > Math.abs(min_y - 10)) dif_y = max_y - $('#Map'+id).height() + 10;
    
    if(dif_x > dif_y) { scale = (dif_x + $('#Map'+id).width()) / ($('#Map'+id).width() / scale) }
    else { scale = scale * ($('#Map'+id).height() + dif_y) / $('#Map'+id).height() }
    */    
    
    //Maps[id].setCenter(new DG.GeoPoint(longitude / gen_coords.longitudes.length, latitude / gen_coords.latitudes.length),scale);       
        
    // Добавляем элемент управления коэффициентом масштабирования:
    Maps[id].controls.add(new DG.Controls.Zoom());      
}


function SetMapPoint(id, contacts, gen_coords){	
	var coords = $('.maps', contacts).html().split(',');
	var geoPoint = new DG.GeoPoint(coords[0],coords[1]);
	
	gen_coords.longitudes.push(coords[0]);
	gen_coords.latitudes.push(coords[1]);
	
	info = '<div class="dgInfocardGeo">';
	//if($('#company_' + id + ' .CompanyName').size()) info += '<h1>'+$('#company_' + id + ' .CompanyName').html()+'</h1>';
    info += '<div class="dg-map-geoclicker-address">'+$('.address', contacts).html()+'</div>';
    if($('.phone span', contacts).html()) info += '<div style="margin:5px 0;"><b>Телефон:</b> '+$('.phone span', contacts).html()+'</div>';
    var worktimeArr = $('.worktime', contacts).html().split('|');
	var worktime = '';
	workt = false;
	for(var i = 0; i < worktimeArr.length; i++){
		worktime += '<td>'+worktimeArr[i]+'</td>';
		if(worktimeArr[i] != '') workt = true;
	}
	if(workt) {
		worktime = '<table class="worktimeTable"><tr><th>Понедельник</th><th>Вторник</th><th>Среда</th><th>Четверг</th><th>Пятница</th><th>Суббота</th><th>Воскресенье</th></tr><tr>'+worktime+'</tr></table>';
		info += '<div style="margin:5px 0;"><b>Время работы:</b>'+worktime+'</div>';
	}	
	info += '</div>';
	
	var markerOptions = {
        geoPoint: geoPoint,
        balloonOptions: {
            contentHtml: info
        }
    }
    
	var marker = new DG.Markers.MarkerWithBalloon(markerOptions);
	Maps[id].markers.add(marker);
	return gen_coords; 
}

function ItemDelete(id, tab, pid) { ActionAndUpdate(id, "DEL", tab, pid); }

function ActionAndUpdate(id, act, tab, pid) { CloseBlank(); JsHttpRequest.query('/modules/companies/qa-JSReq.php',{'id':id,'act':act,'tab':tab},function(result,errors){ if(result){ if (act=="DEL"){ $("#Act"+id+" a:last").remove(); $("#Act"+id+" a:first").attr('href', $("#Act"+id+" a:first").attr('href').replace('edit/'+id, 'add/'+pid)).find('img').attr('src', $("#Act"+id+' img').attr('src').replace('edit', 'add')).attr('title', 'Ответить'); } }},true); }
