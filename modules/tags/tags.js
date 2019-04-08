function GetItemVoting(qid) {		
	JsHttpRequest.query('/modules/lenta/GetVoting-JSReq.php',{'qid':qid},function(result,errors){ 
		if (result["Code"]==1) $("#ItemVotingDiv").html(result["Text"]); 
	},true);	
}


function voteSave(qid, nodeId, link){
	var vid = $('#ItemVotingDiv input:checked').val();
	if(vid){
		JsHttpRequest.query('/modules/lenta/VoteSave-JSReq.php',{'vid':vid, 'qid':qid, 'pid':nodeId, 'link':link},function(result,errors){ 
			if (result["Code"]==1) $("#ItemVotingDiv").html(result["Text"]);
		},true);
	}
	else alert('Вы не выбрали ответ');
}