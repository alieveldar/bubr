var res=0; var answered=0;
function clickanswer(elem) {
	var id=$(elem).attr("id"); if ($(elem).hasClass('testanswering')) { var d=id.split("-");  $('.answer-'+d[1]).removeClass("testanswering"); answered=answered-(-1);
	
	if (d[2]==0) {
		/* $(elem).addClass("testno"); $("#div-"+d[1]+"-1").addClass("testok"); $("#ans-"+d[1]).addClass("anstextno"); $("#ans-"+d[1]).show(); $("#ans-"+d[1]).html(textno); */
	} else { 
		/* $(elem).addClass("testok"); $("#ans-"+d[1]).addClass("anstextok"); $("#ans-"+d[1]).show(); $("#ans-"+d[1]).html(textok); */
	}
	$(elem).addClass("testok"); $("#ans-"+d[1]).addClass("anstextok");
	res=res-(-1); 
	if (answered==total) { getTestResult(res); }
}}

function getTestResult(res) { var text=""; var i=0; end.forEach(function(element, index) { if (i==0) { text=element; i=1; }	if (res>=index) { text=element; }}); ViewBlank("Спасибо!", text); }