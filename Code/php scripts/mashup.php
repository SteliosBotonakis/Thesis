<?php
$ch=curl_init("http://147.27.60.182:1026/v2/entities?type=application");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch,CURLOPT_HTTPHEADER,array();
$existingapps = curl_exec($ch);
curl_close($ch);
?>
<!DOCTYPE HTML>
<html>
<head>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script>
  function showSensors(){
  	var option = document.getElementById("selectCity").value;
  	console.log(option);
  	document.getElementById("sensorsInCity").innerHTML = "";
  	var xhttp;
  	xhttp = new XMLHttpRequest();
  	xhttp.onreadystatechange = function() {
  		if (this.readyState == 4 && this.status == 200) {
  			document.getElementById("sensorsInCity").innerHTML = this.responseText;
  		}
  	};
  	xhttp.open("GET", "sparql.php?q="+option, true);
  	xhttp.send();
  }
  </script>
  <script>
  document.addEventListener("DOMContentLoaded", function() {
  	var xhttp;
  	xhttp = new XMLHttpRequest();
  	xhttp.onreadystatechange = function() {
  		if (this.readyState == 4 && this.status == 200) {
  			document.getElementById("locations").innerHTML = this.responseText;
  		}
  	};
  	xhttp.open("GET", "showLocations.php", true);
  	xhttp.send();

  });
  document.addEventListener("DOMContentLoaded", function() {
  	var xhttp;
  	xhttp = new XMLHttpRequest();
  	xhttp.onreadystatechange = function() {
  		if (this.readyState == 4 && this.status == 200) {
  			document.getElementById("homes").innerHTML = this.responseText;
  		}
  	};
  	xhttp.open("GET", "showHomes.php", true);
  	xhttp.send();

  });
  </script>
  <script>
  var sel = 0;
  $(document).ready(function () {
    $("#App_scope").change(function () {
        var val = $(this).val();
        if (val == "weather") {
          sel = 0;
          $("#weather_div").css("display", "flex");
          $("#home_div").css("display", "none");
          $(".action").css("display", "none");
          $("#act_button").css("display", "none");
          $(".attributes").html("<form><input type='checkbox' class='check' id='temperature' name='temperature' value='temperature'><label for='temperature'>Atmosphere temperature</label><input type='checkbox' class='check' id='humidity' name='humidity' value='humidity'><label for='humidity'>Atmosphere humidity</label><input type='checkbox' class='check' id='windSpeed' name='windSpeed' value='windSpeed'><label for='windSpeed'>Wind speed</label><input type='checkbox' class='check' id='cloud' name='cloud' value='cloud'><label for='cloud'>Cloud coverage</label><input type='checkbox' class='check' id='precipitation' name='precipitation' value='precipitation'><label for='precipitation'>Precipitation</label><input type='checkbox' class='check' id='pressure' name='pressure' value='pressure'><label for='pressure'>Atmospheric pressure</label></form>");
        } else if (val == "home") {
          sel = 1;
          $("#home_div").css("display", "flex");
          $("#weather_div").css("display", "none");
          $(".action").css("display", "block");
          $("#act_button").css("display", "block");
          $(".attributes").html("<form><input type='checkbox' class='check' id='rtemperature' name='rtemperature' value='temperature'><label for='rtemperature'>Room temperature</label><input type='checkbox' class='check' id='rhumidity' name='rhumidity' value='humidity'><label for='rhumidity'>Room humidity</label><input type='checkbox' class='check' id='luminocity' name='luminocity' value='luminocity'><label for='luminocity'>Luminocity</label><input type='checkbox' class='check' id='presence' name='presence' value='presence'><label for='presence'>Presence</label></form>");
        }
    });
  });

  function addMashup() {
    if (confirm("add this mashup?")) {
        var chosenDomains=new Array();
          var cboxes_domain = document.getElementsByClassName('rec_check');
          var len = cboxes_domain.length;
          for (var i=0; i<len; i++) {
            if(cboxes_domain[i].checked)
              chosenDomains.push(cboxes_domain[i].value);
          }

          var chosenAttrs=new Array();
          var cboxes_attrs = document.getElementsByClassName('check');;
          var len = cboxes_attrs.length;
          for (var i=0; i<len; i++) {
            if(cboxes_attrs[i].checked)
              chosenAttrs.push(cboxes_attrs[i].value);
          }

          if (sel == 0) {
            console.log(sel);
            var city=document.getElementById('selectCity').value;
            var func = document.getElementById("function1").value;
          } else {
            console.log(sel);
            var city=document.getElementById('selectHome').value;
            var func = document.getElementById("function2").value;
          }
          var person = {attributes:chosenAttrs, city:city, domains:chosenDomains, func:func};

          Array2MushUp.push(person);
          var objJON=JSON.stringify(Array2MushUp);
          console.log(objJON);
      }
  }

  function createCBappInfo(appid) {
    var Appinfo=new Array();
    var app_name=document.getElementById('appname').value;
    //var description=document.getElementById('description').value;
    var App_scope=document.getElementById('App_scope').value;
    //description=description.replace(/\n/g, '')
    Appinfo={appname:app_name,appid:appid,App_scope:App_scope};
    Appinfo=JSON.stringify(Appinfo);
    var payload;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        payload = this.responseText;
        alert(Appinfo);
      }
    };
    xhttp.open("POST", "AppToCB.php", true);
    xhttp.send(Appinfo);
  }

  </script>
  <script>
  function getActuations() {
    var str=document.getElementById('selectHome').value;
  	var xhttp;
  	if (str.length == 0) {
  		document.getElementById("actuations").innerHTML = "";
  		return;
  	}
  	xhttp = new XMLHttpRequest();
  	xhttp.onreadystatechange = function() {
  		if (this.readyState == 4 && this.status == 200) {
  			document.getElementById("actuations").innerHTML = this.responseText;
  		}
  	};
  	xhttp.open("GET", "getActuations.php?q="+str, true);
  	xhttp.send();
  }
  </script>
  <script>


  function calculations() {

  var app_name=document.getElementById('appname').value;
  var scope=document.getElementById("App_scope").value;
  var existingapps=<?php echo $existingapps;?>;
  existingapps=JSON.stringify(existingapps);
  if(app_name==""){
      alert("app name cant be blank");
  }
  else if(existingapps.search(app_name)!=-1){
      alert("name already in use");
  }
  else if(scope==""){
      alert("Assign the application to a specific scope");
  }
  else{

        if (confirm("if u want to genarate this app press ok")) {

            var f_array = {appname:app_name,appscope:scope,info:Array2MushUp};

            Array2MushUpGenerate.push(f_array);
            var obj_JON=JSON.stringify(Array2MushUpGenerate);
            alert("going to send : "+obj_JON);
            console.log(obj_JON);
              var payload;
              var xhttp = new XMLHttpRequest();
              xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                  payload = this.responseText;
                  alert(payload);
                  payload = payload.substring(payload.indexOf("{"));
                  payload=JSON.parse(payload);
                  createCBappInfo(payload["id"]);
                }
              };
              xhttp.open("POST", "deploy.php", true);
              xhttp.send(obj_JON);
        }
      }
  }

  </script>
  <script>
  var validattributes="";
  var Array2MushUp=new Array();
  var Array2MushUpGenerate=new Array();

  </script>
  <style>
    .container {
      width:60%;
      margin: auto;
      display: flex;
    }
    .left {
      float:left;
    }
    .center {
        display: block;
        margin:0 auto;
        float:left;
    }
    .right {
      display: block;
      margin:0 auto;
      float:left;
    }

    .action {
      display: none;
      margin: auto;
    }

    .action p {
      text-align: center;
    }

    #actuations {
      display: block;
      margin: auto;
    }

    body {
      background-color: #e6e6e6;
      padding: 0;
      margin: 0;
    }

    .sidebar {
      margin-top: -22px;
      background-color: #6666ff;
      color: white;
      text-align: center;
    }
    input[type=checkbox] + label {
    display: block;
    margin: 0.2em;
    cursor: pointer;
    padding: 0.2em;
  }

  input[type=checkbox] {
    display: none;
  }

  input[type=checkbox] + label:before {
    content: "\2714";
    border: 0.1em solid #000;
    border-radius: 0.2em;
    display: inline-block;
    width: 1em;
    height: 1em;
    padding-left: 0.2em;
    padding-bottom: 0.3em;
    margin-right: 0.2em;
    vertical-align: bottom;
    color: transparent;
    transition: .2s;
  }

  input[type=checkbox] + label:active:before {
    transform: scale(0);
  }

  input[type=checkbox]:checked + label:before {
    background-color: MediumSeaGreen;
    border-color: MediumSeaGreen;
    color: #fff;
  }

  input[type=checkbox]:disabled + label:before {
    transform: scale(1);
    border-color: #aaa;
  }

  input[type=checkbox]:checked:disabled + label:before {
    transform: scale(1);
    background-color: #bfb;
    border-color: #bfb;
  }


