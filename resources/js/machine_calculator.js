/**
 * eRPTA Machine Valuation Calculator
 *
 * Mode-aware: auto computes residual_percent from acquisition_date + useful_life + salvage.
 *             manual uses assessor-entered residual_percent directly.
 *
 * Formula:
 *   base_value     = acquisition_cost + freight_cost + installation_cost + other_cost
 *   [auto] age     = current_year - year(acquisition_date)
 *   [auto] dep_rate = min(age / useful_life, 1.0)
 *   [auto] residual = max((1 - dep_rate) × 100, salvage_value_percent)
 *   market_value   = base_value × (residual_percent / 100)
 *   assessed_value = market_value × (assessment_level / 100)
 *
 * age and dep_rate are displayed for the assessor but are NOT submitted as hidden fields.
 * They will be recomputed server-side and stored only in machinery_valuations.
 */
$(document).ready(function () {

    // ─────────────────────────────────────────────────────────────────────────
    // RESIDUAL MODE TOGGLE
    // ─────────────────────────────────────────────────────────────────────────

    function applyResidualMode(mode) {
        if (mode === 'auto') {
            // Hide manual input, show auto fields
            $('#residual-manual-group').addClass('hidden');
            $('#residual-auto-group').removeClass('hidden');
            // Make residual_percent read-only — it will be filled by calculateValues()
            $('#residual_percent').prop('readonly', true)
                .addClass('bg-gray-50 text-gray-500 cursor-not-allowed')
                .removeClass('bg-white text-gray-700');
        } else {
            // Show manual input, hide auto intermediary displays
            $('#residual-manual-group').removeClass('hidden');
            $('#residual-auto-group').addClass('hidden');
            // Make residual_percent editable
            $('#residual_percent').prop('readonly', false)
                .addClass('bg-white text-gray-700')
                .removeClass('bg-gray-50 text-gray-500 cursor-not-allowed');
        }
        calculateValues();
    }

    // Init on page load (handles old() repopulation after validation failure)
    applyResidualMode($('input[name="residual_mode"]:checked').val() || 'auto');

    // React to toggle change
    $('input[name="residual_mode"]').on('change', function () {
        applyResidualMode($(this).val());
    });

    // ─────────────────────────────────────────────────────────────────────────
    // CASCADING CLASSIFICATION LOOKUPS
    // ─────────────────────────────────────────────────────────────────────────

    function fetchActualUses() {
        const assmtKind = $('#assmt_kind').val();
        const revYear   = $('#rev_year').val();

        if (!assmtKind || !revYear) {
            $('#actual_use').prop('disabled', true).html('<option value="">Select Actual Use...</option>');
            return;
        }

        $('#actual_use').prop('disabled', true).html('<option value="">Loading...</option>');

        // Fetch actual uses for this classification
        $.ajax({
            url: ROUTES.getActualUses,
            type: 'GET',
            data: { assmt_kind: assmtKind, rev_year: revYear, category: 'MACHINE' },
            success: function (response) {
                let options = '<option value="">Select Actual Use</option>';
                if (response && response.length > 0) {
                    response.forEach(item => {
                        options += `<option value="${item.actual_use}">${item.actual_use}</option>`;
                    });
                    $('#actual_use').html(options).prop('disabled', false);
                } else {
                    $('#actual_use').html('<option value="">No uses found</option>').prop('disabled', true);
                }
            }
        });

        // Fetch assessment level + classification defaults (salvage, useful_life)
        $.ajax({
            url: ROUTES.getAssessmentLevel,
            type: 'GET',
            data: { assmt_kind: assmtKind, category: 'MACHINE' },
            success: function (response) {
                // Auto-fill assessment level from ordinance table
                if (response.assmnt_percent !== undefined) {
                    $('#assessment_level').val(response.assmnt_percent);
                }

                // Auto-fill salvage and useful_life if classification provides them
                // Only fill if the field is currently empty (don't overwrite assessor input)
                if (response.default_salvage !== undefined && !$('#salvage_value_percent').val()) {
                    $('#salvage_value_percent').val(response.default_salvage);
                }
                if (response.useful_life !== undefined && !$('#useful_life').val()) {
                    $('#useful_life').val(response.useful_life);
                }

                calculateValues();
            }
        });
    }

    $('#assmt_kind, #rev_year').on('change', fetchActualUses);

    // ─────────────────────────────────────────────────────────────────────────
    // CORE VALUATION CALCULATOR
    // ─────────────────────────────────────────────────────────────────────────

    function calculateValues() {
        const mode = $('input[name="residual_mode"]:checked').val() || 'auto';

        // ── Step 1: Base Value ───────────────────────────────────────────────
        const acq     = parseFloat($('#acquisition_cost').val())    || 0;
        const freight = parseFloat($('#freight_cost').val())         || 0;
        const install = parseFloat($('#installation_cost').val())    || 0;
        const other   = parseFloat($('#other_cost').val())           || 0;
        const baseVal = acq + freight + install + other;

        // ── Step 2: Age & Dep Rate (auto mode only; display only, not submitted) ──
        const acquisitionDateVal = $('#acquisition_date').val();
        const currentYear        = new Date().getFullYear();
        let age        = null;
        let depRate    = null;
        let ageLabel   = '—';
        let depRateLabel = '—';

        if (acquisitionDateVal) {
            const acqYear = new Date(acquisitionDateVal).getFullYear();
            age       = Math.max(0, currentYear - acqYear);
            ageLabel  = age + ' yr' + (age !== 1 ? 's' : '');

            const usefulLife = parseFloat($('#useful_life').val()) || 0;
            if (usefulLife > 0) {
                depRate      = Math.min(age / usefulLife, 1.0);
                depRateLabel = (depRate * 100).toFixed(2) + '%';
            }
        }

        // ── Step 3: Residual Percent ─────────────────────────────────────────
        let residualPct;

        if (mode === 'auto') {
            const salvage = parseFloat($('#salvage_value_percent').val()) || 20;
            if (depRate !== null) {
                const computed = (1 - depRate) * 100;
                residualPct    = Math.max(computed, salvage);
            } else {
                // No acquisition date — floor is salvage
                residualPct = salvage;
            }
            // Push computed value into the read-only field so assessor can see it
            $('#residual_percent').val(residualPct.toFixed(2));
        } else {
            // Manual: read directly from the assessor-entered field
            residualPct = parseFloat($('#residual_percent').val()) || 0;
        }

        // ── Step 4: Market Value ─────────────────────────────────────────────
        const marketVal = baseVal * (residualPct / 100);

        // ── Step 5: Assessed Value ───────────────────────────────────────────
        const assessLevel = parseFloat($('#assessment_level').val()) || 0;
        const assessedVal = marketVal * (assessLevel / 100);

        // ── Update display fields ─────────────────────────────────────────────
        $('#base_value_display').val(fmt(baseVal));
        $('#base_value_hidden').val(baseVal.toFixed(2));

        // Age and dep_rate: display only — NOT in hidden submit fields
        if (mode === 'auto') {
            $('#age_display').text(ageLabel);
            $('#dep_rate_display').text(depRateLabel);
        }

        $('#residual_display').text(residualPct.toFixed(2) + '%');

        $('#market_value_display').val(fmt(marketVal));
        $('#market_value_hidden').val(marketVal.toFixed(2));

        $('#assessed_value_display').val(fmt(assessedVal));
        $('#assessed_value_hidden').val(assessedVal.toFixed(2));

        // ── Update sidebar ───────────────────────────────────────────────────
        $('#sidebar-base-display').text('₱ ' + fmt(baseVal));
        if (mode === 'auto') {
            $('#sidebar-age-display').text(ageLabel);
            $('#sidebar-deprate-display').text(depRateLabel);
        } else {
            $('#sidebar-age-display').text('(manual)');
            $('#sidebar-deprate-display').text('(manual)');
        }
        $('#sidebar-residual-display').text(residualPct.toFixed(2) + '%');
        $('#sidebar-market-display').text('₱ ' + fmt(marketVal));
        $('#sidebar-assessed-display').text('₱ ' + fmt(assessedVal));
    }

    function fmt(v) {
        return v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Watch all valuation inputs
    $(document).on('input change',
        '#acquisition_cost, #freight_cost, #installation_cost, #other_cost, ' +
        '#acquisition_date, #useful_life, #salvage_value_percent, ' +
        '#residual_percent, #assessment_level',
        calculateValues
    );

    // Initial run for old() repopulation on validation failures
    calculateValues();

    // ─────────────────────────────────────────────────────────────────────────
    // OWNER MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────

    $('#add-owner-btn').on('click', function () {
        const selector  = $('#owner_selector');
        const ownerId   = selector.val();
        const ownerName = selector.find('option:selected').text().trim();

        if (!ownerId) return;

        if ($(`.owner-item[data-id="${ownerId}"]`).length > 0) {
            alert('This owner is already added.');
            return;
        }

        $('#selected-owners-container').append(`
            <div class="owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm" data-id="${ownerId}">
                <span class="text-xs font-bold text-gray-700">${ownerName}</span>
                <input type="hidden" name="owners[]" value="${ownerId}">
                <button type="button" class="remove-owner-btn text-red-400 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `);

        selector.val('');
    });

    $(document).on('click', '.remove-owner-btn', function () {
        $(this).closest('.owner-item').remove();
    });

});