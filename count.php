<?php
function call_header(){
	echo 'header';
	//header("X-Powered-By: PHP/7.0.1");
	//header("X-Powered-By: ASP.NET", false);
	header("X-Powered-By: ASP.NET");
	//header("Server: Microsoft-IIS/7.5");
	header_remove('Server');
	header("Via: 1.1 TMGSERVER");
	//header("Access-Control-Allow-Origin: *");
}
$hostname = 'localhost';

$xml_data = file_get_contents("http://$hostname:8086/connectioncounts");
$doc      = new DOMDocument();
$doc->loadXML($xml_data);
$wms              = $doc->getElementsByTagName('WowzaStreamingEngine');
$wmstotalactive   = $wms->item(0)->getElementsByTagName("ConnectionsCurrent")->item(0)->nodeValue;
$wmstotaloutbytes = $wms->item(0)->getElementsByTagName("MessagesOutBytesRate")->item(0)->nodeValue;
$wmstotaloutbits  = $wmstotaloutbytes * '8';
echo "<center><b>Hostname:</b> $hostname<br /></center><hr> <b>Server Total Active Connections:</b> $wmstotalactive<br /> <b>Total Outbound bitrate:</b>  $wmstotaloutbits<br /><hr>";
$emailBody = "Hostname: $hostname \n\n Server Total Active Connections: $wmstotalactive \n Total Outbound bitrate:  $wmstotaloutbits\n\n";
$wmsapp    = $doc->getElementsByTagName('Application');
$wmscnt    = $wmsapp->length;
echo "<center>Applications</center>";
$emailBody .= "Applications";

for ($idx = 0; $idx < $wmscnt; $idx++) {
    $appname     = $wmsapp->item($idx)->getElementsByTagName("Name")->item(0)->nodeValue;
    $appccount   = $wmsapp->item($idx)->getElementsByTagName("ConnectionsCurrent")->item(0)->nodeValue;
    $appoutbytes = $wmsapp->item($idx)->getElementsByTagName("MessagesOutBytesRate")->item(0)->nodeValue;
    $appoutbits  = $appoutbytes * '8';
    echo "<hr><b>Application Name:</b> $appname<br /><b> Active Connections:</b> $appccount<br /> <b>Application Bits Out:</b> $appoutbits 
<br />";
    $emailBody .= "\n Application Name: $appname \n Active Connections: $appccount \n Application Bits Out: $appoutbits \n";
}

echo "<hr><center>Streams</center>";
$emailBody .= "\n Streams";
$wmsast  = $doc->getElementsByTagName('Stream');
$wmsasct = $wmsast->length;

for ($sidx = 0; $sidx < $wmsasct; $sidx++) {
    $strname     = $wmsast->item($sidx)->getElementsByTagName("Name")->item(0)->nodeValue;
    $strcuperino = $wmsast->item($sidx)->getElementsByTagName("SessionsCupertino")->item(0)->nodeValue;
    $strctot     = $wmsast->item($sidx)->getElementsByTagName("SessionsTotal")->item(0)->nodeValue;
    echo "<hr><b>Stream URL:</b> $strname <br /> <b>Connections to Stream:</b> $strctot<br />";
    $emailBody .= "\nStream URL: $strname \n Cupertino: $strcuperino \n Connections to Stream: $strctot\n";
}

$emailTo      = "dainv@inet.vn";
$current_date = date("Y-m-d");
$emailSubject = "Live Stream Update: " . $current_date;
$emailFrom    = "myheartwillgoon91@gmail.com";
$emailHeader  = "From: $emailFrom\n" . "MIME-Version: 1.0\n" . "Content-type: text/plain; charset=\"UTF-8\"\n" . "Content-transfer-encoding: 8bit\n";
//mail($emailTo, $emailSubject, $emailBody, $emailHeader);
call_header();
?>
