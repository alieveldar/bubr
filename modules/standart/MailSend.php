<?
@require_once $_SERVER['DOCUMENT_ROOT'].'/modules/standart/phpmailer/class.phpmailer.php';

function MailSend($to, $subject, $body, $from, $attachment = false, $filesPath = false){
	
	$adrs=explode(" ", trim(str_replace("  ", " ", str_replace(array(",", "\r\n", "\r", "\n"), " ", $to))));
	
	$mailer = @new PHPMailer();
	$mailer->FromName = '';
	$mailer->From = $from;
	$mailer->Subject = $subject;
	$mailer->Body = $body;
	$mailer->isHTML(true);
	foreach ($adrs as $adr) {
		$mailer->AddAddress($adr, '');
	}
	$mailer->CharSet = 'utf-8';
	
	if($attachment){
		foreach ($attachment as $file) {
			$mailer->AddAttachment($filesPath.$file, $file);
		}
	}
	
	if($mailer->Send()){
		$mailer->ClearAddresses();
		$mailer->ClearAttachments();
		return true;
	}
	else return false;
}
?>