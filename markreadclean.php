<?php
/* dit script stuurt een POST for mark all read naar microsub server */
$api_key = "YOUR API KEY";/* hash v user en v wachtwoord dus vervang bij wijziging ww */
$apiUrl = 'https://yourfreshrssurl/fresh/api/fever.php';

if (isset($_POST['id'])) {
    $groupid = $_POST['id'];
} else {
    $groupid = '0'; //default is alles gelezen
}

// Create the request payload
$requestData = array(
    'api_key' => $api_key,
//    'api_user' => $apiUser,
    'mark' => 'group',
    'as' => 'read',
    'id' => $groupid // 0 betekent hier alle groepen
);

// Send the request to the FreshRSS Fever API
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
return $response;


?>
