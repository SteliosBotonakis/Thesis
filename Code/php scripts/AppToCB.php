<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $request_body = file_get_contents('php://input');
  $phpobj=json_decode($request_body,true);
  $message="";
  ////////////////////////////////////////////////////////////////////////////////////////////

      $message='{
                    "type": "application",
                    "id": "'.$phpobj['appname'].'",
                    "scope": {
                      "value": "'.$phpobj['App_scope'].'",
                      "type": "Text"
                    }

                }';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://147.27.60.182:1026/v2/entities?options=keyValues");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $message );

    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    $result = curl_exec($ch);
    curl_close($ch);


}

?>
