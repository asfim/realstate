<?php

namespace App\Services\ERP;

use App\Models\Admin;
use App\Models\ERP\Unit;
use App\Models\ERP\UnitPriceHistory;
use App\Models\ERP\UnitStatusEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UnitAvailabilityService
{
    /**
     * Allowed lifecycle transitions.
     */
    protected array $allowedTransitions = [
        'draft' => ['available'],
        'available' => ['held', 'booked', 'draft'],
        'held' => ['available', 'booked', 'draft'],
        'booked' => ['sold', 'cancelled', 'held'],
        'sold' => ['registration_pending', 'handed_over', 'cancelled'],
        'registration_pending' => ['registered', 'cancelled'],
        'registered' => ['handed_over'],
        'handed_over' => [],
        'cancelled' => ['available', 'draft'],
    ];

    /**
     * Transition a unit to a new status inside a database transaction.
     */
    public function transitionStatus(Unit $unit, string $newStatus, ?Admin $actor = null, ?string $notes = null): Unit
    {
        return DB::transaction(function () use ($unit, $newStatus, $actor, $notes) {
            $lockedUnit = Unit::query()->whereKey($unit->id)->lockForUpdate()->firstOrFail();

            $currentStatus = $lockedUnit->status;

            if ($currentStatus === $newStatus) {
                return $lockedUnit;
            }

            if (!isset($this->allowedTransitions[$currentStatus]) || !in_array($newStatus, $this->allowedTransitions[$currentStatus], true)) {
                throw ValidationException::withMessages([
                    'status' => "Cannot transition unit from '{$currentStatus}' to '{$newStatus}'.",
                ]);
            }

            $lockedUnit->status = $newStatus;
            $lockedUnit->lock_version += 1;
            $lockedUnit->save();

            UnitStatusEvent::create([
                'company_id' => $lockedUnit->company_id,
                'unit_id' => $lockedUnit->id,
                'from_status' => $currentStatus,
                'to_status' => $newStatus,
                'actor_id' => $actor?->id,
                'notes' => $notes,
                'created_at' => now(),
            ]);

            return $lockedUnit;
        });
    }

    /**
     * Update unit price with historical logging.
     */
    public function revisePrice(Unit $unit, float|string $newPrice, float|string|null $newMinPrice = null, ?Admin $actor = null, ?string $reason = null): Unit
    {
        return DB::transaction(function () use ($unit, $newPrice, $newMinPrice, $actor, $reason) {
            $lockedUnit = Unit::query()->whereKey($unit->id)->lockForUpdate()->firstOrFail();
            $oldPrice = $lockedUnit->asking_price;

            $lockedUnit->asking_price = $newPrice;
            if ($newMinPrice !== null) {
                $lockedUnit->min_price = $newMinPrice;
            }
            $lockedUnit->lock_version += 1;
            $lockedUnit->save();

            UnitPriceHistory::create([
                'company_id' => $lockedUnit->company_id,
                'unit_id' => $lockedUnit->id,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'changed_by' => $actor?->id,
                'reason' => $reason,
                'created_at' => now(),
            ]);

            return $lockedUnit;
        });
    }
}
