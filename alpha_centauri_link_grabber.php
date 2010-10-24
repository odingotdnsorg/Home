<?php 
//linkliste
$index = file_get_contents("http://www.br-online.de/br-alpha/alpha-centauri/index.xml");


$pattern ='|<li><a href="/br-alpha/alpha-centauri/(.*?)">(.*?)</a></li>|i';
preg_match_all($pattern, $index, $out);

// $out enthält alle linkschnipsel der Jahresübersichen
foreach ($out[1] AS $item => $value) {
$value = "http://www.br-online.de/br-alpha/alpha-centauri/" . $value;
	foreach (get_popup_links($value) AS $link => $url) {
		$links[]= $url; 
	}
}
// $links enthält nun alle links zu den Popups der Sendungen
foreach ($links as $link => $url){
	$videos[] = get_video_link($url);
}

//$videos = array_unique($videos);
#print_r($videos);


foreach ($videos as $video) {
    //dateiname erzeugen und video herunterladen, danach in die datei schreiben
    
    $filename = $video['datum'] . "-" . $video['titel'] . ".wmv";
    $content = file_get_contents($video['link']);
        if (!$handle = fopen($filename, "w+")) {
            print "Kann die Datei $filename nicht öffnen";
        
        }    
        fwrite($handle, $content);
        echo $filename . " wurde erfolgreich gespeichert." . "\n";


}


// Funtionen die Links aus den br alpha seiten ziehen
function get_video_link($url){
	$popup = file_get_contents($url);
	$pattern = "|http://gffstream.vo.llnwd.net/(.*?)wmv|";
	preg_match_all($pattern, $popup, $out);
	$link =	$out[0][3];
    
    //Datum:
    $pattern = '$div class="date">(.*?)<\/div>.*?$is';
	preg_match_all($pattern, $popup, $out);
    $date = trim(strip_tags($out[1][0]));
    $date = date("Y-m-d",strtotime($date));
    
    // Titel: 
    $pattern = '$<em>alpha-Centauri<\/em>(.*?)<\/h3>$is';
	preg_match_all($pattern, $popup, $out);
    $titel = utf8_encode(str_replace('?','',str_replace(' ','_',trim(strip_tags($out[1][0])))));
    
    // Umlaute raus:
    $pat = array("Ö","Ä","Ü","ö","ä","ü");
    $repl = array("Oe", "Ae", "Ue", "oe", "ae", "ue");
    $titel = str_replace($pat, $repl, $titel);
    return array("titel" => $titel, "datum" => $date, "link" => $link);
}



function get_popup_links($url) {

	$base = "http://www.br-online.de/br-alpha/alpha-centauri/";
	$index = file_get_contents($url);
	$pattern ='|<a href="/br-alpha/alpha-centauri/(.*?).xml"|i';
	preg_match_all($pattern,$index,$out);
	foreach ($out[1] AS $item => $value){
		if ($value != "index" && !strstr($value,"videothek")) {
			$ret[] = $base.$value. ".xml";
		}
	}
	return $ret;
}
?>
