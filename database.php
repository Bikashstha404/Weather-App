<?php
// Name: Bikash Shrestha,  University ID: 2407649

$mysqli = connect_server("localhost", "id21870736_bikashshrestha", "Bikash@99"); 

$database = "CREATE DATABASE IF NOT EXISTS id21870736_bikash";
mysqli_query($mysqli, $database);
mysqli_select_db($mysqli, "id21870736_bikash");

$create_table1 = "CREATE TABLE IF NOT EXISTS current_weather_data(weather_id int AUTO_INCREMENT PRIMARY KEY, city varchar(50), country varchar(50), date varchar(50),weather_description varchar(50),weather_icon varchar(10), temperature varchar(10), pressure varchar(10), windspeed varchar(10), humidity varchar(10), timestamp int)";
mysqli_query($mysqli, $create_table1);

$create_table2 = "CREATE TABLE IF NOT EXISTS past_weather_data(weather_id int AUTO_INCREMENT PRIMARY KEY, city varchar(50), country varchar(50), date varchar(50),weather_description varchar(50),weather_icon varchar(10), temperature varchar(10), pressure varchar(10), windspeed varchar(10), humidity varchar(10), timestamp int)";
mysqli_query($mysqli, $create_table2);


function connect_server($server, $username, $password)
{
    $mysqli = mysqli_connect($server, $username, $password); //mysql_connect --> establish a connection to MYSQL server

    //Connect_errno is used to check if there was error during the connect process and is used to retrieve the error number associated with the most recent connection error.
    if ($mysqli->connect_errno) {
        echo '{"error": "Connection Failed!"}';
        exit;
    }

    return $mysqli;
}

function get_weather_data($mysqli, $table, $city_name){

    $result = mysqli_query($mysqli, "SELECT * FROM $table WHERE city = '$city_name' order by timestamp");
    // $result =  $mysqli -> query('SELECT * FROM weather_data WHERE city= "'.$city_name.'" ORDER BY time;'); (Can be done this way too)
    if($result){
        // fetch_all retrieves all rows from the result set and resturns them as a array
        //MYSQLI_ASSOC indicates the resulting array should be an associative array
        $data = $result -> fetch_all(MYSQLI_ASSOC);
        return $data;

    }else{
        return null;
    }
}

function insert_weather_data($mysqli, $table, $weather_data) {
    $city = $weather_data["name"];
    $time = $weather_data["dt"];
    $date = new DateTime("@$time");
    $formattedDate = $date->format('D, j M Y');
    $country = $weather_data["sys"]["country"];
    $weather_description = $weather_data["weather"][0]["description"];
    $weather_icon = $weather_data["weather"][0]["icon"];
    $temperature = $weather_data["main"]["temp"];
    $pressure = $weather_data["main"]["pressure"];
    $windspeed = $weather_data["wind"]["speed"];
    $humidity = $weather_data["main"]["humidity"];
    $timestamp = $weather_data["dt"];

    $result = mysqli_query($mysqli, "INSERT INTO $table(city,country, date, weather_description,weather_icon, temperature, pressure, windspeed, humidity, timestamp)VALUES('$city','$country', '$formattedDate','$weather_description','$weather_icon', '$temperature','$pressure','$windspeed', '$humidity', '$timestamp')");
  
    return $result;
}


//Changing the data coming from the OpenWeather API into associative array and asssigning them to database heading
function databaseFormatData($newData)
{
    $databaseFormatData = ["city" => $newData["name"], "country" => $newData["sys"]["country"], "date" => $newData["dt"], "weather_description" => $newData["weather"][0]["description"], "weather_icon" => $newData["weather"][0]["icon"], "temperature" => $newData["main"]["temp"], "pressure" => $newData["main"]["pressure"], "windspeed" => $newData["wind"]["speed"], "humidity" => $newData["main"]["humidity"], "timestamp" => $newData["dt"]];

    return $databaseFormatData;
}

?>