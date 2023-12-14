@extends('layouts.app')

@section('css')
    @isset($css)
        @foreach($css as $c)
            <link href="{{ asset('styles/'.$c.'.css') }}" rel="stylesheet" type="text/css" />
        @endforeach
    @endisset
@endsection

@section('content')
    <student-enrollment-form :service-id="{{ json_encode($service_id) }}" :batch="{{ json_encode($batch) }}" />
@endsection
