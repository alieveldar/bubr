function GetItemVoting(qid) {
    JsHttpRequest.query('/modules/lenta/GetVoting-JSReq.php',{'qid':qid},function(result,errors){
        $("#ItemVotingDiv").html(result.text);
    },true);
}


function voteSave(qid, nodeId, link){
    var vid = $('#ItemVotingDiv input:checked').val();
    if(vid){
        $("a, span", $("#ItemVotingDiv .votingButton")).toggle();
        JsHttpRequest.query('/modules/lenta/VoteSave-JSReq.php',{'vid':vid, 'qid':qid, 'pid':nodeId, 'link':link},function(result,errors){
            $("#ItemVotingDiv").html(result.text);
        },true);
    }
    else alert('Вы не выбрали ответ');
}


function initMap(ev){
    center = ev[2].split(',');
    var Map = new DG.Map('Map');
    Map.setCenter(new DG.GeoPoint(center[0],center[1]), 14);
    Map.controls.add(new DG.Controls.Zoom());
    var id = ev[0];
    var name = ev[1];
    var icon = ev[3];
    var marker = new DG.Markers.Common({
        geoPoint: new DG.GeoPoint(center[0],center[1]),
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
            JsHttpRequest.query('/modules/eventmap/GetEvent-JSReq.php',{'id':eventId},function(result,errors){
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