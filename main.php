<?php
// Name: Bikash Shrestha,  University ID: 2407649

header("Access-Control-Allow-Origin: *"); //Controls which domain are allowed to access the resources and * means any domain allowed
header("Content-Type: application/json"); //Change content type text/html to application/json. Now the content of the response is in JSON format

include("database.php");
include("fetch.php");

//Geting the name of the city from the url
if (isset($_GET["q"])) {
    $city_name = $_GET["q"];
} else {
    echo '{"Error": "City Name Not Found"}';
    exit;
}

//Assigning the variables for both table
$current_table = "current_weather_data";
$past_table = "past_weather_data";


if ($city_name == "Shimoga") {

    $allPastData = get_weather_data($mysqli, $past_table, $city_name); //Gets all data from the past_weather_data table of the database

    //Getting the past data of the city
    if (isset($_GET["history"])) {
        echo json_encode($allPastData);
        exit;  //Stop the execution from here
    }

    //Counting the number of index of Associative Array built from the database data
    if (count($allPastData) == 0) {
        $existingData = null;
    } else {
        $lastIndex = count($allPastData) - 1; //Finding what the last index is
        $existingData = $allPastData[$lastIndex]; //Assigning the value of only the last index of the database
    }

    $time = 0;
    if (isset($existingData["timestamp"])) {
        $time = $existingData["timestamp"]; //Taking UNIX timestamp from the database of the last index
    }

    $currentTime = time(); //Gets the current UNIX timestamp
    // 1 day = 60 * 60 * 24 = 86400 seconds
    if ($currentTime - $time > 86400) {
        $newData = fetch_weather_data($city_name); //Fetching new data from the OpenWeather API after 1 day 

        if ($newData) {
            $result = insert_weather_data($mysqli,$past_table, $newData); //Inserting the new data into the past_weather_data
            if(!$result){
                echo '{"Error": "Data could not be inserted"}';
                exit;
            }
        } else {
            echo '{"Error": "New Data could not be fetched"}';
            exit;
        }
    }
}

$allCurrentData = get_weather_data($mysqli, $current_table, $city_name); //Gets all data from the current_weather_data table of the database

 //Counting the number of index of Associative Array built from the database data
if (count($allCurrentData) == 0) {
    $existingData = null;
} else {
    $lastIndex = count($allCurrentData) - 1; // Finding what the last index is
    $existingData = $allCurrentData[$lastIndex]; // Assigning the value of only the last index of the database
}

//Check whether exisiting data is null or not
if ($existingData) {

    $time = 0;
    if (isset($existingData["timestamp"])) {
        $time = $existingData["timestamp"]; //Taking UNIX timestamp from the database of the last index
    }

    $currentTime = time(); //Gets the current UNIX timestamp
    if ($currentTime - $time > 3600) {
        $newData = fetch_weather_data($city_name); //Fetching new data from OpenWeather API after 1 hour
        
        if ($newData) {
            $result = insert_weather_data($mysqli, $current_table, $newData); //Inserting new data into the current_weather_data
            if ($result) {
                //Changing the data coming from the OpenWeather API into database format through associative array (Or We can again fetch from the database after the database value is inserted and echo that value by using the same process as above)

                $databaseFormatData = databaseFormatData($newData);

                echo json_encode($databaseFormatData); // convert the associative array into a JSON string 
            } else {
                echo '{"Error": "Data could not be inserted"}';
                exit;
            }
        } else {
            echo '{"Error": "New Data could not be fetched"}';
            exit;
        }
    } else {
        //Data coming from the database
        echo json_encode($existingData); //Convert Associative Array into JSON file
        exit;
    }
} else {
    $newData = fetch_weather_data($city_name);
    if ($newData) {
        $result = insert_weather_data($mysqli, $current_table, $newData); //Inserting new data into the current_weather_data
        if ($result) {
            //Changing the data coming from the OpenWeather API into database format through associative array
            $databaseFormatData = databaseFormatData($newData);

            echo json_encode($databaseFormatData); // converting the associative array into a JSON string 
        } else {
            echo '{"Error": "Data could not be inserted"}';
            exit;
        }
    } else {
        echo '{"Error": "New Data could not be fetched"}';
        exit;
    }
}

?>