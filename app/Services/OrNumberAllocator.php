<?php

namespace App\Services;

use App\Models\OrAssignment;
use App\Models\bpls\onlineBPLS\BplsApplicationOr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrNumberAllocator
{
    /**
     * Allocate $count OR numbers from the available pool.
     *
     * @param  int     $count        How many ORs to allocate (1, 2, or 4)
     * @param  string  $receiptType  e.g. '51C', 'RPTA', 'CTC'
     * @return array<int, array{or_assignment_id: int, or_number: string}>
     *
     * @throws RuntimeException if the pool has insufficient numbers
     */
    public function allocate(int $count, string $receiptType = '51C'): array
    {
        return DB::transaction(function () use ($count, $receiptType) {

            $assignments = OrAssignment::where('receipt_type', $receiptType)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($assignments->isEmpty()) {
                throw new RuntimeException(
                    "No OR assignment pool found for receipt type [{$receiptType}]."
                );
            }

            $allocated = [];

            foreach ($assignments as $assignment) {
                if (count($allocated) >= $count) break;

                $start   = (int) $assignment->start_or;
                $end     = (int) $assignment->end_or;
                $padding = strlen((string) $assignment->start_or);

                // OR numbers already consumed from this range
                $used = BplsApplicationOr::where('or_assignment_id', $assignment->id)
                    ->pluck('or_number')
                    ->map(fn ($n) => (int) $n)
                    ->toArray();

                for ($n = $start; $n <= $end; $n++) {
                    if (count($allocated) >= $count) break;

                    if (!in_array($n, $used)) {
                        $allocated[] = [
                            'or_assignment_id' => $assignment->id,
                            'or_number'        => str_pad((string) $n, $padding, '0', STR_PAD_LEFT),
                        ];
                    }
                }
            }

            if (count($allocated) < $count) {
                throw new RuntimeException(
                    "Insufficient OR numbers available. Needed {$count}, only " . count($allocated) . " remaining."
                );
            }

            return $allocated;
        });
    }
}