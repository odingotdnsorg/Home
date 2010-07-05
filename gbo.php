<?php
/*
 * This script will crawl the famous site german-bash.org and search for 
 * recently added quotes. it will then spit out a RSS feed with the latest 50
 * Quotes. 
 */

$file = 'http://german-bash.org/action/latest';
$debug='false';

if ($fp = fopen($file, 'r')) {
   $content = '';
   // keep reading until there's nothing left
   while ($line = fread($fp, 1024)) {
      $content .= $line;
   }
}
$regexp = '$(.*?)<div class="quote_header">.*?<a title=.*?>#(.*?)</a>.*?<span class="date".*?>(.*?)</span>.*?<div class="zitat">(.*?)</div>$is';      
//matches: zitatnummer - 
preg_match_all($regexp, $content, $matches);      
?>
<?php 
//replace tags and newlines in quote text
//
for ($i=0; $i<=49; $i++ ){
	$matches[4][$i]= nl2br(trim(strip_tags($matches[4][$i])));
	$matches[4][$i] = preg_replace ('#\s+#' , ' ' , $matches[4][$i]); 
	$matches[4][$i] = preg_replace ('#<br /> <br />#' , '<br>' , $matches[4][$i]); 
}
if ($debug == 'true'){
	print_r($matches);
	echo date("D, d M Y G:i:s",mktime($matches[3][$i]));
}
else {
echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
?>
<rss version="2.0">

  <channel>

    <title>german-bash.org</title>
    <link>http://german-bash.org/</link>
	<description>krasse Zitate aus IRC und IM</description>
	<language>de</language>
<?php
	for ($i=0; $i<=49; $i++ ){
		echo '<item>'. "\n";
		echo '<title>Zitat Nummer: ' . $matches[2][$i] .' (' .date("d.m.Y",strtotime($matches[3][$i])).')'. '</title>' . "\n";
		echo '<link>http://german-bash.org/'. $matches[2][$i]  . '</link>' . "\n";
		echo '<description><![CDATA[' . $matches[4][$i] . ']]></description>'. "\n";
		echo '<pubDate>'. date("D, d M Y G:i:s",strtotime($matches[3][$i])) . '</pubDate>'. "\n";
		echo '<guid>'. md5($matches[4][$i]) .'</guid>'. "\n";
		echo '</item>' . "\n";
	}
?>
</channel>
</rss>
<?php
}
?>
