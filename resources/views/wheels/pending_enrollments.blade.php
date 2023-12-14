@extends('layouts.app')

@section('css')
    @isset($css)
        @foreach($css as $c)
            <link href="{{ asset('styles/'.$c.'.css') }}" rel="stylesheet" type="text/css" />
        @endforeach
    @endisset
@endsection

@section('content')
    <pending-enrollments />
@endsection
