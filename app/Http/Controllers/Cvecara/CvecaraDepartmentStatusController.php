<?php
namespace App\Http\Controllers\Cvecara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cvecara\CloseDepartmentRequest;
use App\Http\Requests\Cvecara\ReopenDepartmentRequest;
use App\Models\CaseDepartment;
use App\Models\Department;
use App\Models\FlowerOrder;
use App\Models\FlowerOrderItem;
use App\Models\FuneralCase;
use App\Services\CloseDepartmentService;
use App\Services\ReopenDepartmentService;

class CvecaraDepartmentStatusController extends Controller {
    public function __construct(
        private CloseDepartmentService  $closer,
        private ReopenDepartmentService $reopener,
    ) {}

    public function close(CloseDepartmentRequest $request, FuneralCase $case) {
        $dept = Department::cvecara();
        $cd   = CaseDepartment::where('case_id', $case->id)
            ->where('department_id', $dept->id)->firstOrFail();

        // Validacija: sve stavke moraju biti isporucene
        if (!$request->boolean('no_activity')) {
            $pendingItems = FlowerOrderItem::whereHas('flowerOrder', fn($q) =>
                $q->where('case_id', $case->id)->where('status', '!=', 'otkazan')
            )->where('production_status', '!=', 'isporuceno')->count();

            if ($pendingItems > 0) {
                return redirect()->route('cvecara.show', $case)
                    ->with('error', "Cvecara ima {$pendingItems} stavki koje nisu isporucene. Isporucite ih pre zatvaranja.");
            }
        }

        try {
            $status = $request->boolean('no_activity') ? 'nema_aktivnosti' : 'zatvoreno';
            $this->closer->close($cd, auth()->user(), $status);
            return redirect()->route('cvecara.show', $case)->with('success', 'Cvecara odeljenje je zatvoreno.');
        } catch (\RuntimeException $e) {
            return redirect()->route('cvecara.show', $case)->with('error', $e->getMessage());
        }
    }

    public function reopen(ReopenDepartmentRequest $request, FuneralCase $case) {
        $dept = Department::cvecara();
        $cd   = CaseDepartment::where('case_id', $case->id)
            ->where('department_id', $dept->id)->firstOrFail();
        $this->reopener->reopen($cd, auth()->user(), $request->input('reason'));
        return redirect()->route('cvecara.show', $case)->with('success', 'Cvecara odeljenje je ponovo otvoreno.');
    }
}
