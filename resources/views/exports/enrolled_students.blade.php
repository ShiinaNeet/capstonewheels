@php
    $brdr_b = "border-bottom: 0.5px solid #000000;";
    $brdr_l = "border-left: 0.5px solid #000000;";
    $brdr_r = "border-right: 0.5px solid #000000;";
    $brdr_t = "border-top: 0.5px solid #000000;";

    $tx_b = "font-weight: 600;";
    $tx_c = "text-align: center;";
    $tx_r = "text-align: right;";

    $cell_b = "font-size: 13px;vertical-align: bottom;";
    $cell_c = "font-size: 13px;vertical-align: middle;";

    $w_10 = $pdf ? "" : "width: 100px;";
    $w_15 = $pdf ? "" : "width: 150px;";
    $w_20 = $pdf ? "" : "width: 200px;";
    $w_25 = $pdf ? "" : "width: 250px;";
@endphp
@if ($pdf)
<style>table { font-family: Arial, sans-serif; }</style>
@endif
<table style="{{ $pdf ? "width: 100%;" : "" }}">
    <thead>
        <tr><th colspan="{{ ($exclude_columns ? "11" : "14") }}" style="{{ $pdf ? "font-size: 20px;" : "font-size: 16px;" }} {{ $tx_b }} {{ $tx_c }}">List of Graduates on {{ $service }}</th></tr>
        <tr><th colspan="{{ ($exclude_columns ? "11" : "14") }}" style="{{ $pdf ? "font-size: 15px;font-weight: 400;" : "font-size: 13px;" }}  {{ $tx_c }}">For the month of {{ $month }}</th></tr>
        <tr>
            <th colspan="{{ ($exclude_columns ? "11" : "14") }}" style="{{ $pdf ? "font-size: 13px;font-weight: 400;" : "" }} {{ $tx_r }}">
                Date Printed:
                @if (!$pdf)
                {{ $date_printed }}
                @else
                <span style="{{ $tx_b }}">{{ $date_printed }}</span>
                @endif
            </th>
        </tr>
        <tr>
            <th colspan="{{ ($exclude_columns ? "11" : "14") }}" style="{{ $pdf ? "font-size: 13px;font-weight: 400;" : "" }} {{ $tx_r }}">
                Printed by:
                @if (!$pdf)
                {{ $printed_by }}
                @else
                <span style="{{ $tx_b }}">{{ $printed_by }}</span>
                @endif
            </th>
        </tr>
        <tr><th colspan="{{ ($exclude_columns ? "11" : "14") }}"></th></tr>
        <tr>
            <th rowspan="2" style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">No.</th>
            <th colspan="3" style="{{ $pdf ? "$cell_b $brdr_t font-weight: 400;" : "" }} {{ $tx_c }}">Name</th>
            <th rowspan="2" style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">Birthday</th>
            <th rowspan="2" style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">Sex</th>
            <th rowspan="2" style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">Hours</th>
            <th rowspan="2" style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">Clutch</th>
            <th colspan="2" style="{{ $pdf ? "$cell_b $brdr_t font-weight: 400;" : "" }} {{ $tx_c }}">Date of Attendance</th>
            @if (!$exclude_columns)
            <th colspan="2" style="{{ $pdf ? "$cell_b $brdr_t font-weight: 400;" : "" }} {{ $tx_c }}">Date Printed of Certificates</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t font-weight: 400;" : "" }} {{ $tx_c }}">Certificate Control</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t font-weight: 400;" : "" }} {{ $tx_c }}">Certificate</th>
            @endif
            <th rowspan="2" style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_25 }}">Instructor</th>
        </tr>
        <tr>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_15 }}">Lastname</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_15 }}">Firstname</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_15 }}">Middlename</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">Start</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">End</th>
            @if (!$exclude_columns)
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">LTMS</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">ACES</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_25 }}">Number</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_10 }}">Status</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($list as $key => $row)
        <tr>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ ($key + 1) }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }}">{{ $row['lastname'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }}">{{ $row['firstname'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }}">{{ $row['middlename'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ date('m/d/Y', strtotime($row['birthdate'])) }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ $row['gender'] == 1 ? "Male" : "Female" }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ $row['hours'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ $row['transmission'] == "-" ? "" : ($row['transmission'] == 0 ? "Automatic" : "Manual") }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ date('m/d/Y', strtotime($row['date_start'])) }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ date('m/d/Y', strtotime($row['date_end'])) }}</td>
            @if (!$exclude_columns)
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ $row['LTMS'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ $row['ACES'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ $row['CCM'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ $row['certificate_status'] }}</td>
            @endif
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ $row['instructor'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
