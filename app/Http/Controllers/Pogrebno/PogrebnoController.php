<?php
namespace App\Http\Controllers\Pogrebno;

use App\Http\Controllers\Controller;
use App\Models\CaseDepartment;
use App\Models\Department;
use App\Models\Task;

class PogrebnoController extends Controller {
    public function dashboard() {
        $dept = Department::pogrebno();

        // Aktivni slucajevi Pogrebnog
        $activeCaseDepts = CaseDepartment::with(['funeralCase', 'funeralCase.caseDepartments'])
            ->where('department_id', $dept->id)
            ->where('status', 'aktivan')
            ->latest()
            ->get();

        // Otvoreni zadaci
        $openTasks = Task::with(['funeralCase', 'assignedWorker'])
            ->where('department_id', $dept->id)
            ->open()
            ->orderBy('due_at')
            ->get();

        // Kasnjenja
        $lateTasks = $openTasks->filter(fn($t) => $t->isLate());

        // Slucajevi spremni za zatvaranje (svi zadaci zavrseni)
        $readyToClose = $activeCaseDepts->filter(function ($cd) use ($dept) {
            $openCount = Task::where('case_id', $cd->case_id)
                ->where('department_id', $dept->id)
                ->open()
                ->count();
            return $openCount === 0;
        });

        return view('pogrebno.dashboard', compact(
            'activeCaseDepts', 'openTasks', 'lateTasks', 'readyToClose'
        ));
    }

    public function show(\App\Models\FuneralCase $case) {
        $dept = Department::pogrebno();

        $caseDept = CaseDepartment::where('case_id', $case->id)
            ->where('department_id', $dept->id)
            ->with(['closedBy', 'reopenedBy'])
            ->firstOrFail();

        $caseItems = $case->caseItems()
            ->where('department_id', $dept->id)
            ->with('item')
            ->latest()
            ->get();

        $tasks = $case->tasks()
            ->where('department_id', $dept->id)
            ->with('assignedWorker')
            ->orderBy('due_at')
            ->get();

        $availableItems = \App\Models\Item::where('department_id', $dept->id)->active()->get();
        $workers        = \App\Models\Worker::where('department_id', $dept->id)->active()->get();

        return view('pogrebno.show', compact(
            'case', 'caseDept', 'caseItems', 'tasks', 'availableItems', 'workers'
        ));
    }
}
