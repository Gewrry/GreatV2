<?php

namespace App\Services\RPT;

use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\FaasProperty;

class TdValidationService extends ValidationService
{
    /**
     * Workflow Guard: FAAS -> TD Store.
     */
    public function assertCanStore(FaasProperty $faas, int $effectivityYear)
    {
        if (!$faas->isApproved()) {
            $this->fail('faas_property_id', 'Tax Declarations can only be created for APPROVED property records.');
        }

        // Removed the overarching `faas_property_id` duplicate check.
        // TaxDeclarationController already handles specific component deduplication 
        // to conform with MRPAAO rules (one TD per component).

        if ($faas->totalAssessedValue() <= 0) {
            $this->fail('total_assessed_value', 'Cannot generate TD: The property has zero assessed value.');
        }
    }

    /**
     * Workflow Guard: Draft -> Approved.
     */
    public function assertCanApprove(TaxDeclaration $td)
    {
        if ($td->status !== 'for_review') {
            $this->fail('status', 'Only TDs under review can be approved.');
        }

        if (empty($td->tax_rate) || $td->tax_rate <= 0) {
            $this->fail('tax_rate', 'Cannot approve TD: Valid tax rate (Basic) is required.');
        }
        
        // Ensure FAAS is still active
        if ($td->property->isInactive()) {
            $this->fail('property', 'Cannot approve TD: The parent FAAS record is now inactive.');
        }
    }

    /**
     * Workflow Guard: Approved -> Forwarded.
     */
    public function assertCanForward(TaxDeclaration $td)
    {
        if ($td->status !== 'approved') {
            $this->fail('status', 'Only approved TDs can be forwarded to Treasury.');
        }

        if (empty($td->td_no)) {
            $this->fail('td_no', 'Cannot forward: TD Number has not been assigned.');
        }
    }
}
