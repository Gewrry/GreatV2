{{-- JS: toggle inline forms + auto-scroll --}}
<script>
    const assessmentRules = {!! $assessmentRules !!};
    // MRPAAO statutory constants (mirrors PHP model)
    const BLDG_DEP_PER_YEAR = 0.02;
    const BLDG_MAX_DEP      = 0.80;
    const MACH_MAX_DEP      = 0.80;
    const MACH_MIN_RESIDUAL = 0.20;

    function toggleForm(id) {
        const el = document.getElementById(id);
        el.classList.toggle('hidden');
        if (!el.classList.contains('hidden')) {
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function num(val) { return parseFloat(val) || 0; }
    function fmt(val) { return new Intl.NumberFormat('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(val); }

    function getRate(actualUseId, marketValue) {
        const rules = assessmentRules[actualUseId] || [];
        const match = rules.find(r => marketValue >= r.min && (r.max === null || marketValue <= r.max));
        return match ? match.rate : 0;
    }

    // ── Land Live Calc ────────────────────────────────────
    const landForm = document.querySelector('#land-form form');
    if (landForm) {
        const recalcLand = () => {
            const mv = num(landForm.querySelector('[name="area_sqm"]').value)
                     * num(landForm.querySelector('[name="unit_value"]').value);
            const useId = landForm.querySelector('[name="rpta_actual_use_id"]').value;
            const rate  = getRate(useId, mv);
            if (rate > 0) landForm.querySelector('[name="assessment_level"]').value = rate;
            const av = mv * num(landForm.querySelector('[name="assessment_level"]').value);
            document.getElementById('land-mv-preview').innerText = fmt(mv);
            document.getElementById('land-av-preview').innerText = fmt(av);
        };
        ['rpta_actual_use_id','area_sqm','unit_value','assessment_level']
            .forEach(n => landForm.querySelector(`[name="${n}"]`).addEventListener('input', recalcLand));
    }

    // ── Reusable Component Calculators ───────────────────
    function recalcBldgValues(container) {
        if (!container) return;
        const find = (sel) => container.querySelector(sel);
        
        const area    = num(find('[name="floor_area"]').value);
        const costSqm = num(find('[name="construction_cost_per_sqm"]').value);
        const yrCon   = num(find('[name="year_constructed"]').value);
        const yrApp   = num(find('[name="year_appraised"]').value) || {{ date('Y') }};
        const astLvl  = num(find('[name="assessment_level"]').value);

        // Statutory depreciation: 2%/yr, max 80%
        const age     = Math.max(0, yrApp - yrCon);
        const depRate = Math.min(age * BLDG_DEP_PER_YEAR, BLDG_MAX_DEP);
        const baseMv  = area * costSqm;
        const depAmt  = baseMv * depRate;
        const mv      = baseMv - depAmt;
        
        // Auto-fill assessment level from rules based on market value
        const useId = find('[name="rpta_actual_use_id"]').value;
        const autoRate = getRate(useId, mv);
        if (autoRate > 0) {
            const astLvlInput = find('[name="assessment_level"]');
            if (astLvlInput) astLvlInput.value = autoRate;
        }

        const finalAstLvl = num(find('[name="assessment_level"]').value);
        const av = mv * finalAstLvl;

        // Update displays if they exist in this container
        const depDisp = container.querySelector('.dep-rate-display');
        if (depDisp) depDisp.textContent = (depRate * 100).toFixed(1) + '% (' + age + ' yrs × 2%)' + (depRate >= BLDG_MAX_DEP ? ' — CAPPED' : '');

        const previews = {
            '.bmv-preview': baseMv,
            '.dep-amt-preview': depAmt,
            '.mv-preview': mv,
            '.av-preview': av
        };
        for (let sel in previews) {
            const el = container.querySelector(sel);
            if (el) el.innerText = fmt(previews[sel]);
        }
    }

    function recalcMachValues(container) {
        if (!container) return;
        const find = (sel) => container.querySelector(sel);

        const cost      = num(find('[name="original_cost"]').value);
        const life      = num(find('[name="useful_life"]').value);
        const yrAcq     = num(find('[name="year_acquired"]').value);
        const curYear   = {{ date('Y') }};

        // Statutory depreciation: age/life, max 80%, residual min 20%
        const age       = Math.max(0, curYear - yrAcq);
        const rawRate   = life > 0 ? age / life : 0;
        const depRate   = Math.min(rawRate, MACH_MAX_DEP);
        const depAmt    = cost * depRate;
        const mvRaw     = cost - depAmt;
        const mvFloor   = cost * MACH_MIN_RESIDUAL;
        const mv        = Math.max(mvRaw, mvFloor);

        // Auto-fill assessment level
        const useId = find('[name="rpta_actual_use_id"]').value;
        const autoRate = getRate(useId, mv);
        if (autoRate > 0) {
            const astLvlInput = find('[name="assessment_level"]');
            if (astLvlInput) astLvlInput.value = autoRate;
        }

        const finalAstLvl = num(find('[name="assessment_level"]').value);
        const av = mv * finalAstLvl;

        // Update displays
        const depDisp = container.querySelector('.dep-rate-display');
        const residualHit = mvRaw < mvFloor;
        if (depDisp) depDisp.textContent = (depRate * 100).toFixed(1) + '%'
            + (depRate >= MACH_MAX_DEP ? ' — MAX' : '')
            + (residualHit ? ' (20% residual applied)' : '');

        const previews = {
            '.cost-preview': cost,
            '.dep-amt-preview': cost - mv,
            '.mv-preview': mv,
            '.av-preview': av
        };
        for (let sel in previews) {
            const el = container.querySelector(sel);
            if (el) el.innerText = fmt(previews[sel]);
        }
    }

    // Initialize listeners for all forms (inline and modals)
    document.addEventListener('input', function(e) {
        // Find the closest form or container
        const bldgForm = e.target.closest('.building-calc-container');
        if (bldgForm) recalcBldgValues(bldgForm);

        const machForm = e.target.closest('.machinery-calc-container');
        if (machForm) recalcMachValues(machForm);
        
        // Legacy Land support
        const landForm = e.target.closest('#land-form form');
        if (landForm) {
            const areaInput = landForm.querySelector('[name="area_sqm"]');
            const valInput = landForm.querySelector('[name="unit_value"]');
            const astInput = landForm.querySelector('[name="assessment_level"]');
            if (areaInput && valInput && astInput) {
                const mv = num(areaInput.value) * num(valInput.value);
                const useId = landForm.querySelector('[name="rpta_actual_use_id"]').value;
                const rate  = getRate(useId, mv);
                if (rate > 0) astInput.value = rate;
                const av = mv * num(astInput.value);
                const mvPrev = document.getElementById('land-mv-preview');
                const avPrev = document.getElementById('land-av-preview');
                if(mvPrev) mvPrev.innerText = fmt(mv);
                if(avPrev) avPrev.innerText = fmt(av);
            }
        }
    });

    // ── Component Edit Modal Handlers ───────────────────
    // Global state for spatial filtering
    let currentLandId = undefined;

    function openEditLandModal(id, useId, area, unitVal, astLvl, lot, blk, lat, lng, polygon) {
        currentLandId = id;
        const modal = document.getElementById('editLandModal');
        const form = modal.querySelector('form');
        form.action = "{{ url('rpt/faas/'.$faas->id.'/land') }}/" + id;
        form.querySelector('[name="rpta_actual_use_id"]').value = useId;
        form.querySelector('[name="area_sqm"]').value = area;
        form.querySelector('[name="unit_value"]').value = unitVal;
        form.querySelector('[name="assessment_level"]').value = astLvl;
        form.querySelector('[name="lot_no"]').value = lot;
        form.querySelector('[name="blk_no"]').value = blk;
        form.querySelector('[name="latitude"]').value = lat;
        form.querySelector('[name="longitude"]').value = lng;
        if (form.querySelector('[name="polygon_coordinates"]')) {
            form.querySelector('[name="polygon_coordinates"]').value = polygon ? polygon : '';
        }
        modal.classList.remove('hidden');
        form.dispatchEvent(new Event('input', { bubbles: true })); // Trigger live calc
    }

    function openEditBldgModal(id, useId, landId, area, costSqm, yrCon, yrApp, astLvl, materials) {
        const modal = document.getElementById('editBldgModal');
        const form = modal.querySelector('form');
        form.action = "{{ url('rpt/faas/'.$faas->id.'/building') }}/" + id;
        form.querySelector('[name="rpta_actual_use_id"]').value = useId;
        form.querySelector('[name="faas_land_id"]').value = landId;
        form.querySelector('[name="floor_area"]').value = area;
        form.querySelector('[name="construction_cost_per_sqm"]').value = costSqm;
        form.querySelector('[name="year_constructed"]').value = yrCon;
        form.querySelector('[name="year_appraised"]').value = yrApp;
        form.querySelector('[name="assessment_level"]').value = astLvl;
        form.querySelector('[name="construction_materials"]').value = materials;
        modal.classList.remove('hidden');
        form.dispatchEvent(new Event('input', { bubbles: true })); // Trigger live calc
    }

    function openEditMachModal(id, useId, name, cost, life, year, astLvl) {
        const modal = document.getElementById('editMachModal');
        const form = modal.querySelector('form');
        form.action = "{{ url('rpt/faas/'.$faas->id.'/machinery') }}/" + id;
        form.querySelector('[name="rpta_actual_use_id"]').value = useId;
        form.querySelector('[name="machine_name"]').value = name;
        form.querySelector('[name="original_cost"]').value = cost;
        form.querySelector('[name="useful_life"]').value = life;
        form.querySelector('[name="year_acquired"]').value = year;
        form.querySelector('[name="assessment_level"]').value = astLvl;
        modal.classList.remove('hidden');
        form.dispatchEvent(new Event('input', { bubbles: true })); // Trigger live calc
    }

    @if(session('open_tab'))
        document.addEventListener('DOMContentLoaded', function () {
            const tab = '{{ session('open_tab') }}';
            const panel = document.getElementById('panel-' + tab);
            if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    @endif
</script>
