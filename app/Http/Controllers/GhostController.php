<?php

namespace App\Http\Controllers;

use App\Models\Ghost;
use App\Models\Location;
use App\Services\GhostMatcherService\GhostMatcherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GhostController extends Controller
{
    public function __construct(private readonly GhostMatcherService $matcher) {}

    public function index()
    {
        $ghosts = Ghost::with("location")
            ->orderBy("deadline")
            ->get();

        $stats = [
            "total"      => $ghosts
                ->count(),
            "assigned"   => $ghosts
                ->whereNotNull("location_id")->count(),
            "unassigned" => $ghosts
                ->filter(fn($g) => $g->location_id === null && !$g->isExpired())
                ->count(),
            "expired"    => $ghosts
                ->filter(fn($g) => $g->isExpired())->count(),
        ];

        return view("ghosts.index", compact("ghosts", "stats"));
    }

    public function create()
    {
        return view("ghosts.create", [
            "conditions" => Ghost::conditionLabels()
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            "name"                 => "required|string|max:255",
            "anxiety_level"        => "required|integer|min:1|max:10",
            "favorite_temperature" => "required|in:cold,neutral,warm",
            "deadline"             => "required|date",
            "special_conditions"   => "array",
            "special_conditions.*" => "string|in:" . implode(",", array_keys(Ghost::conditionLabels())),
        ]);
        $data["special_conditions"] = $data["special_conditions"] ?? [];

        $ghost = Ghost::query()->create($data);

        return redirect()->route("ghosts.show", $ghost)->with("success", "Заявка принята.");
    }

    public function show(Ghost $ghost)
    {
        $ghost->load("location");
        $candidates = $this->matcher->explain($ghost);
        $best = null;

        foreach ($candidates as $c)
        {
            if ($c["result"]["possible"])
            {
                $best = $c;
                break;
            }
        }

        return view("ghosts.show", compact("ghost", "candidates", "best"));
    }

    public function autoMatch(Ghost $ghost): RedirectResponse
    {
        $best = $this->matcher->findBest($ghost);

        if (!$best)
        {
            $ghost->update([
                "location_id"  => null,
                "match_score"  => null,
                "match_reason" => "Подходящее место не найдено"
            ]);

            return back()->with("warning", "Подходящее место не найдено — смотрите причины ниже.");
        }

        $ghost->update([
            "location_id"  => $best["location"]->id,
            "match_score"  => $best["result"]["score"],
            "match_reason" => implode("; ", $best["result"]["reasons"]) ?: "Базовое соответствие",
        ]);

        return back()->with("success", "Переселено в «{$best["location"]->name}» (оценка {$best["result"]["score"]}).");
    }

    public function autoMatchAll(): RedirectResponse
    {
        $assigned = $failed = 0;

        foreach (Ghost::query()->orderBy("deadline")->get() as $ghost)
        {
            $best = $this->matcher->findBest($ghost);

            if ($best)
            {
                $ghost->update([
                    "location_id"  => $best["location"]->id,
                    "match_score"  => $best["result"]["score"],
                    "match_reason" => implode("; ", $best["result"]["reasons"]) ?: "Базовое соответствие",
                ]);

                $assigned++;
            } else {
                $ghost->update(["location_id" => null,
                    "match_score" => null,
                    "match_reason" => "Подходящее место не найдено"]);
                $failed++;
            }
        }

        return back()->with("success", "Автораспределение: расселено {$assigned}, без места {$failed}.");
    }

    public function assign(Request $request, Ghost $ghost): RedirectResponse
    {
        $data = $request->validate(["location_id" => "required|exists:locations,id"]);
        $location = Location::findOrFail($data["location_id"]);
        $r = $this->matcher->score($ghost, $location);

        $ghost->update([
            "location_id" => $location->id,
            "match_score" => $r["score"],
            "match_reason" => implode("; ", array_merge($r["reasons"], $r["warnings"])),
        ]);

        if (!empty($r["blockers"]))
            return back()->with("error", "ВНИМАНИЕ! Выбор конфликтует с условиями: " . implode("; ", $r["blockers"]));

        if (!empty($r["warnings"]))
            return back()->with("warning", "Назначено с предупреждениями: " . implode("; ", $r["warnings"]));

        return back()->with("success", "Назначено место «{$location->name}» (оценка {$r["score"]}).");
    }

    public function unassign(Ghost $ghost): RedirectResponse
    {
        $ghost->update(["location_id" => null,
            "match_score" => null,
            "match_reason" => null]);

        return back()->with("success", "Назначение снято.");
    }

    public function destroy(Ghost $ghost): RedirectResponse
    {
        $ghost->delete();
        return redirect()->route("ghosts.index")->with("success", "Заявка удалена.");
    }
}
