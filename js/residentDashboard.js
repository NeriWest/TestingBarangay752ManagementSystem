
const apiKey = "5bd20162fbec2779a808e116cf3ae2ca";
const apiUrl = "https://api.openweathermap.org/data/2.5/weather?units=metric&q=Manila";

const weatherIcon = document.querySelector(".weather-icon");

async function checkWeather() {
    const response = await fetch(apiUrl + `&appid=${apiKey}`);
    var data = await response.json();

    console.log(data);
    document.querySelector(".city").innerHTML = data.name + ", Philippines";
    document.querySelector(".temp").innerHTML = Math.round(data.main.temp) + "°C";

    if (weatherIcon) {
        if (data.weather[0].main === "Clouds") {
            weatherIcon.className = "weather-icon fa-solid fa-cloud";
        } else if (data.weather[0].main === "Rain") {
            weatherIcon.className = "weather-icon fa-solid fa-cloud-rain";
        } else if (data.weather[0].main === "Clear") {
            weatherIcon.className = "weather-icon fa-solid fa-sun";
        } else if (data.weather[0].main === "Snow") {
            weatherIcon.className = "weather-icon fa-solid fa-snowflake";
        } else {
            weatherIcon.className = "weather-icon fa-solid fa-cloud";
        }
    } else {
        console.error("Weather icon element not found!");
    }
}
checkWeather();

/**
* @param {Date} date
*/
const timeElem = document.querySelector(".time");
const dayElem = document.querySelector(".day");
const dateElem = document.querySelector(".date");

function formatTime(date) {
    const hours12 = date.getHours() % 12 || 12;
    const minutes = date.getMinutes();
    const isAm = date.getHours() < 12;

    return `${hours12.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")} ${isAm ? "AM" : "PM"}`;
}
/**
* @param {Date} date
*/
function formatDay(date) {
    const DAYS = ["SUNDAY","MONDAY","TUESDAY","WEDNESDAY","THURSDAY","FRIDAY","SATURDAY"];

    return `${DAYS[date.getDay()]}`;
}
        /**
* @param {Date} date
*/
function formatDate(date) {
    const MONTHS = ["JANUARY","FEBRUARY","MARCH","APRIL","MAY","JUNE","JULY","AUGUST","SEPTEMBER","OCTOBER","NOVEMBER","DECEMBER"];

    return `${MONTHS[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
}

setInterval(() => {
    const now = new Date();

    timeElem.textContent = formatTime(now);
    dateElem.textContent = formatDate(now);
    dayElem.textContent = formatDay(now);

}, 200);







