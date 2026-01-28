<x-admin.app>
    @include('layouts.rpt.navigation')
    RPTA Settings Content Here
    <br>
    <a href="{{route('rpt.actual-use.index')}}">Actual Use</a>
    <br>
    <a href="{{route('rpt.additional-items.index')}}">Additional Items</a>
        <br>

    <a href="{{ route('rpt.assessment-levels.index') }}">Assessment Level</a>
        <br>

    <a href="#">Barangay Setup</a>
        <br>

    <a href="#">Bulk CARP Status Update per Brgy</a>
        <br>

    <a href="{{ route('rpt.classifications.index') }}">Classification</a>
        <br>

    <a href="#">Depreciation Rate for Buildings</a>
        <br>

    <a href="#">Owner Selections</a>
        <br>

    <a href="#">Other Improvements for Land</a>
        <br>

    <a href="#">Signatories & Revision Year</a>
        <br>

    <a href="#">Transaction Code</a>
        <br>

    <a href="#">Update Local Map (Generated via QGIS)   </a>
        <br>

    <a href="#">Update PIN / Year for RPU</a>
        <br>

    <a href="#">General Revision of RPUs</a>
        <br>

    <a href="#">Public Auction Identification for RPTA</a>


</x-admin.app>