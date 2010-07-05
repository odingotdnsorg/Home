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
	$dllink = get_video_link($url);
echo $dllink . "\n";
}

// Funtionen die Links aus den br alpha seiten ziehen
function get_video_link($url){
	$popup = file_get_contents($url);
	$pattern = "|http://gffstream.vo.llnwd.net/(.*?)wmv|";
	preg_match_all($pattern, $popup, $out);
	return 	$out[0][3];
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
