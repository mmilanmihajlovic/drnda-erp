<?php
namespace App\Services;

use App\Models\AuditLog;
use App\Models\CaseDepartment;
use App\Models\CaseItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReopenDepartmentService {
    public function __construct(private CaseProgressService $progress) {}

    /**
     * Ponovo otvori zatvoreno odeljenje.
     * Razlog je obavezan.
     */
    public function reopen(CaseDepartment $cd, User $user, string $reason): void {
        DB::transaction(function () use ($cd, $user, $reason) {
            $old = $cd->only(['status', 'progress', 'closed_at', 'closed_by']);

            // Odkljucaj CaseItems
            CaseItem::where('case_id', $cd->case_id)
                ->where('department_id', $cd->department_id)
                ->whereNotNull('locked_at')
                ->update(['locked_at' => null]);

            // Vrati u aktivan status
            $cd->update([
                'status'         => 'aktivan',
                'progress'       => 0,
                'reopened_at'    => now(),
                'reopened_by'    => $user->id,
                'reopen_reason'  => $reason,
            ]);

            // Audit log
            AuditLog::record('reopen_department', $cd, $old, [
                'status'        => 'aktivan',
                'reopen_reason' => $reason,
                'reopened_by'   => $user->id,
            ]);

            // Refresh progress
            $this->progress->recalculate($cd->fresh());
        });
    }
}
