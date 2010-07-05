<?php
/*
 * Author: Odin
 * This script grabs the latest heise.de quotes and puts them into
 * a .rdf file specified in the variable $file.
 * this RDF file can be read by most RSS readers, so you get a nice
 * heise.de fullfeed without ads
 */
if (php_sapi_name() != "cli") {
	die("cli only");
}

$file = "/home/user/heise.rdf";
function parse_heise($url){
	$page = file_get_contents($url);
	$pattern = "|<!-- RSPEAK_START -->(.*?)<!-- RSPEAK_STOP -->|is";
	preg_match_all($pattern, $page, $out);
	$content = $out[1];
	foreach ($content AS $blob){
		if (!empty($blob)){

			if (!preg_match("|<.*h1>|is", $blob)){
				$out .= strip_tags($blob,"<a>");
			}
		}
	}
	//Foren Link anfügen

	$pattern = '$"news_foren".*?<p>(.*?)</p>$is';
	preg_match($pattern, $page, $link);
	$out .= strip_tags($link[1],"<a><b>");

	if (!empty($out)){
		return $out;
	}
	else {
		return false;
	}
}





$rss = file_get_contents("http://www.heise.de/newsticker/heise.rdf");
$pattern = "|<item>.*?<title>(.*?)</title>.*?<link>(.*?)</link>.*?</item>|is";
preg_match_all($pattern, $rss, $out);
// Array formatieren:
$length = count($out[1]);
//$length=1;
for ($i = 0; $i < $length; $i++) {
	$data[$i]['title'] = $out[1][$i];
	$data[$i]['url'] = $out[2][$i];
	$data[$i]['content'] = preg_replace("/[\n|\t]+/m", '<br>',preg_replace("|Array|","",parse_heise($data[$i]['url'])));

}

$feed ='<?xml version="1.0" encoding="utf-8"?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://my.netscape.com/rdf/simple/0.9/">

  <channel>

    <title>heise online News</title>
    <link>http://www.heise.de/newsticker/</link>
	<description>Nachrichten nicht nur aus der Welt der Computer</description>';

	for ($i=0; $i < $length; $i++ ){
		$feed .=  '<item>'. "\n";
		$feed .= '<title>'.$data[$i]['title'].'</title>' . "\n";
		$feed .= '<link>'.$data[$i]['url'].'</link>' . "\n";
		$feed .= '<description><![CDATA[' ;
		$feed .= $data[$i]['content'];
		$feed .=  ']]></description>'. "\n";
		$feed .= '</item>' . "\n";
	}

$feed .= "
</channel>
</rdf:RDF>";

unlink($file);
$fh = fopen($file, 'w');
fwrite ($fh, $feed);
fclose($fh);



?>
