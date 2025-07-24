<!DOCTYPE html>
<html>
<head>
    <title>রিয়েলটাইম লোকেশন ট্র্যাকিং</title>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        #location-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .location-item {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #333;
        }
        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>আপনার বর্তমান অবস্থান</h1>

    <div id="location-info">
        <div class="location-item">
            <span class="label">অক্ষাংশ:</span>
            <span id="latitude">লোড হচ্ছে...</span>
        </div>
        <div class="location-item">
            <span class="label">দ্রাঘিমাংশ:</span>
            <span id="longitude">লোড হচ্ছে...</span>
        </div>
        <div class="location-item">
            <span class="label">ঠিকানা:</span>
            <span id="address">লোড হচ্ছে...</span>
        </div>
    </div>

    <div id="map"></div>

    <script>
        // Pusher initialization
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true,
            enabledTransports: ['ws', 'wss']
        });

        // Subscribe to channel
        const channel = pusher.subscribe('location.{{ auth()->id() }}');

        // Listen for location updates
        channel.bind('location.updated', function(data) {
            updateLocationUI(data.location);
        });

        // Update UI with location data
        function updateLocationUI(location) {
            if(location) {
                document.getElementById('latitude').textContent = location.latitude;
                document.getElementById('longitude').textContent = location.longitude;
                document.getElementById('address').textContent = location.address;

                // Update map if available
                if(window.map && location.latitude && location.longitude) {
                    window.map.setView([location.latitude, location.longitude], 15);
                    L.marker([location.latitude, location.longitude]).addTo(window.map)
                        .bindPopup(location.address)
                        .openPopup();
                }
            }
        }

        // Get initial location
        fetch('/get-location')
            .then(response => response.json())
            .then(data => updateLocationUI(data.location));

        // Geolocation tracking
        if (navigator.geolocation) {
            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            };

            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;

                    fetch('/track-location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ latitude, longitude })
                    })
                    .catch(error => console.error('Error:', error));
                },
                (error) => {
                    console.error('Geolocation error:', error);
                    document.getElementById('address').textContent = 'লোকেশন এক্সেস করতে অনুমতি দিন';
                },
                options
            );
        } else {
            document.getElementById('address').textContent = 'আপনার ব্রাউজার লোকেশন সাপোর্ট করে না';
        }

        // Load Leaflet map
        function loadMap() {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js';
            script.onload = function() {
                const style = document.createElement('link');
                style.rel = 'stylesheet';
                style.href = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css';
                document.head.appendChild(style);

                // Initialize map
                window.map = L.map('map').setView([23.8103, 90.4125], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(window.map);

                // Check if we have existing location to show
                fetch('/get-location')
                    .then(response => response.json())
                    .then(data => {
                        if(data.location && data.location.latitude) {
                            window.map.setView([data.location.latitude, data.location.longitude], 15);
                            L.marker([data.location.latitude, data.location.longitude])
                                .addTo(window.map)
                                .bindPopup(data.location.address)
                                .openPopup();
                        }
                    });
            };
            document.body.appendChild(script);
        }

        // Load map after page loads
        window.addEventListener('load', loadMap);
    </script>
</body>
</html>
