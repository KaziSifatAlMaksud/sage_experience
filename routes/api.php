use Illuminate\Http\Request;
use App\Models\SkillArea;
use App\Models\Skill;
use App\Models\Practice;
use Illuminate\Support\Facades\DB;

// Route to get all skills data
Route::get('/skills-data', function () {
    return response()->json([
        'skillAreas' => SkillArea::all(),
        'skills' => Skill::with('skillArea')->get(),
        'practices' => Practice::with('skill')->get()
    ]);
});

// Individual endpoints for skills and practices by ID
Route::get('/skills', function (Request $request) {
    $areaId = $request->query('area_id');

    // Use the same reliable subquery approach to ensure unique skills by ID
    return DB::query()
        ->fromSub(function ($query) use ($areaId) {
            $query->from('skills')
                ->select('id', 'name', 'description', 'skill_area_id')
                ->where('skill_area_id', $areaId)
                ->orderBy('id');
        }, 'skills_by_area')
        ->select('id', 'name', 'description', 'skill_area_id')
        ->groupBy('id', 'name', 'description', 'skill_area_id')
        ->orderBy('name')
        ->get();
});

Route::get('/practices', function (Request $request) {
    $skillId = $request->query('skill_id');

    // Use the same reliable subquery approach for practices
    return DB::query()
        ->fromSub(function ($query) use ($skillId) {
            $query->from('practices')
                ->select('id', 'name', 'description', 'skill_id', 'order')
                ->where('skill_id', $skillId)
                ->orderBy('id');
        }, 'practices_by_skill')
        ->select('id', 'name', 'description', 'skill_id', 'order')
        ->groupBy('id', 'name', 'description', 'skill_id', 'order')
        ->orderBy('order')
        ->get();
});
