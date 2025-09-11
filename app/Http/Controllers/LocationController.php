<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Building;
class LocationController extends Controller
{
    public function getFloors($buildingId)
    {
        return response()->json(Floor::where('building_id', $buildingId)->get());
    }

    public function getRooms($floorId)
    {
        return response()->json(Room::where('floor_id', $floorId)->get());
    }
}
