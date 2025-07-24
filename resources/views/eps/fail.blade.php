@extends('layouts.app')

@section('content')
<div class="p-5 bg-red-100 text-center">
    <h2>❌ Payment Failed</h2>
    <p>Invoice: {{ $trx->invoice_id }}</p>
</div>
@endsection
