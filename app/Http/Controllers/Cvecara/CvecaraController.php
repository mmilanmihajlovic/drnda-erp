<?php
namespace App\Http\Controllers\Cvecara;

use App\Http\Controllers\Controller;
use App\Models\CaseDepartment;
use App\Models\Department;
use App\Models\FlowerOrder;
use App\Models\FlowerOrderItem;
use Illuminate\Http\Request;

class CvecaraController extends Controller {

    public function dashboard() {
        $dept = Department::cvecara();

        $activeCaseDepts = CaseDepartment::with(['funeralCase'])
            ->where('department_id', $dept->id)
            ->where('status', 'aktivan')
            ->latest()->get();

        $lateItems = FlowerOrderItem::with(['flowerOrder.funeralCase', 'assignedWorker'])
            ->whereHas('flowerOrder', fn($q) => $q->where('status', 'aktivan'))
            ->where('production_status', '!=', 'isporuceno')
            ->whereHas('flowerOrder', fn($q) => $q->whereNotNull('delivery_at')->where('delivery_at', '<', now()))
            ->get();

        $pendingProduction = FlowerOrderItem::with(['flowerOrder.funeralCase'])
            ->whereHas('flowerOrder', fn($q) => $q->where('status', 'aktivan'))
            ->whereIn('production_status', ['novo', 'dodeljeno', 'u_izradi'])
            ->count();

        $readyItems = FlowerOrderItem::where('production_status', 'spremno')->count();

        return view('cvecara.dashboard', compact(
            'activeCaseDepts', 'lateItems', 'pendingProduction', 'readyItems'
        ));
    }

    public function show(\App\Models\FuneralCase $case) {
        $dept = Department::cvecara();

        $caseDept = CaseDepartment::where('case_id', $case->id)
            ->where('department_id', $dept->id)
            ->with(['closedBy', 'reopenedBy'])
            ->firstOrFail();

        $orders = FlowerOrder::where('case_id', $case->id)
            ->with(['items.assignedWorker'])
            ->latest()->get();

        $availableItems = \App\Models\Item::where('department_id', $dept->id)->active()->get();
        $workers = \App\Models\Worker::where('department_id', $dept->id)->active()->get();

        return view('cvecara.show', compact('case', 'caseDept', 'orders', 'availableItems', 'workers'));
    }
}
