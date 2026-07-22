<?php
namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreWorkerRequest;
use App\Http\Requests\Settings\UpdateWorkerRequest;
use App\Models\Department;
use App\Models\User;
use App\Models\Worker;
class WorkerController extends Controller {
    public function index() { return view('settings.workers.index', ['workers' => Worker::with(['department','user'])->latest()->paginate(20)]); }
    public function create() { return view('settings.workers.create', ['departments' => Department::orderBy('name')->get(), 'users' => User::active()->orderBy('name')->get()]); }
    public function store(StoreWorkerRequest $request) { Worker::create($request->validated()); return redirect()->route('settings.workers.index')->with('success', 'Radnik je uspesno dodat.'); }
    public function edit(Worker $worker) { return view('settings.workers.edit', ['worker' => $worker, 'departments' => Department::orderBy('name')->get(), 'users' => User::active()->orderBy('name')->get()]); }
    public function update(UpdateWorkerRequest $request, Worker $worker) { $worker->update($request->validated()); return redirect()->route('settings.workers.index')->with('success', 'Radnik je uspesno izmenjen.'); }
    public function destroy(Worker $worker) { $worker->delete(); return redirect()->route('settings.workers.index')->with('success', 'Radnik je obrisan.'); }
}
