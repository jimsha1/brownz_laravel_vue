@extends('layouts.vcapp')

@section('content')
@php
    $cashier = \App\Models\User::find($user_id);
@endphp

@include('openCloseMoney.partials.close-register-form', [
    'is_force_close' => true,
    'page_title' => __('Force Close Register'),
    'page_hint' => __('Enter cash denomination counts to force-close this float session.'),
    'redirect_after_close' => url('/re_print_float_close_amt'),
    'cashier_name' => $cashier->name ?? null,
])
@endsection
