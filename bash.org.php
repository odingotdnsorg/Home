<?php
/*
 * Author: Odin
 * This script grabs the latest bash.org quotes and puts them into
 * a .rdf file specified in the variable $file.
 * this RDF file can be read by most RSS readers, so you get a nice
 * bash.org fullfeed without ads
 */
if (php_sapi_name() != "cli") {
//	die("cli only");
}


$file = "/home/user/bash.rdf";


$rss = file_get_contents("http://bash.org/?latest");
$pattern = '|<p class="quote"><a href="\?(.*?)" title=.*?<p class="qt">(.*?)</p>|is';
preg_match_all($pattern, $rss, $out);
print_r($out);
// Array formatieren:

$length = count($out[1]);
//$length=1;

for ($i = 0; $i < $length; $i++) {
	$data[$i]['title'] = $out[1][$i];
	$data[$i]['url'] = "http://bash.org/?" . $out[1][$i]; 
	$data[$i]['content'] = $out[2][$i];

}

$feed ='<?xml version="1.0" encoding="utf-8"?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://my.netscape.com/rdf/simple/0.9/">

  <channel>

    <title>bash.org</title>
    <link>http://bash.org/</link>
	<description>Quote Database Home<description>';

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
