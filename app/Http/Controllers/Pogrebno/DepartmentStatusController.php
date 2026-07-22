<?php
namespace App\Http\Controllers\Pogrebno;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pogrebno\CloseDepartmentRequest;
use App\Http\Requests\Pogrebno\ReopenDepartmentRequest;
use App\Models\CaseDepartment;
use App\Models\Department;
use App\Models\FuneralCase;
use App\Services\CloseDepartmentService;
use App\Services\ReopenDepartmentService;

class DepartmentStatusController extends Controller {
    public function __construct(
        private CloseDepartmentService  $closer,
        private ReopenDepartmentService $reopener,
    ) {}

    public function close(CloseDepartmentRequest $request, FuneralCase $case) {
        $dept = Department::pogrebno();
        $cd   = CaseDepartment::where('case_id', $case->id)
            ->where('department_id', $dept->id)
            ->firstOrFail();

        try {
            $status = $request->input('no_activity') ? 'nema_aktivnosti' : 'zatvoreno';
            $this->closer->close($cd, auth()->user(), $status);
            return redirect()->route('pogrebno.show', $case)
                ->with('success', 'Odeljenje Pogrebno je zatvoreno.');
        } catch (\RuntimeException $e) {
            return redirect()->route('pogrebno.show', $case)
                ->with('error', $e->getMessage());
        }
    }

    public function reopen(ReopenDepartmentRequest $request, FuneralCase $case) {
        $dept = Department::pogrebno();
        $cd   = CaseDepartment::where('case_id', $case->id)
            ->where('department_id', $dept->id)
            ->firstOrFail();

        $this->reopener->reopen($cd, auth()->user(), $request->input('reason'));

        return redirect()->route('pogrebno.show', $case)
            ->with('success', 'Odeljenje je ponovo otvoreno.');
    }
}
