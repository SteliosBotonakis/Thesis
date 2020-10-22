<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$request_body = file_get_contents('php://input');
	$payload=json_decode($request_body,true);
	////////////////////////////////////////////////////////////////////colors
	function random_color_part() {
	    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	}
	function random_color() {
	    return random_color_part() . random_color_part() . random_color_part();
	}
	////////////////////////////////////////////////////////////////////functions
	$globalmessage="";
	$globaloperations=0;
	$firstResult = [];
	$secondResult = [];
	$thirdResult = [];
	$tagmessage=array();
	$appname=$payload[0]["appname"];
  $temperature = 0;
  $humidity = 0;
	$luminocity = 0;
  $windSpeed = 0;
  $cloud = 0;
  $precipitation = 0;
  $pressure = 0;

	$attributes = [];

  $attrs = $payload[0]["info"][0]["attributes"];

	if ($payload[0]["appscope"] == "weather") {
		foreach ($attrs as $i) {
			if ($i == "temperature") {
				array_push($attributes, "tourism:hasTemperature");
			} elseif ($i == "humidity") {
				array_push($attributes, "tourism:hasHumidity");
			} elseif ($i == "windSpeed") {
				array_push($attributes, "tourism:hasWindSpeed");
			} elseif ($i == "cloud") {
				array_push($attributes, "tourism:hasCloudCoverage");
			} elseif ($i == "precipitation") {
				array_push($attributes, "tourism:hasPrecipitation");
			} elseif ($i == "pressure") {
				array_push($attributes, "tourism:hasPressure");
			}
		}
	} elseif ($payload[0]["appscope"] == "home") {
		foreach ($attrs as $i) {
			if ($i == "temperature") {
				array_push($attributes, "home:hasTemperatureState");
			} elseif ($i == "humidity") {
				array_push($attributes, "home:hasHumidityState");
			} elseif ($i == "luminocity") {
				array_push($attributes, "home:hasLuminocityState");
			} elseif ($i == "presence") {
				array_push($attributes, "home:hasPresenceState");
			}
		}
	}

  $city = $payload[0]["info"][0]["city"];
  $domain = $payload[0]["info"][0]["domains"];

	$query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX sosa: <http://www.w3.org/ns/sosa/>	PREFIX ex: <http://example.org/data/> PREFIX tourism: <http://sensormeasurement.appspot.com/ont/m3/tourism#> SELECT ?o WHERE { ex:$city sosa:assumes ?o}";

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "http://147.27.60.182:8080/jena-examples-0.0.1-SNAPSHOT/select?thequery=" . urlencode($query),
		CURLOPT_PORT => "8080",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
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
		foreach ($resp["results"]["bindings"] as $y) {
			array_push($GLOBALS['thirdResult'], $y["o"]["value"]);
			//echo $y["p"]["value"] . "<br>";
		}
	}



  function queryWeather($attributes, $domain, $city){
		$s = "ex:".$city;
		//$firstResult = [];
		//$secondResult = [];
		foreach ($attributes as $x) {
			$query = "PREFIX sosa: <http://www.w3.org/ns/sosa/>	PREFIX ex: <http://example.org/data/> PREFIX tourism: <http://sensormeasurement.appspot.com/ont/m3/tourism#> SELECT ?p WHERE { $s $x ?p}";

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "http://147.27.60.182:8080/jena-examples-0.0.1-SNAPSHOT/select?thequery=" . urlencode($query),
				CURLOPT_PORT => "8080",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
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
					array_push($GLOBALS['firstResult'], $i["p"]["value"]);
					//echo $i["p"]["value"] . "<br>";
				}
			}
		}
		foreach ($GLOBALS['firstResult'] as $i) {
			foreach ($domain as $k) {
				$query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX sosa: <http://www.w3.org/ns/sosa/>	PREFIX ex: <http://example.org/data/> PREFIX tourism: <http://sensormeasurement.appspot.com/ont/m3/tourism#> SELECT ?p WHERE { <$i> tourism:isRecommendation ?p. ?p rdf:type tourism:$k}";

				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "http://147.27.60.182:8080/jena-examples-0.0.1-SNAPSHOT/select?thequery=" . urlencode($query),
					CURLOPT_PORT => "8080",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
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
					foreach ($resp["results"]["bindings"] as $y) {
						array_push($GLOBALS['secondResult'], $y["p"]["value"]);
						//echo $y["p"]["value"] . "<br>";
					}
				}
			}
		}

		//print_r($firstResult);
		//print_r($secondResult);
  }

	function queryHome($attributes, $domain, $city){
		$s = "ex:".$city;
		//$firstResult = [];
		//$secondResult = [];
		foreach ($attributes as $x) {
			$query = "PREFIX sosa: <http://www.w3.org/ns/sosa/>	PREFIX ex: <http://example.org/data/> PREFIX home: <http://sensormeasurement.appspot.com/ont/m3/home#> SELECT ?p ?i WHERE { $s $x ?p}";

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "http://147.27.60.182:8080/jena-examples-0.0.1-SNAPSHOT/select?thequery=" . urlencode($query),
				CURLOPT_PORT => "8080",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
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
					array_push($GLOBALS['firstResult'], $i["p"]["value"]);
					//echo $i["p"]["value"] . "<br>";
				}
				$GLOBALS['firstResult'] = array_unique($GLOBALS['firstResult']);
			}
		}
		foreach ($GLOBALS['firstResult'] as $i) {
			$query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX sosa: <http://www.w3.org/ns/sosa/>	PREFIX ex: <http://example.org/data/> PREFIX home: <http://sensormeasurement.appspot.com/ont/m3/home#> SELECT ?p WHERE { <$i> home:hasRecommendation ?p}";

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "http://147.27.60.182:8080/jena-examples-0.0.1-SNAPSHOT/select?thequery=" . urlencode($query),
				CURLOPT_PORT => "8080",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
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
				foreach ($resp["results"]["bindings"] as $y) {
					array_push($GLOBALS['secondResult'], $y["p"]["value"]);
					//echo $y["p"]["value"] . "<br>";
				}
			}
		}
  }

	function calculate_H_AVERAGE_7D($attributes, $domain, $city){
		$chart_counter = 0;
		$ids = array();
		foreach ($attributes as $attr) {
			$chart_counter++;
			$today = date("Y-m-d");
			$stop_date = date('Y-m-d', strtotime($today . ' -7 day'));


			if ($attr == "temperature") {
				$temperature = 1;
				array_push($ids, $city.ucfirst($attr)."Observation");
			} elseif ($attr == "humidity") {
				$humidity = 1;
				array_push($ids, $city.ucfirst($attr)."Observation");
			} elseif ($attr == "windSpeed") {
				$windSpeed = 1;
				array_push($ids, $city.ucfirst($attr)."Observation");
			} elseif ($attr == "cloud") {
					$cloud = 1;
					array_push($ids, $city.ucfirst($attr)."Observation");
			} elseif ($attr == "precipitation") {
					$precipitation = 1;
					array_push($ids, $city.ucfirst($attr)."Observation");
			} elseif ($attr == "pressure") {
					$pressure = 1;
					array_push($ids, $city.ucfirst($attr)."Observation");
			} elseif ($attr == "luminocity") {
					$luminocity = 1;
					array_push($ids, $city.ucfirst($attr)."Observation");
			}
		}

			$message="['HOUR','$attr'],";
			$array = array();
			$dates_array = array();
			$dates_count = array();
			for ($x = 0; $x < count($ids); $x++) {
				$ch = curl_init( "http://147.27.60.79:8666/STH/v1/contextEntities/type/Observation/id/$ids[$x]/attributes/hasSimpleResult?aggrMethod=sum&aggrPeriod=day&dateFrom=$stop_date" );
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: default",'Fiware-ServicePath: /'));
				$result = curl_exec($ch);
				//echo $result;
				curl_close($ch);
				$payload=json_decode($result,true);
				$array[$ids[$x]]=$payload;
			}
			for ($x = 0; $x < count($ids); $x++) {
						$current_Sensor=$ids[$x];
						$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
						for($k=0;$k<$origin_count;$k++){
							$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
							$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

							for ($y = 0; $y < count($points); $y++) {
								$YMD_date = substr($_id_date,0,10);
								$offset=$points[$y]['offset'];
								$offset--;
								$YMD_date = date('Y-m-d', strtotime($YMD_date ."+$offset day"));
								$samples=$points[$y]['samples'];
								$sum=$points[$y]['sum'];
								if (empty($results[$YMD_date])){
										$results[$YMD_date]=($sum/$samples);
								}else{
										$results[$YMD_date]=$results[$YMD_date]+($sum/$samples);
								}
							}
						}
			}
			foreach ($results as $YMD_date=>$value) {
							$results[$YMD_date]=$value/count($ids);
							$message=$message."['$YMD_date',$results[$YMD_date]],";
			}
			//print_r($results);
			$color=random_color();

			$message=substr($message, 0, -1);
			for ($count = 0; $count < $chart_counter; $count++) {
          $GLOBALS['globalmessage']='<script type="text/javascript">
            google.charts.load(\'current\', {\'packages\':[\'corechart\']});
            google.charts.setOnLoadCallback(drawChart'.$GLOBALS['globaloperations'].');
            function drawChart'.$GLOBALS['globaloperations'].'() {
                    var data = google.visualization.arrayToDataTable(['.$message.']);
                    var options = {
                            vAxis: {
                                    title: \''.$city.'\'
                            },
                            series: {
                                    0: { color: \'#'.$color.'\' }
                            },
                            title: \'Average '.$attr.' for the last 7 Days\',
                            curveType: \'function\',
                    };

                    var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

                    chart.draw(data, options);
                    }
    								</script>';
                            $GLOBALS['globaloperations']++;
          }

	  }


		function calculate_H_MAX_7D($attributes, $domain, $city){
			$chart_counter = 0;
			$ids = array();
		 foreach ($attributes as $attr) {
			 $chart_counter++;
			 $today = date("Y-m-d");
			 $stop_date = date('Y-m-d', strtotime($today . ' -7 day'));


			 if ($attr == "temperature") {
				 $temperature = 1;
				 array_push($ids, $city.ucfirst($attr)."Observation");
			 } elseif ($attr == "humidity") {
				 $humidity = 1;
				 array_push($ids, $city.ucfirst($attr)."Observation");
			 } elseif ($attr == "windSpeed") {
				 $windSpeed = 1;
				 array_push($ids, $city.ucfirst($attr)."Observation");
			 } elseif ($attr == "cloud") {
					 $cloud = 1;
					 array_push($ids, $city.ucfirst($attr)."Observation");
			 } elseif ($attr == "precipitation") {
					 $precipitation = 1;
					 array_push($ids, $city.ucfirst($attr)."Observation");
			 } elseif ($attr == "pressure") {
					 $pressure = 1;
					 array_push($ids, $city.ucfirst($attr)."Observation");
			 } elseif ($attr == "luminocity") {
					 $luminocity = 1;
					 array_push($ids, $city.ucfirst($attr)."Observation");
			 }
		 }

			 $message="['HOUR','$attr'],";
			 $array = array();
			 $dates_array = array();
			 $dates_count = array();
					for ($x = 0; $x < count($ids); $x++) {
						$ch = curl_init( "http://147.27.60.79:8666/STH/v1/contextEntities/type/Observation/id/$ids[$x]/attributes/hasSimpleResult?aggrMethod=max&aggrPeriod=day&dateFrom=$stop_date" );
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: default",'Fiware-ServicePath: /'));
						$result = curl_exec($ch);
						//echo $result;
						curl_close($ch);
						$payload=json_decode($result,true);
						$array[$ids[$x]]=$payload;
					}
					for ($x = 0; $x < count($ids); $x++) {
								$current_Sensor=$ids[$x];
								$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
								for($k=0;$k<$origin_count;$k++){
									$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
									$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

									for ($y = 0; $y < count($points); $y++) {
										$YMD_date = substr($_id_date,0,10);
										$offset=$points[$y]['offset'];
										$offset--;
										$YMD_date = date('Y-m-d', strtotime($YMD_date ."+$offset day"));
										$samples=$points[$y]['samples'];
										$max=$points[$y]['max'];
										if (empty($results[$YMD_date])){
												$results[$YMD_date]=$max;
										}else{
													if ($max>=$results[$YMD_date]){
															$results[$YMD_date]=$max;
													}
										}
									}
								}
					}
					foreach ($results as $YMD_date=>$value) {

									$message=$message."['$YMD_date',$results[$YMD_date]],";
					}
					$color=random_color();

					$message=substr($message, 0, -1);
					for ($count = 0; $count < $chart_counter; $count++) {
              $GLOBALS['globalmessage']='<script type="text/javascript">
                                google.charts.load(\'current\', {\'packages\':[\'corechart\']});
                                google.charts.setOnLoadCallback(drawChart'.$GLOBALS['globaloperations'].');
                                function drawChart'.$GLOBALS['globaloperations'].'() {
                                        var data = google.visualization.arrayToDataTable(['.$message.']);
                                        var options = {
                                                vAxis: {
                                                        title: \''.$city.'\'
                                                },
                                                series: {
                                                        0: { color: \'#'.$color.'\' }
                                                },
                                                title: \'Max '.$attr.' for the last 7 Days\',
                                                curveType: \'function\',
                                        };

                                        var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

                                        chart.draw(data, options);
                                        }
                        								</script>';
                                                $GLOBALS['globaloperations']++;
            }

			  }

	function calculate_H_MIN_7D($attributes, $domain, $city){
		$chart_counter = 0;
		$ids = array();
	 foreach ($attributes as $attr) {
		 $chart_counter++;
		 $today = date("Y-m-d");
		 $stop_date = date('Y-m-d', strtotime($today . ' -7 day'));


		 if ($attr == "temperature") {
			 $temperature = 1;
			 array_push($ids, $city.ucfirst($attr)."Observation");
		 } elseif ($attr == "humidity") {
			 $humidity = 1;
			 array_push($ids, $city.ucfirst($attr)."Observation");
		 } elseif ($attr == "windSpeed") {
			 $windSpeed = 1;
			 array_push($ids, $city.ucfirst($attr)."Observation");
		 } elseif ($attr == "cloud") {
				 $cloud = 1;
				 array_push($ids, $city.ucfirst($attr)."Observation");
		 } elseif ($attr == "precipitation") {
				 $precipitation = 1;
				 array_push($ids, $city.ucfirst($attr)."Observation");
		 } elseif ($attr == "pressure") {
				 $pressure = 1;
				 array_push($ids, $city.ucfirst($attr)."Observation");
		 } elseif ($attr == "luminocity") {
				 $luminocity = 1;
				 array_push($ids, $city.ucfirst($attr)."Observation");
		 }
	 }

		 $message="['HOUR','$attr'],";
		 $array = array();
		 $dates_array = array();
		 $dates_count = array();
						for ($x = 0; $x < count($ids); $x++) {
							$ch = curl_init( "http://147.27.60.79:8666/STH/v1/contextEntities/type/Observation/id/$ids[$x]/attributes/hasSimpleResult?aggrMethod=min&aggrPeriod=day&dateFrom=$stop_date" );
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: default",'Fiware-ServicePath: /'));
							$result = curl_exec($ch);
							//echo $result;
							curl_close($ch);
							$payload=json_decode($result,true);
							$array[$ids[$x]]=$payload;
						}
						for ($x = 0; $x < count($ids); $x++) {
									$current_Sensor=$ids[$x];
									$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
									for($k=0;$k<$origin_count;$k++){
										$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
										$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

										for ($y = 0; $y < count($points); $y++) {
											$YMD_date = substr($_id_date,0,10);
											$offset=$points[$y]['offset'];
											$offset--;
											$YMD_date = date('Y-m-d', strtotime($YMD_date ."+$offset day"));
											$samples=$points[$y]['samples'];
											$min=$points[$y]['min'];
											if (empty($results[$YMD_date])){
													$results[$YMD_date]=$min;
											}else{
														if ($min<=$results[$YMD_date]){
																$results[$YMD_date]=$min;
														}
											}
										}
									}
						}
						foreach ($results as $YMD_date=>$value) {

										$message=$message."['$YMD_date',$results[$YMD_date]],";
						}
						//print_r($results);
						$color=random_color();

						$message=substr($message, 0, -1);
						for ($count = 0; $count < $chart_counter; $count++) {
							$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
								google.charts.load(\'current\', {\'packages\':[\'corechart\']});
								google.charts.setOnLoadCallback(drawChart'.$GLOBALS['globaloperations'].');
								function drawChart'.$GLOBALS['globaloperations'].'() {
									var data = google.visualization.arrayToDataTable(['.$message.']);
									var options = {
										vAxis: {
											title: \''.$city.'\'
										},
										series: {
											0: { color: \'#'.$color.'\' }
										},
										title: \'Minimum '.$attr.' for the last 7 Days\',
										curveType: \'function\',
									};

									var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

									chart.draw(data, options);
								}
							</script>';
							$GLOBALS['globaloperations']++;
						}
				  }


					function calculate_H_AVERAGE_24H($attributes, $domain, $city){
						$chart_counter = 0;
						$ids = array();
						foreach ($attributes as $attr) {
							$chart_counter++;
							$today = date("Y-m-d H:i:s");
							$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));


							if ($attr == "temperature") {
								$temperature = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "humidity") {
								$humidity = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "windSpeed") {
								$windSpeed = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "cloud") {
									$cloud = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "precipitation") {
									$precipitation = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "pressure") {
									$pressure = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "luminocity") {
									$luminocity = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							}
						}

							$message="['HOUR','$attr'],";
							$array = array();
							$dates_array = array();
							$dates_count = array();
						for ($x = 0; $x < count($ids); $x++) {
							$ch = curl_init( "http://147.27.60.79:8666/STH/v1/contextEntities/type/Observation/id/$ids[$x]/attributes/hasSimpleResult?aggrMethod=sum&aggrPeriod=hour&dateFrom=$stop_date" );
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: default",'Fiware-ServicePath: /'));
							$result = curl_exec($ch);
							curl_close($ch);
							$payload=json_decode($result,true);
							$array[$ids[$x]]=$payload;
						}
						for ($x = 0; $x < count($ids); $x++) {
									$current_Sensor=$ids[$x];
									$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
									for($k=0;$k<$origin_count;$k++){
										$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
										$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

										for ($y = 0; $y < count($points); $y++) {
											$YMD_date = $_id_date;
											$offset=$points[$y]['offset'];
											$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
											$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
											$samples=$points[$y]['samples'];
											if($samples>0){
												if (empty($count_contri[$YMD_date])){
														$count_contri[$YMD_date]=1;
												}else{
														$count_contri[$YMD_date]++;
												}
											}
											$sum=$points[$y]['sum'];
											if (empty($results[$YMD_date])){
													$results[$YMD_date]=($sum/$samples);
											}else{
													$results[$YMD_date]=$results[$YMD_date]+($sum/$samples);
											}
										}
									}
						}
						foreach ($results as $YMD_date=>$value) {
										$results[$YMD_date]=$value/$count_contri[$YMD_date];
										$message=$message."['$YMD_date',$results[$YMD_date]],";
						}
						//print_r($results);
						$color=random_color();
						$message=substr($message, 0, -1);
						for ($count = 0; $count < $chart_counter; $count++) {
			          $GLOBALS['globalmessage']='<script type="text/javascript">
			            google.charts.load(\'current\', {\'packages\':[\'corechart\']});
			            google.charts.setOnLoadCallback(drawChart'.$GLOBALS['globaloperations'].');
			            function drawChart'.$GLOBALS['globaloperations'].'() {
			                    var data = google.visualization.arrayToDataTable(['.$message.']);
			                    var options = {
			                            vAxis: {
			                                    title: \''.$city.'\'
			                            },
			                            series: {
			                                    0: { color: \'#'.$color.'\' }
			                            },
			                            title: \'Average '.$attr.' for the last 24 Hours\',
			                            curveType: \'function\',
			                    };

			                    var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

			                    chart.draw(data, options);
			                    }
			    								</script>';
			                            $GLOBALS['globaloperations']++;
			          }
					}


					function calculate_H_MIN_24H($attributes, $domain, $city){
						$chart_counter = 0;
						$ids = array();
						foreach ($attributes as $attr) {
							$chart_counter++;
							$today = date("Y-m-d H:i:s");
							$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));


							if ($attr == "temperature") {
								$temperature = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "humidity") {
								$humidity = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "windSpeed") {
								$windSpeed = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "cloud") {
									$cloud = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "precipitation") {
									$precipitation = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "pressure") {
									$pressure = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "luminocity") {
									$luminocity = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							}
						}

							$message="['HOUR','$attr'],";
							$array = array();
							$dates_array = array();
							$dates_count = array();
						for ($x = 0; $x < count($ids); $x++) {
							$ch = curl_init( "http://147.27.60.79:8666/STH/v1/contextEntities/type/Observation/id/$ids[$x]/attributes/hasSimpleResult?aggrMethod=min&aggrPeriod=hour&dateFrom=$stop_date" );
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: default",'Fiware-ServicePath: /'));
							$result = curl_exec($ch);
							curl_close($ch);
							$payload=json_decode($result,true);
							$array[$ids[$x]]=$payload;
						}
						for ($x = 0; $x < count($ids); $x++) {
									$current_Sensor=$ids[$x];
									$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
									for($k=0;$k<$origin_count;$k++){
										$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
										$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

										for ($y = 0; $y < count($points); $y++) {
											$YMD_date = $_id_date;
											$offset=$points[$y]['offset'];
											$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
											$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
											$samples=$points[$y]['samples'];
											$min=$points[$y]['min'];
											if (empty($results[$YMD_date])){
													$results[$YMD_date]=$min;
											}else{
														if($min<=$results[$YMD_date]){
															$results[$YMD_date]=$min;
														}
											}
										}
									}
						}
						foreach ($results as $YMD_date=>$value) {
										//$results[$YMD_date]=$value/count($ids);
										$message=$message."['$YMD_date',$results[$YMD_date]],";
						}
						//print_r($results);
						$color=random_color();

						$message=substr($message, 0, -1);
						for ($count = 0; $count < $chart_counter; $count++) {
								$GLOBALS['globalmessage']='<script type="text/javascript">
									google.charts.load(\'current\', {\'packages\':[\'corechart\']});
									google.charts.setOnLoadCallback(drawChart'.$GLOBALS['globaloperations'].');
									function drawChart'.$GLOBALS['globaloperations'].'() {
													var data = google.visualization.arrayToDataTable(['.$message.']);
													var options = {
																	vAxis: {
																					title: \''.$city.'\'
																	},
																	series: {
																					0: { color: \'#'.$color.'\' }
																	},
																	title: \'Minimum '.$attr.' for the last 24 Hours\',
																	curveType: \'function\',
													};

													var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

													chart.draw(data, options);
													}
													</script>';
																	$GLOBALS['globaloperations']++;
								}
					}

					function calculate_H_MAX_24H($attributes, $domain, $city){
						$chart_counter = 0;
						$ids = array();
						foreach ($attributes as $attr) {
							$today = date("Y-m-d H:i:s");
							$stop_date = date('Y-m-dTH:i:s', strtotime($today . ' -24 hour'));

							$chart_counter++;

							if ($attr == "temperature") {
								$temperature = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "humidity") {
								$humidity = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "windSpeed") {
								$windSpeed = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "cloud") {
									$cloud = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "precipitation") {
									$precipitation = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "pressure") {
									$pressure = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "luminocity") {
									$luminocity = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							}
						}
						//$message = array();
							$message="['HOUR','$attr'],";
							$array = array();
							$dates_array = array();
							$dates_count = array();
						for ($x = 0; $x < count($ids); $x++) {
							$ch = curl_init( "http://147.27.60.79:8666/STH/v1/contextEntities/type/Observation/id/$ids[$x]/attributes/hasSimpleResult?aggrMethod=max&aggrPeriod=hour&dateFrom=$stop_date" );
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: default",'Fiware-ServicePath: /'));
							$result = curl_exec($ch);
							curl_close($ch);
							$payload=json_decode($result,true);
							$array[$ids[$x]]=$payload;
							//$message="['HOUR','$attr'],";
						}
						for ($x = 0; $x < count($ids); $x++) {
									$current_Sensor=$ids[$x];
									$origin_count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
									for($k=0;$k<$origin_count;$k++){
										$points=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["points"];
										$_id_date=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$k]["_id"]["origin"];

										for ($y = 0; $y < count($points); $y++) {
											$YMD_date = $_id_date;
											$offset=$points[$y]['offset'];
											$T_purposes[$current_Sensor][$attr][$_id_date][$offset]=1;
											$YMD_date = date('g:i a', strtotime($YMD_date ."+$offset hour"));
											$samples=$points[$y]['samples'];
											$max=$points[$y]['max'];
											if (empty($results[$YMD_date])){
													$results[$YMD_date]=$max;
											}else{
														if($max>=$results[$YMD_date]){
															$results[$YMD_date]=$max;
														}
											}
										}
									}
						}
						foreach ($results as $YMD_date=>$value) {
										//$results[$YMD_date]=$value/count($ids);
										$message=$message."['$YMD_date',$results[$YMD_date]],";
						}
									//print_r($results);
									$color=random_color();
									$message=substr($message, 0, -1);
									for ($count = 0; $count < $chart_counter; $count++) {
										$GLOBALS['globalmessage']=$GLOBALS['globalmessage'].'<script type="text/javascript">
											google.charts.load(\'current\', {\'packages\':[\'corechart\']});
											google.charts.setOnLoadCallback(drawChart'.$GLOBALS['globaloperations'].');
											function drawChart'.$GLOBALS['globaloperations'].'() {
															var data = google.visualization.arrayToDataTable(['.$message.']);
															var options = {
																			vAxis: {
																							title: \''.$city.'\'
																			},
																			series: {
																							0: { color: \'#'.$color.'\' }
																			},
																			title: \'Maximum '.$attr.' for the last 24 Hours\',
																			curveType: \'function\',
															};

															var chart = new google.visualization.LineChart(document.getElementById('.$GLOBALS['globaloperations'].'));

															chart.draw(data, options);
															}
															</script>';
																			$GLOBALS['globaloperations']++;
													}
					}

					function calculate_LIVE($attributes, $domain, $city){
						$ids = array();
						$chart_counter = 0;
						foreach ($attributes as $attr) {
							if ($attr == "temperature") {
								$temperature = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "humidity") {
								$humidity = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "windSpeed") {
								$windSpeed = 1;
								array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "cloud") {
									$cloud = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "precipitation") {
									$precipitation = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "pressure") {
									$pressure = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							} elseif ($attr == "luminocity") {
									$luminocity = 1;
									array_push($ids, $city.ucfirst($attr)."Observation");
							}
					}
						$message="['Sensor','$attr', { role: 'style' } ],";
						$array = array();
						$dates_LIVE = array();
						for ($x = 0; $x < count($ids); $x++) {
							$ch = curl_init( "http://147.27.60.79:8666/STH/v1/contextEntities/type/Observation/id/$ids[$x]/attributes/hasSimpleResult?lastN=1");
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER,array("Fiware-Service: default",'Fiware-ServicePath: /'));
							$result = curl_exec($ch);
							//echo $result;
							curl_close($ch);
							$payload=json_decode($result,true);
							$array[$ids[$x]]=$payload;

						}
						for ($x = 0; $x < count($ids); $x++) {
									$count=count($array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"]);
									for ($y = 0; $y < $count; $y++) {
										$attrValue=$array[$ids[$x]]["contextResponses"][0]["contextElement"]["attributes"][0]["values"][$y]["attrValue"] ;
										if (!array_key_exists($rest, $dates_array)) {
												$dates_LIVE[$ids[$x]]=$attrValue;
										}

									}
						}

						foreach ($dates_LIVE as $id=>$value) {
										$color=random_color();
										$message=$message."['$id',$value,'$color'],";

						}

						$message=substr($message, 0, -1);
						for ($count = 0; $count < $chart_counter; $count++) {
							$GLOBALS['globalmessage']='<script type="text/javascript">

						    google.charts.load("current", {packages:["corechart"]});
						    google.charts.setOnLoadCallback(drawChart'.$GLOBALS['globaloperations'].');
						    function drawChart'.$GLOBALS['globaloperations'].'() {
						      var data = google.visualization.arrayToDataTable(['.$message.']);

						      var view = new google.visualization.DataView(data);
						      view.setColumns([0, 1,
						                       { calc: "stringify",
						                         sourceColumn: 1,
						                         type: "string",
						                         role: "annotation" },
						                       2]);

						      var options = {
										vAxis: {
											title: \''.$city.'\'
										},

						        title: "Maximum '.$attr.' for the last 24 Hours",
						        bar: {groupWidth: "10%"},
										colors: [\'#'.$color.'\'],
										legend: { position: "none" },

						      };
						      var chart = new google.visualization.ColumnChart(document.getElementById('.$GLOBALS['globaloperations'].'));
						      chart.draw(data, options);
						  }
						  </script>';
						  $GLOBALS['globaloperations']++;
						}
					}


	if($payload[0]["info"][0]["func"] == "ΑVERAGE_7D") {
		calculate_H_AVERAGE_7D($attrs, $domain, $city);
	} elseif ($payload[0]["info"][0]["func"] == "MAX_7D") {
		calculate_H_MAX_7D($attrs, $domain, $city);
	}	elseif ($payload[0]["info"][0]["func"] == "MIN_7D") {
		calculate_H_MIN_7D($attrs, $domain, $city);
	} elseif($payload[0]["info"][0]["func"] == "MAX_24hr"){
		calculate_H_MAX_24H($attrs, $domain, $city);
	}
	elseif($payload[0]["info"][0]["func"] == "MΙΝ_24hr"){
		calculate_H_MIN_24H($attrs, $domain, $city);
	}
	elseif($payload[0]["info"][0]["func"] == "ΑVERAGE_24hr"){
		calculate_H_AVERAGE_24H($attrs, $domain, $city);
	}
	elseif($payload[0]["info"][0]["func"] == "LIVE"){
		if ($payload[0]["appscope"] == "weather") {
			queryWeather($attributes, $domain, $city);
		} elseif ($payload[0]["appscope"] == "home") {
			queryHome($attributes, $domain, $city);
		}
	}

/////////////////////////////////////////////////////////////////////////END
/////////////////////////////////////////////////////////////////////////PAGE construct
$divs="";
for ($x = 0; $x < count($payload[0]["info"][0]["attributes"]); $x++) {
			$divs=$divs.'<div id="'.$x.'" style="width:100%; height: 800px;float:right;"></div>';
}

$weather_divs="";
foreach ($firstResult as $res) {
	if (basename($res) == "RoomTemperature") {
		$res = "http://example.org/data/NormalTemparature";
	} elseif (basename($res) == "AboveRoomTemperature") {
		$res = "http://example.org/data/AboveNormalTemparature";
	} elseif (basename($res) == "BelowRoomTemperature") {
		$res = "http://example.org/data/BelowNormalTemparature";
	}
	$weather_divs = $weather_divs . '<p>'.basename($res).'</p>';
}

$recommendation_divs="";
foreach ($secondResult as $res) {
	$recommendation_divs = $recommendation_divs . '<p>'.basename($res).'</p>';
}

$further_recommendation_divs="";
foreach ($thirdResult as $res) {
	$further_recommendation_divs = $further_recommendation_divs . '<p>'.basename($res).'</p>';
}

echo '<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
#P { text-align: center }
body,h1,h2,h3,h4,h5 {font-family: "Poppins", sans-serif}
body {font-size:16px;}
.w3-half img{margin-bottom:-6px;margin-top:16px;opacity:0.8;cursor:pointer}
.w3-half img:hover{opacity:1}
#recommendation p {margin:auto;}
</style>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

	'.$globalmessage.'
</head>
<body>
<div id="p"><h1 class="w3-xxxlarge w3-text-red"style="color: DodgerBlue!important;margin-top:80px;"><b>'.$appname.'</b></h1></div>
<div id="flexbox" style="display:flex; justify-content: space-evenly;">
	<div id="weather">
		<h1>Live state</h1>
		'.$weather_divs.'
	</div>
	<div id="recommendation">
		<h1>Recommendations based on current state</h1>
		'.$recommendation_divs.'
	</div>
	<div id="assumptions">
		<h1>Further assumptions</h1>
		'.$further_recommendation_divs.'
	</div>
</div>
<div id="kouta">
	'.$divs.'
</div>
</body>
</html>
';}


  ?>
