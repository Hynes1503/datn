<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function createBuilding()
    {
        return view('admin.buildings.create');
    }

    public function storeBuilding(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name',
            'code' => 'required|string|max:50|unique:buildings,code',
            'description' => 'nullable|string',
        ]);

        Building::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Thêm tòa thành công!');
    }

    public function createFloor()
    {
        $buildings = Building::orderBy('name')->get();
        return view('admin.floors.create', compact('buildings'));
    }

    public function storeFloor(Request $request)
    {
        $request->validate([
            'building_id'   => 'required|exists:buildings,id',
            'floor_number'  => 'required|integer',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        Floor::create([
            'building_id'  => $request->building_id,
            'floor_number' => $request->floor_number,
            'name'         => $request->name,
            'description'  => $request->description,
        ]);

        return redirect()->back()->with('success', 'Thêm tầng thành công!');
    }

    public function createRoom()
    {
        $floors = Floor::with('building')->orderBy('name')->get();
        return view('admin.rooms.create', compact('floors'));
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'floor_id'     => 'required|exists:floors,id',
            'room_number'  => 'required|string|max:50',
            'name'         => 'required|string|max:255',
            'capacity'     => 'nullable|integer',
            'type'         => 'nullable|string|max:100',
            'description'  => 'nullable|string',
        ]);

        Room::create([
            'floor_id'    => $request->floor_id,
            'room_number' => $request->room_number,
            'name'        => $request->name,
            'capacity'    => $request->capacity,
            'type'        => $request->type,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Thêm phòng thành công!');
    }

    public function getFloors($buildingId)
    {
        $floors = Floor::where('building_id', $buildingId)->orderBy('floor_number')->get();
        return response()->json($floors);
    }
}
