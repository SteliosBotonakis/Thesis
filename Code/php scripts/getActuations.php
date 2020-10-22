<?php

// get the q parameter from URL
$q = $_REQUEST["q"];
  $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "1026",
  CURLOPT_URL => "http://147.27.60.182:1026/v2/entities/?type=Actuation&limit=200",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Host: 147.27.60.182:1026"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $resp = json_decode($response, true);
  foreach($resp as $actuation)
  {
    if (array_key_exists('hasFeatureOfInterest', $actuation) && basename($actuation['hasFeatureOfInterest']['value']['object']) == $q ) {
      if (array_key_exists('actuationEnabled', $actuation)  && $actuation['actuationEnabled']['value']) == "1") {
        echo '<p>' . $actuation['id']  . '</p>';
      }
      //print_r($actuation);
    }
  }
  //echo $response;
}
?>
