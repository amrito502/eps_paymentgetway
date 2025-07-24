<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Track My Location</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>User Location:</h2>

    <p><strong>Full Address:</strong> <span id="full_address">Fetching...</span></p>
    <p><strong>Road:</strong> <span id="road">Fetching...</span></p>
    <p><strong>City:</strong> <span id="city">Fetching...</span></p>
    <p><strong>District:</strong> <span id="district">Fetching...</span></p>
    <p><strong>Country:</strong> <span id="country">Fetching...</span></p>

    <script>
        window.onload = function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(success, error);
            } else {
                alert("Geolocation not supported.");
            }

            function success(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                fetch('{{ route('reverse.geocode') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ latitude, longitude })
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('full_address').innerText = data.full_address;
                    document.getElementById('road').innerText = data.road ?? 'N/A';
                    document.getElementById('city').innerText = data.city ?? 'N/A';
                    document.getElementById('district').innerText = data.district ?? 'N/A';
                    document.getElementById('country').innerText = data.country ?? 'N/A';
                });
            }

            function error() {
                alert("Location permission denied or unavailable.");
            }
        }
    </script>
</body>
</html>
