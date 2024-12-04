<?php
if (isset($_GET['logout'])) {
    header('Location: login.php'); // Redirect to login
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css">
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #4C6444;
      color: white;
      text-align: center;
      padding: 1rem;
    }

    #search-section {
      padding: 1rem;
      background-color: #f0f200;
      color: white;
      text-align: center;
    }

    #search-section input {
      width: 60%;
      padding: 0.5rem;
      margin-right: 0.5rem;
    }

    #search-section button {
      padding: 0.5rem;
      background-color: #4C6444;
      color: white;
      border: none;
      cursor: pointer;
    }

    #container {
      display: flex;
      flex-wrap: wrap;
      margin: 1rem;
    }

    #map {
      flex: 1;
      min-width: 800px;
      height: 700px;
    }

    #info {
      flex: 1 1 35%;
      margin-left: 1rem;
      padding: 1rem;
      background-color: #f9f9f9;
      border: 1px solid #ddd;
    }

    #info h2 {
      color: #4C6444;
    }

    #attractions-info {
      max-height: 150px;
      overflow-y: scroll;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #fff;
      margin-top: 20px;
    }

    #news-info {
      max-height: 150px;
      overflow-y: scroll;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #fff;
      margin-top: 20px;
    }

    #attractions-info ul,
    #news-info ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    #attractions-info li,
    #news-info li {
      padding: 8px;
      border-bottom: 1px solid #f0f0f0;
    }

    #news-info li a {
      color: #4CAF50;
      text-decoration: none;
    }

    #news-info li a:hover {
      text-decoration: underline;
    }

    .logout-button {
      display: block;
      margin: 1rem auto;
      padding: 0.5rem 2rem;
      background-color: #f0f200;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 1rem;
    }

    .logout-button a {
      color: white;
      text-decoration: none;
    }
  </style>
  
 <script>
    function playAudio(fileName) {
        const audio = new Audio(fileName);
        audio.play();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const h1Element = document.querySelector('h1');
        const h2Element = document.querySelector('h2');

        h1Element.addEventListener('click', () => playAudio('Yati.mp3'));
        h2Element.addEventListener('click', () => playAudio('atahala.mp3'));
    });
  </script>
    
</head>
<body>
  <header>
    <h1>Welcome to Gabai</h1>
    <h2>Your Friendly Route Guide: Paving the Way to Your Destination!</h2>
  </header>

  <div id="search-section">
    <input type="text" id="destination" placeholder="Enter destination">
    <button id="search-button">Search</button>
  </div>

  <div id="container">
    <div id="map"></div>
    <div id="info">
      <h2>Weather Information</h2>
      <div id="weather-info">Enter a destination to see weather information.</div>
      <h2>Nearby Attractions</h2>
      <div id="attractions-info">Attractions will be listed here.</div>
      <h2>Related News</h2>
      <div id="news-info">News articles will be listed here.</div>
    </div>
  </div>

  <button class="logout-button" id="logout-btn">
    <a href="dashboard.php?logout=true">Log Out</a>
  </button>

  <script>
    const weatherApiKey = 'bc19641048e154108af78620b5d1deca';
    const tripMapApiKey = '5ae2e3f221c38a28845f05b63322de3141c0a154f0d820b16bbfaf7b';
    const newsApiKey = 'de98c59db3374c56aa26d58c7e705a3a';

    let userCoordinates;
    let routeControl; // Global variable to store the route control

    const map = L.map('map').setView([10.6698, 122.9488], 13); // Default location: Bacolod City
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          userCoordinates = [position.coords.latitude, position.coords.longitude];
          map.setView(userCoordinates, 16);
          L.marker(userCoordinates)
            .addTo(map)
            .bindPopup('<b>You are here</b>')
            .openPopup();
        },
        () => {
          alert('Unable to retrieve your location.');
        }
      );
    } else {
      alert('Geolocation is not supported by your browser.');
    }

    document.getElementById('search-button').addEventListener('click', async () => {
      const destination = document.getElementById('destination').value;
      if (!destination || !userCoordinates) {
        alert('Please enter a destination and allow location access.');
        return;
      }

      try {
        const geoResponse = await fetch(`https://api.opentripmap.com/0.1/en/places/geoname?name=${destination}&apikey=${tripMapApiKey}`);
        const geoData = await geoResponse.json();
        const { lat, lon } = geoData;

        // Reset the map view to the destination
        map.setView([lat, lon], 13);
        L.marker([lat, lon]).addTo(map).bindPopup(destination).openPopup();

        // Calculate the route if it exists
        if (routeControl) {
          map.removeControl(routeControl); // Remove any existing route before creating a new one
        }

        routeControl = L.Routing.control({
          waypoints: [
            L.latLng(userCoordinates[0], userCoordinates[1]), // Starting point
            L.latLng(lat, lon) // Destination
          ],
          routeWhileDragging: true
        }).addTo(map);

        const weatherResponse = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${weatherApiKey}&units=metric`);
        const weatherData = await weatherResponse.json();
        document.getElementById('weather-info').innerHTML = `
          <p><strong>Location:</strong> ${destination}</p>
          <p><strong>Temperature:</strong> ${weatherData.main.temp}°C</p>
          <p><strong>Condition:</strong> ${weatherData.weather[0].description}</p>
        `;

        const attractionsResponse = await fetch(`https://api.opentripmap.com/0.1/en/places/radius?radius=5000&lon=${lon}&lat=${lat}&apikey=${tripMapApiKey}`);
        const attractionsData = await attractionsResponse.json();
        const attractions = attractionsData.features
          .map(attraction => `<li>${attraction.properties.name}</li>`)
          .join('');
        document.getElementById('attractions-info').innerHTML = attractions
          ? `<ul>${attractions}</ul>`
          : 'No attractions found near this location.';

        const newsResponse = await fetch(`https://newsapi.org/v2/everything?q=${destination}&apiKey=${newsApiKey}`);
        const newsData = await newsResponse.json();
        const newsArticles = newsData.articles
          .map(article => `<li><a href="${article.url}" target="_blank">${article.title}</a></li>`)
          .join('');
        document.getElementById('news-info').innerHTML = newsArticles
          ? `<ul>${newsArticles}</ul>`
          : 'No news articles found for this location.';
      } catch (error) {
        console.error('Error fetching data:', error);
        alert('An error occurred. Please try again later.');
      }
    });
  </script>
</body>
</html>
