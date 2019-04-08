<?	
$file="_topblock-new_default"; if (RetCache($file, "cacheblock")=="true") { list($Page["TopContent"], $cap)=GetCache($file, 0); } else { list($Page["TopContent"], $cap)=CreateTopBlock(); SetCache($file, $Page["TopContent"], "", "cacheblock"); }	

function CreateTopBlock() {
	global $Domains, $SubDomain, $GLOBAL, $C20; $text='';
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
		

		
		
		
		
	// --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---
	return(array($text, ""));
}
?>
