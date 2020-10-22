<?php
  $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "1026",
  CURLOPT_URL => "http://147.27.60.182:1026/v2/entities/?type=Home",
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
  echo '<select id="selectHome" class="select-css">';
  foreach($resp as $home)
  {
    echo '<option value=' . $home['id'] . '>' . $home['id'] . '</option>';
  }
  echo '</select>';
  //echo $response;
}
?>
