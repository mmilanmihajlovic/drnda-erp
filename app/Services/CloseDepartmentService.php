<?php
namespace App\Services;

use App\Models\AuditLog;
use App\Models\CaseDepartment;
use App\Models\CaseItem;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CloseDepartmentService {
    public function __construct(private CaseProgressService $progress) {}

    /**
     * Zatvori odeljenje slucaja.
     *
     * @param string $status 'zatvoreno' | 'nema_aktivnosti'
     * @throws \RuntimeException ako ima otvorenih zadataka
     */
    public function close(CaseDepartment $cd, User $user, string $status = 'zatvoreno'): void {
        // Validacija: nema otvorenih zadataka
        $openTasks = Task::where('case_id', $cd->case_id)
            ->where('department_id', $cd->department_id)
            ->open()
            ->count();

        if ($openTasks > 0) {
            throw new \RuntimeException(
                "Odeljenje ima {$openTasks} otvorenih zadataka. Zatvorite ih pre zatvaranja odeljenja."
            );
        }

        DB::transaction(function () use ($cd, $user, $status) {
            $old = $cd->only(['status', 'progress', 'closed_at', 'closed_by']);

            // Zakljucaj CaseItems
            CaseItem::where('case_id', $cd->case_id)
                ->where('department_id', $cd->department_id)
                ->whereNull('locked_at')
                ->update(['locked_at' => now()]);

            // Zatvori odeljenje
            $cd->update([
                'status'    => $status,
                'progress'  => 100,
                'closed_at' => now(),
                'closed_by' => $user->id,
            ]);

            // Audit log
            AuditLog::record('close_department', $cd, $old, $cd->fresh()->toArray());

            // Refresh progress
            $this->progress->recalculate($cd->fresh());
        });
    }
}
