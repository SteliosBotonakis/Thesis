<?php
$q = $_REQUEST["q"];
echo $q . "<br>";
$s = "<http://example.org/data/$q>";
$query = "PREFIX sosa: <http://www.w3.org/ns/sosa/> PREFIX ex: <http://example.org/data/>
PREFIX tourism: <http://sensormeasurement.appspot.com/ont/m3/tourism#>
SELECT ?o ?s WHERE {" . $s . " tourism:hasWeather ?o. ?o tourism:isRecommendation ?s}";
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://147.27.60.182:8080/jena-examples-0.0.1-SNAPSHOT/?thequery=" . urlencode($query),
  CURLOPT_PORT => "8080",
  //CURLOPT_URL => "http://147.27.60.182:8080/jena-examples-0.0.1-SNAPSHOT/?thequery=PREFIX%20sosa:%20%3Chttp://www.w3.org/ns/sosa/%3E%20PREFIX%20ex:%20%3Chttp://example.org/data/%3E%20PREFIX%20geo:%20%3Chttp://www.w3.org/2003/01/geo/wgs84_pos%23%3E%09%0ASELECT%20?s%20%20?o%20%20WHERE%20%7B%3Chttp://example.org/data/Rethymno%3E%20geo:isLocationOf%20?o%7D",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Accept: */*",
    "Cache-Control: no-cache",
    "Connection: keep-alive",
    "Host: 147.27.60.182:8080",
    "Postman-Token: 61dbf144-1c87-4b03-8e59-0abd00adf579,c1038510-096d-4c01-9856-4a125c13bd2f",
    "User-Agent: PostmanRuntime/7.15.0",
    "accept-encoding: gzip, deflate",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $resp = json_decode($response, true);
  foreach ($resp["results"]["bindings"] as $i) {
    echo $i["o"]["value"] . " recommendation " . $i["s"]["value"] . "<br>";
  }
}



?>
