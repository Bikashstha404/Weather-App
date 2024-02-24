// Name: Bikash Shrestha,  University ID: 2407649
//document.cookie = "__test=c01eb0612ba7ca7bf50a959571606f7b; expires=Sat, 19 Mar 2025 05:21:50 GMT; path=/";

API_KEY = "5872be66452cabe6665e3480d7e88526";
default_city = "Shimoga"
let searchedCity;
async function cityNameSearched(city_name) {
    try {
        const storedData = localStorage.getItem(city_name);
        if (storedData) {
            const data = JSON.parse(storedData);
            weatherData(data);
            buttons(city_name);
            clearLocalStorage();
        } else {
            const response = await fetch(`https://bikashshresthaweatherapp.000webhostapp.com/main.php?q=${city_name}`) //Fetching data from the database
            const data = await response.json();

            // Storing Data in Local Storage
            localStorage.setItem(`${city_name}`, JSON.stringify(data));
            //Calling the function to insert datas on HTML
            weatherData(data);
            buttons(city_name);
            clearLocalStorage();
        }
    } catch (error) {
        document.querySelector(`.search-validation`).innerHTML = "Invalid City";
        setTimeout(() => {
            document.querySelector(`.search-validation`).innerHTML = "";
        }, 3000)
    }

}

function weatherData(data) {

    //Assigning the data fetched from the url
    let weatherDescription = data.weather_description;
    let iconCode = data.weather_icon;
    let weatherIcons = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
    let temperature = data.temperature;
    let dt = data.timestamp;
    let locationName = data.city;
    let locationCountry = data.country;
    let pressure = data.pressure;
    let windspeed = data.windspeed;
    let humidity = data.humidity;


    //Changing the data of the html with the assigned data
    document.querySelector('.weather-state').innerHTML = weatherDescription;

    document.querySelector(`.weather-icons`).src = weatherIcons;

    temperature = (temperature - 273.15).toFixed(1);
    document.querySelector(`.temperature`).innerHTML = temperature + `\u00B0C`;

    let day = new Date(dt * 1000).toLocaleDateString('en-US', {
        weekday: 'long'
    })
    document.querySelector(`.day`).innerHTML = day;

    let date = new Date(dt * 1000).toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })

    document.querySelector(`.date`).innerHTML = date;

    document.querySelector(`.location-name`).innerHTML = locationName + ` (${locationCountry})`;

    document.querySelector(`.pressure`).innerHTML = `${pressure} hPa`;

    document.querySelector(`.windspeed`).innerHTML = `${windspeed} m/s`;

    document.querySelector(`.humidity`).innerHTML = `${humidity} %`;

    document.querySelector(`.past_heading`).innerHTML = ` Weather Data of ${locationName} of Last 7 Days`;
}

async function pastWeatherData(city_name) {

    const response = await fetch(`https://bikashshresthaweatherapp.000webhostapp.com/main.php?q=${city_name}&history=true`); //Fetching data from the database
    const data = await response.json();

    //Making the table empty everything user click the view past weather
    document.querySelector(`.past_weather_table tbody`).innerHTML = "";

    for (let i = 0; i < 7; i++) {

        //Retriving data from the last index since the data saves from the first day 
        let lastIndex = data.length - 1;
        let weatherData = data[lastIndex - i];

        //Assigning the value of fetched data
        let iconCode = weatherData.weather_icon;
        let weatherIcons = `https://openweathermap.org/img/wn/${iconCode}.png`;
        let temperature = (weatherData.temperature - 273.15).toFixed(1);

        //Creating a tr from javascript to creat as many as we like using loop
        let tr = document.createElement("tr");

        //Adding td on the new element tr and giving them the value
        tr.innerHTML = `
        <td >${weatherData.date}</td>
        <td>${weatherData.weather_description}</td>
        <td><img src="${weatherIcons}" alt="Weather icons"></td>
        <td>${temperature}\u00B0C</td>
        <td>${weatherData.pressure}hPa</td>
        <td>${weatherData.windspeed}m/s</td>
        <td>${weatherData.humidity}%</td>
        `;
        //Adding the new elemebt on the table in tbody tag
        document.querySelector(`.past_weather_table tbody`).appendChild(tr);
    }
    document.querySelector(`.past_weather_data`).style.display = "block";
}

function buttons(city_name){
      //If it is not default city then don't show the past weather button
      if (city_name == "Shimoga") {
        document.querySelector(`.show_past_weather`).style.display = "block";
    } else {
        document.querySelector(`.show_past_weather`).style.display = "none";
    }

    //Assigning the value written on the search box
    document.querySelector(`.search-box`).addEventListener("input", (event) => {
        searchedCity = event.target.value;
    })

    //If the user pressed enter after writing the city name then display that city data and empty the search box
    document.querySelector(`.search-box`).addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.target.value = "";
            searchedCity = searchedCity[0].toUpperCase() + searchedCity.slice(1)
            cityNameSearched(searchedCity);
        }
    })

    //If the user pressed submit button after writing the city name then display that city data and empty the search box
    document.querySelector(`.search-button`).addEventListener("click", (event) => {
        searchedCity = searchedCity[0].toUpperCase() + searchedCity.slice(1)

        cityNameSearched(searchedCity);

        let searchBox = document.querySelector(`.search-box`)
        if (searchBox) {
            searchBox.value = "";
        }
    })

    //If the user press the view past weather data then show the past weather data feched from database 
    document.querySelector(`.show_past_weather`).addEventListener("click", (event) => {
        pastWeatherData(city_name);
    })

    //If the user press the close symbol then close the past weather data
    document.querySelector(`.close_button`).addEventListener("click", (event) => {
        document.querySelector(`.past_weather_data`).style.display = "none";
    })
}

cityNameSearched(default_city);

function clearLocalStorage(){
var data = localStorage.getItem(default_city)
var jsonData = JSON.parse(data)
localStorageTime = jsonData.timestamp
const realTime = Math.floor(Date.now() / 1000); 
if(realTime-localStorageTime > 3600){
    localStorage.clear();
}
}
