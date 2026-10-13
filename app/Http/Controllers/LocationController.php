<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::query()->withCount("ghosts")->get();
        return view("locations.index", compact("locations"));
    }

    public function create()
    {
        return view("locations.create");
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            "name"        => "required|string|max:255",
            "type"        => "required|string|max:100",
            "capacity"    => "required|integer|min:1|max:999",
            "lighting"    => "required|in:dark,dim,bright",
            "noise_level" => "required|in:quiet,moderate,loud",
            "humidity"    => "required|in:dry,normal,damp",
            "description" => "nullable|string",
        ]);
        $data["has_people"] = $request->boolean("has_people");
        $data["has_attic"] = $request->boolean("has_attic");
        $data["has_mirrors"] = $request->boolean("has_mirrors");
        Location::query()->create($data);

        return redirect()->route("locations.index")->with("success", "Место добавлено.");
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();
        return redirect()->route("locations.index")->with("success", "Место удалено.");
    }
}
