@extends('layouts.app')

@section('content')
<div class="p-5 bg-green-100 text-center">
    <h2>✅ Payment Successful</h2>
    <p>Invoice: {{ $trx->invoice_id }}</p>
    <p>Amount: ৳{{ $trx->amount }}</p>
</div>
@endsection
