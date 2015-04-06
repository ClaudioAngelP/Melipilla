<?php 


$url='http://sms2.cardboardfish.com:9001/HTTPSMS?';


$fields = array(
            'S' => urlencode('H'),
            'UN' => urlencode('sistemasexp1'),
            'P' => urlencode('vjf7XlZX'),
            'DA' => urlencode('56987201904'),
            'SA' => urlencode('56987201904'),
            'M' => urlencode('SALUDOS DE GIS - SEIS')
        );

$fields_string='';

//url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');


$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_VERBOSE, 1);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);


?>
