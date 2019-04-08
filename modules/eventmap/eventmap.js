function initMap(center, events){
	center = center.split(',');
	var Map = new DG.Map('Map');
    Map.setCenter(new DG.GeoPoint(center[0],center[1]), 12);
    Map.controls.add(new DG.Controls.Zoom());
    
    for(i = 0; i < events.length; i++){
    	if(!events[i][2]) continue;	
    	var id = events[i][0];
    	var name = events[i][1];
    	var coords = events[i][2].split(',');
    	var icon = events[i][3]; 
    	var marker = new DG.Markers.Common({
	    	geoPoint: new DG.GeoPoint(coords[0],coords[1]),
	    	icon : icon ? new DG.Icon(icon, new DG.Size(35, 35), function() { return new DG.Point(-7, -51)} ) : null,
	    	hint : name,	    	
	    	clickCallback : function(clickEvent, marker){
	    		var this_ = this;
	    		var iconurl = this_.getIcon().url;
	    		var markerId = this_.getContainerId();
	    		var icon = $('#'+markerId).attr('data-icon');
	    		var eventId = $('#'+markerId).attr('data-event');
				if(icon){
					$('#'+markerId).parent().addClass('custom'); 
		    		this_.setIcon(new DG.Icon('/template/standart/loader00.gif', new DG.Size(35, 35), function() { return new DG.Point(-7, -51)} ));
		    	}
	    		JsHttpRequest.query('/modules/eventmap/GetEvent-JSReq.php',{'id':eventId, 'readmore' : 1},function(result,errors){ 					
					if(icon) this_.setIcon(new DG.Icon(iconurl, new DG.Size(35, 35), function() { return new DG.Point(-7, -51)} ));
					else $('#'+markerId).parent().removeClass('custom');
					ViewBlank(result['name'], result['text']);
				},true);
	    	}
	    });
		Map.markers.add(marker);
		var markerId = marker.getContainerId();
		$('#'+markerId).attr('data-event', id);
		if(icon){
			$('#'+markerId).parent().addClass('custom');
			$('#'+markerId).attr('data-icon', 1);
		}
    }              
}

function Calendar(dates, currentDate){
	var momths_ru = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
	var datesArr = dates.split(',');
	var dateObj = currentDate ? new Date(currentDate) : new Date();
	var day = dateObj.getDate();
	var month = dateObj.getMonth();
	var year = dateObj.getFullYear();
	var firstDayDateObj = new Date(year, month, 1);
	var lastDayDateObj = new Date(year, month + 1, 0);
	var prevMonthDateObj = new Date(year, month - 1, 1);
	var nextMonthDateObj = new Date(year, month + 1, 1);
	var todayDateObj = new Date();
	var todayDay = todayDateObj.getDate();
	var todayMonth = todayDateObj.getMonth();
	var todayYear = todayDateObj.getFullYear();
	
	var calendar = '<table>';
	calendar += '<tr><th colspan="7"><a href="javascript:void(0);" class="prev" onclick="Calendar(\''+dates+'\', '+prevMonthDateObj.getTime()+');">&lt;</a> '+momths_ru[month]+' '+year+' <a href="javascript:void(0);" class="next" onclick="Calendar(\''+dates+'\', '+nextMonthDateObj.getTime()+');">&gt;</a></th></tr>';
	calendar += '<tr><th>ПН</th><th>ВТ</th><th>СР</th><th>ЧТ</th><th>ПТ</th><th>СБ</th><th>ВС</th></tr><tr>';	
	for(var i = 2 - firstDayDateObj.getDay(), j = 1; i <= lastDayDateObj.getDate() + (7 - (lastDayDateObj.getDay() == 0 ? 7 : lastDayDateObj.getDay())); i++, j++){
		var active = todayDay == i && todayMonth == month && todayYear == year ? ' class="active"' : '';
		if($.inArray(i+'-'+(month+1)+'-'+year, datesArr) != -1) calendar += '<td><a href="javascript:void(0);" onclick="getDayEvents(\''+i+'-'+(month+1)+'-'+year+'\', $(this))"'+active+'>'+i+'</a></td>';
		else calendar += '<td><span'+active+'>'+(i > 0 && i <= lastDayDateObj.getDate() ? i : '')+'</span></td>';
		if(j % 7 == 0) calendar += '</tr><tr>';		
	}
	calendar += '</tr></table>';
	$('.Calendar').html(calendar);
	$('.EventsList').empty();
	if($.inArray(todayDay+'-'+(todayMonth+1)+'-'+todayYear, datesArr) != -1 && todayMonth == month && todayYear == year) getDayEvents(todayDay+'-'+(todayMonth+1)+'-'+todayYear);
}

function getDayEvents(date, o){
	if(o){
		$('.Calendar .active').removeClass('active');
		o.addClass('active');
	}
	$('.EventsList').html('Загрузка');
	JsHttpRequest.query('/modules/eventmap/GetDayEvents-JSReq.php',{'date':date},function(result,errors){ 					
		$('.EventsList').html(result['text']);
	},true);
}
