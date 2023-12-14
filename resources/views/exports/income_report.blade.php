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

    $w_15 = $pdf ? "" : "width: 150px;";
    $w_26 = $pdf ? "" : "width: 260px;";
@endphp
@if ($pdf)
<style>table { font-family: Arial, sans-serif; }</style>
@endif
<table style="{{ $pdf ? "width: 100%;" : "" }}">
    <thead>
        <tr><th colspan="4" style="{{ $pdf ? "font-size: 20px;" : "font-size: 16px;" }} {{ $tx_b }} {{ $tx_c }}">Income Report</th></tr>
        <tr><th colspan="4" style="{{ $pdf ? "font-size: 15px;font-weight: 400;" : "font-size: 13px;" }} font-weight: 400; {{ $tx_c }}">For the date range of {{ $date_start }} to {{ $date_end }}</th></tr>
        <tr>
            <th colspan="4" style="{{ $pdf ? "font-size: 13px;font-weight: 400;" : "" }} {{ $tx_r }}">
                Date Printed:
                @if (!$pdf)
                {{ $date_printed }}
                @else
                <span style="{{ $tx_b }}">{{ $date_printed }}</span>
                @endif
            </th>
        </tr>
        <tr>
            <th colspan="4" style="{{ $pdf ? "font-size: 13px;font-weight: 400;" : "" }} {{ $tx_r }}">
                Printed by:
                @if (!$pdf)
                {{ $printed_by }}
                @else
                <span style="{{ $tx_b }}">{{ $printed_by }}</span>
                @endif
            </th>
        </tr>
        <tr><th colspan="4"></th></tr>
        <tr>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_26 }}">Student</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_26 }}">Service</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_15 }}">Price (PHP)</th>
            <th style="{{ $pdf ? "$cell_b $brdr_t" : "" }} {{ $brdr_b }} {{ $tx_c }} {{ $w_15 }}">Date Enrolled</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($list as $row)
        <tr>
            <td style="{{ $pdf ? "$cell_c" : "" }}">{{ $row['student_name'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }}">{{ $row['service'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_r }}">{{ $pdf ? number_format((float)$row['amount'], 2, '.', ',') : $row['amount'] }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $tx_c }}">{{ date('F jS, Y', strtotime($row['date_enrolled'])) }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $brdr_t }}"></td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $brdr_t }} {{ $tx_b }} {{ $tx_r }}">Total</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $brdr_t }} {{ $tx_b }} {{ $tx_r }}">{{ $pdf ? number_format((float)$total_income, 2, '.', ',') : $total_income }}</td>
            <td style="{{ $pdf ? "$cell_c" : "" }} {{ $brdr_t }}"></td>
        </tr>
    </tbody>
</table>
