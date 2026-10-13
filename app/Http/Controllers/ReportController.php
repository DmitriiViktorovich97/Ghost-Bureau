<?php

namespace App\Http\Controllers;

use App\Models\Ghost;
use App\Models\Location;

class ReportController extends Controller
{
    public function index()
    {
        $ghosts    = Ghost::with("location")->get();
        $locations = Location::withCount("ghosts")->get();

        $total      = $ghosts->count();
        $assigned   = $ghosts
            ->whereNotNull("location_id")->count();
        $unassigned = $ghosts
            ->filter(fn($g) => $g->location_id === null && !$g->isExpired())
            ->count();
        $expired    = $ghosts
            ->filter(fn($g) => $g->isExpired())->count();

        $problemGhosts = $ghosts
            ->filter(fn($g) => $g->location_id === null || ($g->match_score !== null && $g->match_score < 70))
            ->sortBy("match_score");

        $crowded = $locations->filter(fn($l) => $l->ghosts_count > $l->capacity);
        $free    = $locations->filter(fn($l) => $l->ghosts_count === $l->capacity);

        return view("reports.index", compact(
            "total", "assigned", "unassigned", "expired",
            "problemGhosts", "crowded", "free", "locations"
        ));
    }
}
