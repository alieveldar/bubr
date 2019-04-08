<?
session_start(); putenv('GDFONTPATH=' . realpath('.'));
$width = 90;               //Ширина изображения
$height = 28;               //Высота изображения
$font_size = 8;            //Размер шрифта
$let_amount = 4;            //Количество символов, которые нужно набрать
$fon_let_amount = 0;       //Количество символов на фоне
$font = "Captcha.ttf";   		//Путь к шрифту
//набор символов
$letters = array("a","b","d","e","f","g","h","k","m","n","p","s","t","v","x","y","z","2","3","4","5","6","7","8","9");     
//цвета
$colors = array("90","110","130","150","170","190","210"); 
$src = imagecreatetruecolor($width,$height);    //создаем изображение              
$fon = imagecolorallocate($src,255,255,255);    //создаем фон
imagefill($src,0,0,$fon);                       //заливаем изображение фоном
for($i=0;$i < $fon_let_amount;$i++)          	//добавляем на фон буковки
{
$color = imagecolorallocatealpha($src,rand(0,255),rand(0,255),rand(0,255),100);
$letter = $letters[rand(0,sizeof($letters)-1)];
$size = rand($font_size-2,$font_size+2);                                           
imagettftext($src,$size,rand(0,40),
rand($width*0.1,$width-$width*0.1),
rand($height*0.2,$height),$color,$font,$letter);
}

for($i=0;$i < $let_amount;$i++) {
$color = imagecolorallocatealpha($src,$colors[rand(0,sizeof($colors)-1)],
$colors[rand(0,sizeof($colors)-1)],
$colors[rand(0,sizeof($colors)-1)],rand(20,40));
$letter = $letters[rand(0,sizeof($letters)-1)];
$size = rand($font_size*2-2,$font_size*2+2);
$x = ($i+1)*$font_size + rand(1,5)-5+($i*15);      //даем каждому символу случайное смещение
$y = (($height*2)/3) + rand(0,5)+5;                           
$cod[] = $letter;                        //запоминаем код
imagettftext($src,$size,rand(0,15),$x,$y,$color,$font,$letter);
}
$_SESSION["CaptchaCode"] = implode("",$cod);     //переводим код в строку
header ("Content-type: image/gif");         //выводим готовую картинку
imagegif($src);

?>