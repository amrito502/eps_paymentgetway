<?php
namespace App\Http\Controllers;

use App\Events\RealTimeLocation;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function showRealtime()
    {
        return view('realtime-location');
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $address = $this->getAddress($request->latitude, $request->longitude);

            $location = UserLocation::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $address
                ]
            );

            broadcast(new RealTimeLocation($location))->toOthers();

            return response()->json([
                'status' => 'success',
                'data' => $location
            ]);

        } catch (\Exception $e) {
            \Log::error('Location error: '.$e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getLocation()
    {
        $location = UserLocation::where('user_id', auth()->id())->first();
        return response()->json(['location' => $location]);
    }

    private function getAddress($lat, $lng)
    {
        $response = Http::withHeaders([
            'User-Agent' => 'YourAppName/1.0 (your@email.com)'
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'lat' => $lat,
            'lon' => $lng,
            'format' => 'json',
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch address');
        }

        return $response->json()['display_name'] ?? 'Unknown location';
    }
}
