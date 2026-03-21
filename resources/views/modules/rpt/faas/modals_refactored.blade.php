{{-- modals_refactored.blade.php refactored to include individual modals --}}

@include('modules.rpt.faas.modals._add_land')
@include('modules.rpt.faas.modals._add_building')
@include('modules.rpt.faas.modals._add_machinery')

@include('modules.rpt.faas.modals._edit_land')
@include('modules.rpt.faas.modals._edit_building')
@include('modules.rpt.faas.modals._edit_machinery')
@include('modules.rpt.faas.modals._edit_master')

@include('modules.rpt.faas.modals._transfer')
@include('modules.rpt.faas.modals._subdivide')

@include('modules.rpt.faas.modals._return_draft')
@include('modules.rpt.faas.modals._cancel_faas')
@include('modules.rpt.faas.modals._manage_attachments')
@include('modules.rpt.faas.modals._document_preview')
