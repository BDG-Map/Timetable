<?php 

header('Content-type: text/xml, charset=windows-1252');  
/* gets the contents of a file if it exists, otherwise grabs and caches */
function get_content($file,$url,$hours = 1) {
	//vars
	$current_time = time(); 
	//$expire_time = $hours * 60 * 60; 
	$expire_time = 10;
	$file_time = filemtime($file);
	//decisions, decisions
	if(file_exists($file) && ($current_time - $expire_time < $file_time)) {
		//echo 'returning from cached file';
		//echo '<script>alert("-CACHE-");</script>;'; 
		return file_get_contents($file);
	}
	else {
		$content = get_url($url);
		$content.= '<!-- cached:  '.time().'-->';
		$content.= '<!-- emebriNyelven:  '.date("Y-m-d H:i:s",time()).'-->';
		//$content.= '<!-- <valid:  '.date("Y-m-d H:i:s",time()).'-->';
		//$content.= '<valid ervenyesseg="2021-09-03"/>';
		file_put_contents($file,$content);
		//echo 'retrieved fresh from '.$url.':: '.$content;
		return $content;
	}
}

/* gets content from a URL via curl */
function get_url($url) {
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}
$cacheFajl = 'orarendcache.txt';
$bdgURL = 'https://app.berzsenyi.hu/orarend/data.xml';
$orarend = get_content($cacheFajl,$bdgURL,1);

$xml = simplexml_load_string($orarend);
$game = $xml->addChild("valid");
$game->addAttribute("ervenyesseg", date("Y-m-d",time()));


$groups = $xml->xpath("//group");
$classes = $xml->xpath("//class");

$semmi = 0;

foreach ($groups as $group) 
{
    //for ($x = 0; $x <= count($classes); $x++)
    foreach ($classes as $osztaly)
    {
        //$group["studentcount"] = $semmi++;
        //["studentids"] = $group["classid"]."____".$osztaly["id"];
        
        if(strcmp($group["classid"],$osztaly["id"]) == 0)
        {
            $eddigi = $group["name"];
            $group["name"] = $osztaly["name"]." - ".$eddigi;
            //$group["entireclass"] = "jó";
            
        }
        
    }
    
    
    
    //echo $group;
    //$group["entireclass"] = "new value";
}    


echo $xml->saveXML();

?>
