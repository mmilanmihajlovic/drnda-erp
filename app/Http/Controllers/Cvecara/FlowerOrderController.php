<?php
namespace App\Http\Controllers\Cvecara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cvecara\StoreFlowerOrderRequest;
use App\Http\Requests\Cvecara\StoreFlowerOrderItemRequest;
use App\Models\CaseItem;
use App\Models\Department;
use App\Models\FlowerOrder;
use App\Models\FlowerOrderItem;
use App\Models\FuneralCase;
use App\Services\CaseProgressService;

class FlowerOrderController extends Controller {
    public function __construct(private CaseProgressService $progress) {}

    public function index() {
        $orders = FlowerOrder::with(['funeralCase', 'items'])
            ->whereIn('status', ['aktivan'])
            ->latest()->paginate(20);
        return view('cvecara.prodaja', compact('orders'));
    }

    public function store(StoreFlowerOrderRequest $request, FuneralCase $case) {
        FlowerOrder::create([
            ...$request->validated(),
            'order_number' => FlowerOrder::generateOrderNumber(),
            'case_id'      => $case->id,
            'created_by'   => auth()->id(),
        ]);
        $this->refreshProgress($case);
        return redirect()->route('cvecara.show', $case)->with('success', 'Porudzbina je kreirana.');
    }

    /**
     * Dodaj stavku u porudzbinu + automatski kreiraj CaseItem za racun.
     * CaseItem je "naplativa stavka" koja se koristi pri generisanju racuna.
     */
    public function storeItem(StoreFlowerOrderItemRequest $request, FuneralCase $case, FlowerOrder $order) {
        $dept = Department::cvecara();

        // Kreiraj FlowerOrderItem
        $item = FlowerOrderItem::create([
            ...$request->validated(),
            'flower_order_id' => $order->id,
        ]);

        // Kreiraj odgovarajuci CaseItem (za racun)
        CaseItem::create([
            'case_id'       => $case->id,
            'department_id' => $dept->id,
            'item_id'       => $request->input('item_id'),
            'order_id'      => $order->id,
            'description'   => $request->input('description'),
            'quantity'      => $request->input('quantity'),
            'unit_price'    => $request->input('unit_price'),
            'billable'      => true,
        ]);

        $this->refreshProgress($case);
        return redirect()->route('cvecara.show', $case)->with('success', 'Stavka je dodata.');
    }

    public function destroy(FuneralCase $case, FlowerOrder $order) {
        // Obrisi i odgovarajuce CaseItems
        CaseItem::where('case_id', $case->id)->where('order_id', $order->id)->delete();
        $order->delete();
        $this->refreshProgress($case);
        return redirect()->route('cvecara.show', $case)->with('success', 'Porudzbina je obrisana.');
    }

    public function destroyItem(FuneralCase $case, FlowerOrderItem $item) {
        // Obrisi odgovarajuci CaseItem
        CaseItem::where('case_id', $case->id)
            ->where('order_id', $item->flower_order_id)
            ->where('description', $item->description)
            ->first()?->delete();
        $item->delete();
        $this->refreshProgress($case);
        return redirect()->route('cvecara.show', $case)->with('success', 'Stavka je obrisana.');
    }

    private function refreshProgress(FuneralCase $case): void {
        $dept = Department::cvecara();
        $cd   = $case->caseDepartments()->where('department_id', $dept->id)->first();
        if ($cd) $this->progress->recalculate($cd->load('department'));
    }
}
