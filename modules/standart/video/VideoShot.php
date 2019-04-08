<?
header ("Content-type: image/jpeg");
if (!is_file("../../userfiles/media/".$id.".jpg") || @filesize("../../userfiles/media/".$id.".jpg")<5000) { exec("ffmpeg -i ../../userfiles/media/".$id." -an -ss 30 -r 1 -vframes 1 -s 480x360 -y -f mjpeg ../../userfiles/media/".$id.".jpg"); } 
$picture=file_get_contents("../../userfiles/media/".$id.".jpg");
echo $picture;
?>