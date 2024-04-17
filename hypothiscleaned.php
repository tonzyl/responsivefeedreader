<?php
$token = 'your hypothes.is api token';

$formurl = 'https://hypothes.is/api/annotations';


$formproperties = array (
	'uri' => $annouri,
	'document' => array ('title' => [$annodoc]),
	'text' => $annotext,
	'tags' => $cats,
	);
	
$jsondata = json_encode($formproperties);

$headers = [
'Accept: application/vnd.hypothesis.v1+json',
'Content-type: application/json',
'Authorization: Bearer '.$token
];

$formOptions = array(
    'http' => array(
        'header'  => $headers,
        'method'  => 'POST',
        'content' => $jsondata
    )
);

$context = stream_context_create($formOptions);
$resp = file_get_contents($formurl, false, $context);

/* this only posts a single annotation as page note, not an annotation of a highlight. */
?>