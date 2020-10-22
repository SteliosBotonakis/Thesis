<?php
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $request_body = file_get_contents('php://input');
   $phpobj=json_decode($request_body,true);
   $appname=$phpobj[0]["appname"];
   $request_body=addslashes($request_body);
   $message="'$request_body'";
   //echo $message
   $random=rand();
   $test1='{
     "id": "91ad451.f6e52b8",
     "label": "'.$appname.'",
     "nodes": [ ],
     "configs": [

       {
           "id": "d35866e6.40251'.$random.'",
           "type": "function",
           "z": "1323372c.c65249",
           "name": "Functionality",
           "func": "msg.headers = {};msg.payload='.$message.';return msg;",
           "outputs": 1,
           "noerr": 0,
           "x": 410,
            "y": 200,
           "wires": [
               [
                   "8ef3242d.f1b5c8'.$random.'"
               ]
           ]
       },
       {
           "id": "8ef3242d.f1b5c8'.$random.'",
           "type": "http request",
           "z": "1323372c.c65249",
           "name": "Calculations",
           "method": "POST",
           "ret": "obj",
           "url": "http://localhost/calculations.php",
           "tls": "",
           "proxy": "",
            "authType": "basic",
            "x": 590,
            "y": 200,
           "wires": [
               [
                   "784c5a17.4f59b4'.$random.'"
               ]
           ]
       },
       {
           "id": "eb58b28.6b271d'.$random.'",
           "type": "http in",
            "z": "1323372c.c65249",
            "name": "Endpoint",
           "url": "/'.$appname.'",
           "method": "get",
           "upload": false,
           "swaggerDoc": "",
            "x": 180,
            "y": 200,
           "wires": [
               [
                   "d35866e6.40251'.$random.'"
               ]
           ]
       },
       {
           "id": "784c5a17.4f59b4'.$random.'",
           "type": "http response",
           "z": "1323372c.c65249",
            "name": "Response",
            "statusCode": "",
            "headers": {},
            "x": 790,
            "y": 200,
           "wires": []
       }
   ]
 } ';
   $ch=curl_init("http://147.27.60.65:1880/flow");
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json","X-Auth-token: thisismagickeyfornodered","Node-RED-Deployment-Type: flows"));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt( $ch, CURLOPT_POSTFIELDS, $test1 );

   curl_setopt($ch, CURLOPT_HEADER,true);
   # Send request.
   $result = curl_exec($ch);
   curl_close($ch);
   echo $result;

}


?>