.select-css {
    display: block;
    font-size: 16px;
    font-family: sans-serif;
    font-weight: 700;
    color: #444;
    line-height: 1.3;
    padding: .6em 1.4em .5em .8em;
    width: 100%;
    max-width: 326px;
    box-sizing: border-box;
    margin: 0;
    border: 1px solid #aaa;
    box-shadow: 0 1px 0 1px rgba(0,0,0,.04);
    border-radius: .5em;
    -moz-appearance: none;
    -webkit-appearance: none;
    appearance: none;
    background-color: #fff;
    background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'),
      linear-gradient(to bottom, #ffffff 0%,#e5e5e5 100%);
    background-repeat: no-repeat, repeat;
    background-position: right .7em top 50%, 0 0;
    background-size: .65em auto, 100%;
}
.select-css::-ms-expand {
    display: none;
}
.select-css:hover {
    border-color: #888;
}
.select-css:focus {
    border-color: #aaa;
    box-shadow: 0 0 1px 3px rgba(59, 153, 252, .7);
    box-shadow: 0 0 0 3px -moz-mac-focusring;
    color: #222;
    outline: none;
}
.select-css option {
    font-weight:normal;
}

input[type=text] {
    border: 5px solid white;
    -webkit-box-shadow:
      inset 0 0 8px  rgba(0,0,0,0.1),
            0 0 16px rgba(0,0,0,0.1);
    -moz-box-shadow:
      inset 0 0 8px  rgba(0,0,0,0.1),
            0 0 16px rgba(0,0,0,0.1);
    box-shadow:
      inset 0 0 8px  rgba(0,0,0,0.1),
            0 0 16px rgba(0,0,0,0.1);
    padding: 15px;
    background: rgba(255,255,255,0.5);
    margin: 0 0 10px 0;
}

  </style>
  </head>
  <body>

    <div class="sidebar">
      <h1>Mashup Service</h1>
      <h2>Create application<h2>
    </div>

    <div class=right_side>
    <p style="text-align: center">Select Application domain:</p>
    <select style="margin: auto;display: block;" id="App_scope" class="select-css" name="App_scope">
      <option value="" selected disabled>Select Domain</option>
      <option value="weather">City Weather and Recommendations</option>
      <option value="home">Home Automations</option>
    </select>

    <br><br>

    <div id="p" style="text-align: center" >Application name </div>
    <input id="appname" type="text" name="appname" placeholder="e.g: myapp"style="width:30%;margin : 0 auto;display: block;"><br><br>

    <div  id="weather_div" class="container">
      <div class="left">
        <div class="city">
          <p>Select City:</p>
          <div id="locations">
          </div>
        </div>
      </div>


      <div class="center" id="center1">
        <p>Select attributes:</p>
        <div class="attributes">

        </div>
      </div>


      <div class="right">
        <p>Select recommandation to apply semantics:</p>
        <div id="recommendation">
          <form>
            <input type='checkbox' class='rec_check' id="Accomodation" name="Accomodation" value='Accomodation'> <label for="Accomodation">Accomodation</label>
            <input type='checkbox' class='rec_check' id="Activity" name="Activity" value='Activity'><label for="Activity">Activity</label>
            <input type='checkbox' class='rec_check' id="Garment" name="Garment" value='Garment'><label for="Garment">Garment</label>
            <input type='checkbox' class='rec_check' id="MartialArt" name="MartialArt" value='MartialArt'><label for="MartialArt">MartialArt</label>
            <input type='checkbox' class='rec_check' id="Nightlife" name="Nightlife" value='Nightlife'><label for="Nightlife">Nightlife</label>
            <input type='checkbox' class='rec_check' id="Place" name="Place" value='Place'><label for="Place">Place</label>
            <input type='checkbox' class='rec_check' id="Relaxation" name="Relaxation" value='Relaxation'><label for="Relaxation">Relaxation</label>
            <input type='checkbox' class='rec_check' id="Road" name="Road" value='Road'><label for="Road">Road</label>
            <input type='checkbox' class='rec_check' id="Shoe" name="Shoe" value='Shoe'><label for="Shoe">Shoe</label>
            <input type='checkbox' class='rec_check' id="Sport" name="Sport" value='Sport'><label for="Sport">Sport</label>
            <input type='checkbox' class='rec_check' id="Transport" name="Transport" value='Transport'><label for="Transport">Transport</label>
            <input type='checkbox' class='rec_check' id="WaterSport" name="WaterSport" value='WaterSport'><label for="WaterSport">WaterSport</label>
            <input type='checkbox' class='rec_check' id="Weather" name="Weather" value='Weather'><label for="Weather">Weather</label>
          </form>
        </div>
      </div>

      <div style="display:block;margin:0 auto;float:left;">
        <select id="function1" class="select-css">
          <option value="none" selected disabled>Select calculation to execute</option>
          <option  value='MAX_24hr'>24h max value for every hour</option>
          <option  value='MΙΝ_24hr'>24h min value for every hour</option>
          <option  value='ΑVERAGE_24hr'>24h average value for every hour</option>
          <option  value='MAX_7D'>7d max value for every day</option>
          <option  value='MIN_7D'>7d min value for every day</option>
          <option  value='ΑVERAGE_7D'>7d average value for every day</option>
          <option  value='LIVE'>value right now</option>
        </select>
      </div>
  </div>

  <div  id="home_div" class="container" style="display: none">
    <div class="left">
      <div class="home">
        <p>Select Home:</p>
        <div id="homes">
        </div>
      </div>

    </div>

    <div class="center" id="center2">
      <p>Select attributes:</p>
      <div class="attributes">

      </div>
    </div>

    <div class="right">
      <select id="function2" class="select-css">
        <option value="none" selected disabled>Select calculation to execute</option>
        <option  value='MAX_24hr'>24h max value for every hour</option>
        <option  value='MΙΝ_24hr'>24h min value for every hour</option>
        <option  value='ΑVERAGE_24hr'>24h average value for every hour</option>
        <option  value='MAX_7D'>7d max value for every day</option>
        <option  value='MIN_7D'>7d min value for every day</option>
        <option  value='ΑVERAGE_7D'>7d average value for every day</option>
        <option  value='LIVE'>value right now</option>
      </select>
    </div>
  </div>
  <br><br>
  <input id="act_button" type="button"  value="See actuations" onclick="getActuations();" style="width:20%;height:46px;margin:auto;display:none"/><br><br>
  <div class="action">
    <p style="font-weight:bold;">Actuations enabled in building:</p>
    <div id="actuations">

    </div>
  </div>
  <br><br>
  <input type="button"  value=" Add Mashups" onclick="addMashup();" style="width:20%;height:46px;margin:auto;display:block"/><br><br>
  <input type="button" onclick="calculations();" value=" Generate Application" style="width:20%;height:46px;margin:auto;display:block"/>

</div>
</body>
</html>
