<?php
/*
 * Server-Load RSS Feed V1.00 
 * By Markus Junginger, http://jars.de
 * modified by Markus Knigge for the windows sidebar server gadget
 */
date_default_timezone_set('America/Los_Angeles');
header("Content-Type: application/rss+xml"); 
$MJ_LINK='http://'.$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"];
?>
<rss version="2.0">
  <channel>
    <title><?php echo $_SERVER['HTTP_HOST']; ?> Server Load</title>
    <link><?php echo $MJ_LINK ?></link>
    <description>Server Load</description>
    <pubDate><?php echo date("r"); ?></pubDate>
    <?php
    if (function_exists('sys_getloadavg')) {
    	$loadArray = sys_getloadavg();
    	//$load= "Load: ".$loadArray[0]."/".$loadArray[1]."/".$loadArray[2];
	$minute[0] = "Average load past 1 minute";
	$minute[1] = "Average load past 5 minutes";
	$minute[2] = "Average load past 15 minutes";
    } else {
    	$load=@file_get_contents('/proc/loadavg');
    }
    if(!$loadArray) {
      $load= 'Sorry, no load average available for your server';
      $info = 'PHP >= 5.1.3 or access to /proc/loadavg is required.';
    } 
    for ($i=0;$i<3;$i++):?>
	    <item>
		    <title><?php echo number_format(round($loadArray[$i],2),2);?></title>
		    <description><![CDATA[<?php echo $minute[$i];?>]]></description>
		    <pubDate><?php echo date("r"); ?></pubDate>
	      	    <link><?php echo $MJ_LINK ?></link>
	      	    <guid><?php echo $_SERVER['REQUEST_URI']; ?></guid>
	    </item>
    <? 
    endfor;
    ?>
  </channel>
</rss>
