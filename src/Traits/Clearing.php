<?php
/**
 * Eloquent IFRS Accounting
 *
 * @author    Edward Mungai
 * @copyright Edward Mungai, 2020, Germany
 * @license   MIT
 */
namespace IFRS\Traits;

/**
 *
 * @author emung
 */

trait Clearing
{
    /**
     * Cleared Transaction amount.
     */
    public function clearedAmount()
    {
        $cleared = 0;
        foreach ($this->clearances as $clearance) {
            $cleared += $clearance->amount;
        }

        return $cleared;
    }
    /**
     * Cleared Model records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function clearances()
    {
        return $this->morphMany('IFRS\Models\Assignment', 'cleared');
    }
}
