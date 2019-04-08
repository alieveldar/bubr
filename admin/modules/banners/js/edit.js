$(document).ready(function() {
    $("#zay").keydown(function(event) {
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || 
            (event.keyCode == 65 && event.ctrlKey === true) || 
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 return;
        }
        else {
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });
});

var GET=parseGetParams(); var pid=GET["id"]; function FocusZay() { $("#zayok").val("0"); $("#zay").toggleClass("ErrorInput", false); }
function LoadZayavka() { $("#Info").html("Идет загрузка данных"); if ($("#zay").val()==0) { $("#zay").toggleClass("ErrorInput", true); } else { $("#Loader").html(loader); var id=$("#zay").val();
JsHttpRequest.query('modules/banners/getIdorder-JSReq.php',{'id':id}, function(result,errors){ if(result){ $("#Loader").html(""); if (result["code"]==0) { $("#Msg2").attr("class", "ErrorDiv"); $("#zay").toggleClass("ErrorInput", true); $("#Info").html("Укажите номер заявки"); $("#Msg2").html("Не найдена заявка с указанным номером <b>#"+id+"</b>");  
} else { $("#Msg2").attr("class", "SuccessDiv"); $("#zcap").html("Заявка #"+id); $("#Msg2").html("Загружена заявка с указанным номером <b>#"+id+"</b>"); $("#zayok").val("1");  $("#Info").html(result["text"]); }}},true); }}

function JsVerify() { var error=0; if ($("#zayok").val()==0) { error=1; $("#zay").toggleClass("ErrorInput", true); $("#Msg2").attr("class", "ErrorDiv"); $("#Msg2").html("Введите номер заявки, согласно которой добавляете материал и нажмите <b>Загрузить данные</b>"); } 
if ($("#dname").val()=="") { error=1; $("#dname").toggleClass("ErrorInput", true); $("#Msg2").attr("class", "ErrorDiv"); $("#Msg2").html("Введите название нового рекламного материала"); } if (error==1) { return false; } else { return true;}}
