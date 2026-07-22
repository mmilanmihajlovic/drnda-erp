<?php
namespace App\Http\Controllers\Pogrebno;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pogrebno\StoreCaseItemRequest;
use App\Models\CaseItem;
use App\Models\Department;
use App\Models\FuneralCase;
use App\Services\CaseProgressService;

class CaseItemController extends Controller {
    public function __construct(private CaseProgressService $progress) {}

    public function store(StoreCaseItemRequest $request, FuneralCase $case) {
        $dept = Department::pogrebno();

        $item = CaseItem::create([
            ...$request->validated(),
            'case_id'       => $case->id,
            'department_id' => $dept->id,
        ]);

        $this->progress->recalculate(
            $case->caseDepartments()->where('department_id', $dept->id)->first()
        );

        return redirect()
            ->route('pogrebno.show', $case)
            ->with('success', 'Stavka je dodata.');
    }

    public function destroy(FuneralCase $case, CaseItem $caseItem) {
        abort_if($caseItem->isLocked(), 403, 'Stavka je zakljucana.');
        $caseItem->delete();
        return redirect()->route('pogrebno.show', $case)->with('success', 'Stavka je obrisana.');
    }
}
