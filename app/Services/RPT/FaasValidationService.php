<?php

namespace App\Services\RPT;

use App\Models\RPT\FaasProperty;
use App\Models\RPT\RptaRevisionYear;

class FaasValidationService extends ValidationService
{
    /**
     * Legal Guard: Ownership Consistency.
     */
    public function assertOwnerIsValid(FaasProperty $faas)
    {
        if (empty($faas->owner_name)) {
            $this->fail('owner_name', 'Property owner name is legally required.');
        }
        if (empty($faas->owner_address)) {
            $this->fail('owner_address', 'Property owner address is legally required for tax liability.');
        }
    }

    /**
     * Audit Guard: No Duplicate Active FAAS for Same Title.
     */
    public function assertNoActiveDuplicateByTitle(?string $titleNo, ?int $excludeId = null)
    {
        if (empty($titleNo)) return;

        $exists = FaasProperty::where('title_no', $titleNo)
            ->where('status', 'approved')
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            $this->fail('title_no', "An active FAAS already exists for Title No: {$titleNo}.");
        }
    }

    /**
     * Workflow Guard: Draft -> For Review.
     */
    public function assertCanSubmitReview(FaasProperty $faas)
    {
        if (!$faas->isDraft()) {
            $this->fail('status', 'Only draft records can be submitted for review.');
        }

        if ($faas->property_type === 'land' && $faas->lands()->count() === 0) {
            $this->fail('components', 'Cannot submit for review: Property Type is Land, but no Land appraisal is attached.');
        }

        if ($faas->property_type === 'building' && $faas->buildings()->count() === 0) {
            $this->fail('components', 'Cannot submit for review: Property Type is Building, but no Building appraisal is attached.');
        }

        if ($faas->property_type === 'machinery' && $faas->machineries()->count() === 0) {
            $this->fail('components', 'Cannot submit for review: Property Type is Machinery, but no Machinery appraisal is attached.');
        }

        if ($faas->property_type === 'mixed' && $faas->lands()->count() === 0 && $faas->buildings()->count() === 0 && $faas->machineries()->count() === 0) {
            $this->fail('components', 'Cannot submit for review: Property Type is Mixed, but no appraisals (Land, Building, or Machinery) are attached.');
        }

        if ($faas->totalMarketValue() <= 0) {
            $this->fail('total_market_value', 'Cannot submit for review: Total Market Value must be greater than zero.');
        }

        // Attachment Guard: At least one supporting document must be on file.
        if ($faas->attachments()->count() === 0) {
            $this->fail('attachments', 'Cannot submit for review: Please upload at least one supporting document (e.g., Title/Deed, Sketch Plan) before submitting.');
        }
    }

    /**
     * Workflow Guard: For Review -> Approved.
     */
    public function assertCanApprove(FaasProperty $faas)
    {
        if (!in_array($faas->status, ['for_review', 'recommended'])) {
            $this->fail('status', 'Only records under review or recommended can be approved.');
        }

        // Check Revision Year
        $revision = $faas->revision_year_id 
            ? RptaRevisionYear::find($faas->revision_year_id)
            : RptaRevisionYear::current();

        if (!$revision) {
            $this->fail('revision_year_id', 'Cannot approve: No active or current revision year set in settings.');
        }

        // Check if ARP is already assigned
        if ($faas->arp_no && FaasProperty::where('arp_no', $faas->arp_no)->where('id', '!=', $faas->id)->exists()) {
            $this->fail('arp_no', 'The ARP Number assigned is already in use by another record.');
        }
        
        $this->assertOwnerIsValid($faas);
    }

    /**
     * Workflow Guard: General Revision.
     */
    public function assertCanGeneralRevise(FaasProperty $faas)
    {
        if (!$faas->isApproved()) {
            $this->fail('status', 'Only approved FAAS records can undergo general revision.');
        }

        if ($faas->isInactive()) {
            $this->fail('status', 'Target FAAS is already inactive.');
        }

        // Block if pending TDs exist
        if ($faas->taxDeclarations()->whereIn('status', ['draft', 'for_review', 'approved'])->exists()) {
            $this->fail('td', 'Cannot revise: There are pending or approved Tax Declarations that have not yet been forwarded to Treasury.');
        }

        // Block if children already exist
        if ($faas->revisions()->exists()) {
            $this->fail('revisions', 'Cannot revise: A successor revision already exists for this record.');
        }
    }

    /**
     * Workflow Guard: Subdivision.
     */
    public function assertCanSubdivide(FaasProperty $faas)
    {
        // Allow subdivision if explicitly 'land', 'mixed', or if land appraisal exists
        $isLandRelated = strcasecmp($faas->property_type, 'land') === 0 || 
                         strcasecmp($faas->property_type, 'mixed') === 0 ||
                         $faas->lands()->exists();

        if (!$isLandRelated) {
            $this->fail('property_type', 'Invalid Transaction: Only properties with Land components can be subdivided.');
        }

        if (!$faas->isApproved()) {
            $this->fail('status', 'Target FAAS must be approved before it can be subdivided.');
        }

        if ($faas->isInactive()) {
            $this->fail('status', 'Target FAAS is already inactive.');
        }
    }

    /**
     * Workflow Guard: Consolidation.
     */
    public function assertCanConsolidate(array $faasIds)
    {
        $faasRecords = FaasProperty::whereIn('id', $faasIds)->get();

        if ($faasRecords->count() !== count($faasIds)) {
            $this->fail('ids', 'One or more selected FAAS records could not be found.');
        }

        foreach ($faasRecords as $faas) {
            $isLandRelated = strcasecmp($faas->property_type, 'land') === 0 || 
                             strcasecmp($faas->property_type, 'mixed') === 0 ||
                             $faas->lands()->exists();

            if (!$isLandRelated) {
                $this->fail('property_type', "Invalid Consolidation: Property {$faas->arp_no} has no Land components.");
            }
            if (!$faas->isApproved()) {
                $this->fail('status', "Property {$faas->arp_no} must be approved before consolidation.");
            }
            if ($faas->isInactive()) {
                $this->fail('status', "Property {$faas->arp_no} is already inactive.");
            }
        }
    }

    /**
     * Legal Guard: Tax Clearance.
     * Prevents Transfer of Ownership if there are outstanding tax liabilities.
     */
    public function assertHasTaxClearance(FaasProperty $faas)
    {
        // Check all associated Tax Declarations
        foreach ($faas->taxDeclarations as $td) {
            // 1. Workflow Guard: Must be forwarded to Treasury first 
            // This ensures that billings exist and are paid.
            if ($td->status === 'approved') {
                $this->fail('tax_clearance', "Action Blocked: Tax Declaration {$td->td_no} has not been forwarded to Treasury. Please forward it and ensure all taxes are paid before transferring.");
            }

            // 2. Financial Guard: Check for any unpaid or partial billings
            $hasUnpaid = $td->billings()
                ->whereIn('status', ['unpaid', 'partial'])
                ->where('balance', '>', 0)
                ->exists();

            if ($hasUnpaid) {
                $this->fail('tax_clearance', "Action Blocked: Property (ARP: {$faas->arp_no}) has outstanding tax liabilities. Please settle all billings in Treasury first.");
            }
        }
    }
}
