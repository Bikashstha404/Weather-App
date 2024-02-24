<?php
// Name: Bikash Shrestha,  University ID: 2407649

function fetch_weather_data($city_name)
{   
    $API_KEY = "5872be66452cabe6665e3480d7e88526";
    $url = "https://api.openweathermap.org/data/2.5/weather?q=$city_name&appid=$API_KEY";
    

    //file_get_contents function in PHP is used to read the entire contents of a file into a string. It can also read internet data if we gave it internet url
    $stringData = file_get_contents($url); //Retreive data from the open weather API into a string
    
    //json_decode() decodes the JSON string and convert into a PHP Associative Araay
    $data = json_decode($stringData, true); //If true returns associative array when false returns an object

    return $data;
}

?>