<?
### НАСТРОЙКИ САЙТА

if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

// РАЗДЕЛ
	/*$data=DB("SELECT `id`,`shortname`,`link`, `sets` FROM `_pages` WHERE (`link`='".$alias."') LIMIT 1");
	if ($data["total"]!=1) { 
	$AdminText=ATextReplace('Item-Module-Error', $id, "_pages"); 
	$GLOBAL["error"]=1; 
	} else {
	@mysql_data_seek($data["result"], 0); 
	$raz=@mysql_fetch_array($data["result"]); 
*/
// СОХРАНЕНИЕ ПОЛЕЙ И ФОРМ
	//$P=$_POST;
	//if (isset($P["savebutton"])) {
		
		
		//Запрос сохранения полей
		
	//$q="UPDATE  `".$alias."_queries` SET `name`='".$P["qname"]."',`types`='".(int)$P["qtypes"]. "',`text`='".$P["qtext"]."'WHERE id='".(int)$id."'";
	//$_SESSION["Msg"]="<div class='SuccessDiv'>Ответ  успешно отредактирован! <a href ='?cat=".
	//$alias.	"_voting&id=".(int)$_GET["pid"]."'>Перейти к списку вопросов</a>   </div>";
		
		
		
		
		
  //$q="UPDATE  `".$alias."_answers` SET `text`='".$P["qname"]."' WHERE qid='".(int)$id."'";
	//$data=DB($q); 
		//$last=DBL(); 
		//DB("UPDATE `".$alias."_lenta` SET `rate`='".$last."' WHERE  (id='".$last."')");
		//$ya_request = file_get_contents("http://site.yandex.ru/ping.xml?urls=".urlencode("http://".$VARS['mdomain']."/".$alias."/view/".$last)."&login=v-Disciple&search_id=2043787&key=315057c26103684b3ab8224c10107ad8ef55f963");
		//@header("location: ?cat=".$alias."_aedit&id=".$id);
		//exit();
	//}
	

	
	
	
	$P=$_POST;
	if (isset($P["savebutton"])) {	
	
	
	    //var_dump($P["atypes"]);
	   ///echo "type = = = ".$P["atypes"];
		$t=(int)$P["atypes"];
		//$P["atypes"]
		
	    switch ($t){
		 case 1 : 
		 {
		 
		 
		  
		 foreach ($P["Inp"] as $key=>$val) {
			//echo " val == ".$val ;
			//echo "  key = ".$key;
			$q="UPDATE `".$alias."_answers` SET `text`='".$val."',`points` = '" .$P["points"][$key]."'  WHERE (`id`='".(int)$key."')"; 
			//echo $q;
			DB($q);
		   }
		  $_SESSION["Msg"]="<div class='SuccessDiv'>Настройки успешно сохранены</div>"; 
		  @header("location: ?cat=".$alias."_aedit&id=".$id); 
		  exit();
		 
		 
		 }
		 break;
	    case 2 : {  
		
		
		
		
		$data=DB("SELECT * FROM `".$alias."_answers` WHERE (`qid`=".(int)$id.")");
		$votes = array();
		if($data["total"]){
			for ($i=0; $i<$data["total"]; $i++){
				@mysql_data_seek($data["result"], $i);
				$ar=@mysql_fetch_array($data["result"]);
				$votes[] = $ar["id"];
				
				
				
			}
		}
		
		
		foreach ($P["votes"] as $key => $value) {
		    
			$value=str_replace("'", "&#039;", $value);
			if($value == '' && in_array($key, $votes)) 
			//echo "Я тут ".in_array($key, $votes)." key ".$key. "value " .$value ;
			//echo "i deleted";
			DB("DELETE FROM `".$alias."_answers` WHERE (`id`=$key)");
			else if(in_array($key, $votes) && $value != '') {
			//echo "key=".$key."text=".$value."points = ".var_dump($P["points"]);
			
			DB("UPDATE `".$alias."_answers` SET `text`='$value' ,`points`='".$P["points"][$key]."' WHERE (`id`=$key)");
			
			//echo "UPDATE `".$alias."_answers` SET `text`='".$value."' `points`='".$point."' WHERE (`id`=$key)";
			
			}
			else if($value != '')
			
			
			DB("INSERT INTO `".$alias."_answers` (`qid`, `text`,`points`) VALUES ('$id', '$value','".$P["points"][$key]."')");
		}
		
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно отредактирована!</div>"; 
		@header("location: ?cat=".$alias."_aedit&id=".$id);
		exit();
		
		
	    } break;

        case 3 : 
        {
         $data=DB("SELECT * FROM `".$alias."_answers` WHERE (`qid`=".(int)$id.")");
		$votes = array();
		if($data["total"]){
			for ($i=0; $i<$data["total"]; $i++){
				@mysql_data_seek($data["result"], $i);
				$ar=@mysql_fetch_array($data["result"]);
				$votes[] = $ar["id"];
				
				
				
			}
		}
		
		
		foreach ($P["votes"] as $key => $value) {
		    
			$value=str_replace("'", "&#039;", $value);
			if($value == '' && in_array($key, $votes)) 
			//echo "Я тут ".in_array($key, $votes)." key ".$key. "value " .$value ;
			//echo "i deleted";
			DB("DELETE FROM `".$alias."_answers` WHERE (`id`=$key)");
			else if(in_array($key, $votes) && $value != '') {
			//echo "key=".$key."text=".$value."points = ".var_dump($P["points"]);
			
			DB("UPDATE `".$alias."_answers` SET `text`='$value' ,`points`='".$P["points"][$key]."' WHERE (`id`=$key)");
			
			//echo "UPDATE `".$alias."_answers` SET `text`='".$value."' `points`='".$point."' WHERE (`id`=$key)";
			
			}
			else if($value != '')
			
			
			DB("INSERT INTO `".$alias."_answers` (`qid`, `text`,`points`) VALUES ('$id', '$value','".$P["points"][$key]."')");
		}
		
		$_SESSION["Msg"]="<div class='SuccessDiv'>Запись успешно отредактирована!</div>"; 
		@header("location: ?cat=".$alias."_aedit&id=".$id);
		exit();
		
        } break;    		
		 
		};
		
	}
	

	$answers=array(0=>"Поле для ввода текста",1=>"Ответ - картинка",2=>"Единичный выбор из вариантов",3=>"Множественный выбор из вариантов");
	
	$G=$_GET;
	
	
	//select answers.* ,queries.types from tests_answers answers 
	//left join  tests_queries  queries on queries.id=answers.qid   where answers.qid=1
	//$q="SELECT answers.*,queries.types,queries.name as qname from ".$alias."_answers answers left join  ".
	//$alias."_queries queries on queries.id=answers.qid  where answers.qid='".(int)$id."' group by answers.id";
	
	$q1="SELECT queries.types,queries.name as qname  FROM  ".$alias."_queries queries WHERE id='".(int)$id."' group by 1";
	$q2="SELECT answers.* FROM ".$alias."_answers  answers WHERE answers.qid = '".(int)$id."' GROUP BY 1 ";
	
	echo $q1 ."<br>".$q2;
	//echo $id;
	//echo $q;
	//echo "SELECT `".$alias."_answers`.*, `".$alias."_queries`.`types`   FROM `".$alias."_answers` left join `".$alias."_queries` on 
	//`".$alias."_queries`.`id`=`".$alias."_answers`.`qid`
	//WHERE (`".$alias."_answers`.`qid`='".(int)$id."') group by 1";
	//echo 'id = '.$id.'types '.$G['types'] ;
	
	//$data=DB("SELECT `".$alias."_answers`.*, `".$alias."_queries`.`types`   FROM `".$alias."_answers` left join `".$alias."_queries` on 
	//`".$alias."_queries`.`id`=`".$alias."_answers`.`qid`
	//WHERE (`".$alias."_answers`.`qid`='".(int)$id."') group by 1"); 
		$data=DB($q1);
		
		@mysql_data_seek($data["result"],0); 
		$node=@mysql_fetch_array($data["result"]); 		

	    //var_dump($node);
		$AdminText='<h2>Редактирование ответов: &laquo'.$node["qname"].'&raquo;</h2>'.$_SESSION["Msg"];
	    $AdminText.='<form action="'.$_SERVER["REQUEST_URI"].'" enctype="multipart/form-data" method="post">';
		$AdminText.='<div style="display:none;"><input type="text" value="'.$node['types'].'" name="atypes">  </div>';
		//Определение типа ответа
		
		
        
	    
		//echo "I'm here " . "type = ".$node['types'];
		$data=DB($q2);
		
		//@mysql_data_seek($data["result"],0); 
		//$node=@mysql_fetch_array($data["result"]); 
	switch($node['types']) 
	{
	  
	  case 1 : ///выбор ответов из картинок
	  {
	  
	  $AdminText.="<div class='RoundText'>".'<div id="uploader" class="align-center"></div>';
	  $AdminText.="<div class='Info' align='center'>Вы можете загружать файлы jpg, png, gif до 10М и размером не более 10.000px на 10.000px</div>".'</div>';
	
	  
	  if ($data["total"]>0) {
		$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/adapters/jquery.js'></script>";
		$AdminText.="<div class='RoundText'><div class='LinkR MultiDel'><a href='javascript:void(0);' onclick='MultiDelete()'>Удалить выбранные</a></div><table>";
		for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); 
		$ar=@mysql_fetch_array($data["result"]);
		//if ($ar["stat"]==1) {
		//$chk0="checked"; 
		//} else {$chk0="";}
			$img="<img src='/userfiles/picpreview/".$ar["pic"]."' width='150' />"; ///$chb=$ar["sets"]==1?"checked":"";
			$AdminText.='<tr class="TRLine" id="Line'.$ar["id"].'" style="border-bottom:2px dotted #CCC;">
			<td class="LongInput" style="width:10%;" valign="top" align="center" >'.$img.$C10.'
			
			<td class="LongInput" style="width:80%;" valign="top"><input name="Inp['.$ar["id"].']" value="'.$ar["text"].'" placeholder="Название фотографии">'.$C5.'
			<span style="display:block; float:left; margin-right:5px;">';
			
			
			
			$AdminText.="<td class='LongInput' style='width:80%;' valign='top'><input type='text' name='points[".$ar["id"]."]'  placeholder='Баллы за ответ' value='".$ar["points"]."'> </td>";
			
			
			///$AdminText.="<textarea name='PostText[".$ar["id"]."]' id='textedit".$ar["id"]."' style='outline:none;' class='texteditors'>".$ar["text"]."</textarea>";
			$AdminText.='</td><td style="padding-top:10px !important;" valign="top">
				<div  class="Act"><input type="checkbox" id="'.$ar["id"].'" class="selectItem"></div>'.$C15.'
				<div id="Act'.$ar["id"].'" class="Act"><a href="javascript:void(0);" onclick="ItemDelete(\''.$ar["id"].'\', \''.$ar["pic"].'\')">'.AIco('exit').'</a></div>'.$C25.'
				<div  class="Act"><a href="javascript:void(0);" onclick="ItemUp(\''.$ar["id"].'\')" title="Поднять">'.AIco(3).'</a></div>'.$C15.'
				<div  class="Act"><a href="javascript:void(0);" onclick="ItemDown(\''.$ar["id"].'\')" title="Опустить">'.AIco(4).'</a></div>
			</td>';
			$AdminText.='</tr>';
		endfor;
		$AdminText.="</table>".$C15."<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить настройки'></div></div>";
	    }
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  }
	  
	  break; 
	  case 2  ://выбор из готовых вариантов единичный выбор
      { 
	  
	  $AdminText.='<div class="RoundText"><table><tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>';
       $AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:15px;">Ответы и балы</td><td id="Answers" class="LongInput" >';
	   if($data["total"]){
		for ($i=0; $i<$data["total"]; $i++)
		{	
		    @mysql_data_seek($data["result"],$i); 
		    $node=@mysql_fetch_array($data["result"]); 
			//$AdminText.='<div><input type="text"  name="points['.$node["id"].']" value="'.$node["points"].'"  style="float:right;margin-right:15px;width:120px;" placeholder="Баллы за ответ"><input  placeholder="Вариант ответа" name="votes['.$node["id"].']" type="text"   value=\''.$node["text"].'\' style="float:left;width:400px;">';
			$AdminText.='<div><input  placeholder="Вариант ответа" name="votes['.$node["id"].']" type="text"   value=\''.
            $node["text"].'\' style="float:left;width:400px;" class="qtext"><input type="text"  name="points['.$node["id"].']" value="'.$node["points"].
            '"  style="float:right;margin-right:15px;width:120px;"placeholder="Баллы за ответ">';			
			
			if($i >= 2) $AdminText.='<a title="Удалить" onclick="RemoveField($(this))" href="javascript:void(0);"><img style="margin:2px 0 0 3px; width:14px;" valign="middle" src="/admin/images/icons/exit.png"></a>';
			$AdminText.=$C5.'</div>';
		}
		}
		else{
			$AdminText.='<div><input  placeholder="Вариант ответа" name="votes[]" type="text"  
             style="float:left;width:400px;" class="qtext"><input type="text"  name="points[]" 
              style="float:right;margin-right:15px;width:120px;"placeholder="Баллы за ответ">';
			  $AdminText.='<div><input  placeholder="Вариант ответа" name="votes[]" type="text"  
             style="float:left;width:400px;" class="qtext"><input type="text"  name="points[]" 
              style="float:right;margin-right:15px;width:120px;"placeholder="Баллы за ответ">';
		}
		$AdminText.='</td><tr>';
		$AdminText.='<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="AddField();" class="AddField">Добавить поле</a></td><tr>';
	
		$AdminText.='</table>';	
		$AdminText.='</div>';
		
		$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	
	  
	  
	  
	} break;
	  
	case 3: 
	
	
	{ 
	  
	  $AdminText.='<div class="RoundText"><table><tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>';
       $AdminText.='<tr class="TRLine1"><td class="VarText" style="vertical-align:top; padding-top:15px;">Ответы и балы</td><td id="Answers" class="LongInput" >';
	   if($data["total"]){
		for ($i=0; $i<$data["total"]; $i++)
		{	
		    @mysql_data_seek($data["result"],$i); 
		    $node=@mysql_fetch_array($data["result"]); 
			//$AdminText.='<div><input type="text"  name="points['.$node["id"].']" value="'.$node["points"].'"  style="float:right;margin-right:15px;width:120px;" placeholder="Баллы за ответ"><input  placeholder="Вариант ответа" name="votes['.$node["id"].']" type="text"   value=\''.$node["text"].'\' style="float:left;width:400px;">';
			$AdminText.='<div><input  placeholder="Вариант ответа" name="votes['.$node["id"].']" type="text"   value=\''.
            $node["text"].'\' style="float:left;width:400px;" class="qtext"><input type="text"  name="points['.$node["id"].']" value="'.$node["points"].
            '"  style="float:right;margin-right:15px;width:120px;"placeholder="Баллы за ответ">';			
			
			if($i >= 2) $AdminText.='<a title="Удалить" onclick="RemoveField($(this))" href="javascript:void(0);"><img style="margin:2px 0 0 3px; width:14px;" valign="middle" src="/admin/images/icons/exit.png"></a>';
			$AdminText.=$C5.'</div>';
		}
		}
		else{
			//$AdminText.='<input name="votes[]" type="text">'.$C5 .'<input name="points[]" type="text">' ;
			//$AdminText.='<input name="votes[]" type="text">'.$C5.'<input name="points[]" type="text">';
			$AdminText.='<div><input  placeholder="Вариант ответа" name="votes[]" type="text"  
             style="float:left;width:400px;" class="qtext"><input type="text"  name="points[]" 
              style="float:right;margin-right:15px;width:120px;"placeholder="Баллы за ответ">';
			  $AdminText.='<div><input  placeholder="Вариант ответа" name="votes[]" type="text"  
             style="float:left;width:400px;" class="qtext"><input type="text"  name="points[]" 
              style="float:right;margin-right:15px;width:120px;"placeholder="Баллы за ответ">';
		}
		$AdminText.='</td><tr>';
		$AdminText.='<tr class="TRLine0"><td class="VarName"></td><td><a href="javascript:void(0);" onclick="AddField();" class="AddField">Добавить поле</a></td><tr>';
	
		$AdminText.='</table>';	
		$AdminText.='</div>';
		
		$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные'></div>";
	
	  
	  
	  
	}
	break;
	
	
	
	
	  
	  
	  
	  
	   
	  
	  
	  };
    
	   

	

	
	
	
	    //echo 'I"m Writing : '.$node["name"];
	    
		
		
	/*	
	$AdminText='<h2>Редактирование: &laquo'.$node["name"].'&raquo;</h2>'.$_SESSION["Msg"];
	$AdminText.="<form action='".$_SERVER["REQUEST_URI"]."&pid=".$node["pid"]."' enctype='multipart/form-data' method='post'>";	
	
	$AdminText.="<script type='text/javascript' src='/admin/texteditor/ckeditor.js'></script><script type='text/javascript' src='/admin/texteditor/filemanager/ajex.js'></script>";
		
	
	$AdminText.="<div class='RoundText'><table>".
	'<tr class="TRLine0"><td style="width:22%;"></td><td style="width:78%;"></td></tr>
	<tr class="TRLine0"><td class="VarText">Заголовок вопроса<star>*</star></td>
	<td class="LongInput"><input name="qname" id="qname" type="text" class="JsVerify2" maxlength="80" value="'.$node["name"].'"></td><tr>
	
	<tr><td>Тип вопроса :</td><td><select name="qtypes" id="qtypes">'.GetSelected($answers, $node["types"]).'</select></td></tr>'.
	
	
	'<tr><td colspan="2">'.
	"<h2>Основное содержание публикации</h2><textarea name='qtext' id='textedit' style='outline:none;'>".$node["text"]."</textarea>
		<script type='text/javascript'>var editor=CKEDITOR.replace('textedit'); AjexFileManager.init({ returnTo: 'ckeditor', editor: editor});</script>".'
		
	</td></tr>'
	
	.
	
	"</table></div>";
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	*/
	
	### Сохранение
	//$AdminText.="<div class='CenterText'><input type='submit' name='savebutton' id='savebutton' class='SaveButton' value='Сохранить данные '></div>";
	
	$AdminText.="</form>";

// ПРАВАЯ КОЛОНКА
	//$AdminRight="<br><br><div class='SecondMenu2'><a href='?cat=".$alias."_qedit&id=".$id."' title='Редактировать ответы'>Редактировать ответы</a></div><br>.";
}
$_SESSION["Msg"]="";
?>