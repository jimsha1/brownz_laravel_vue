@extends('layouts.vcapp')

@section('content')
@include('openCloseMoney.partials.close-register-form', [
    'is_edit_close' => true,
    'page_title' => __('Edit Closed Count'),
    'page_hint' => __('Update the cash count for this closed register. Sales, open time, and close time stay the same.'),
    'summary_hint' => __('Review the count, then save. Sales and close time will not change.'),
    'form_action' => route('update.closed.float'),
    'submit_label' => __('Save Count'),
    'submit_icon' => 'fa-save',
    'status_label' => __('Closed'),
    'status_class' => 'is-force',
    'redirect_after_close' => route('re.print.bill'),
    'cashier_name' => $cashier->name ?? null,
    'prefill_total' => $prefill_total ?? null,
])
@endsection
