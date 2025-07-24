@extends('layouts.app')

@section('content')
<div class="container">
    <h2>EPS Sandbox Payment</h2>
    <form method="POST" action="{{ route('eps.initiate') }}">
        @csrf
        <input type="text" name="name" placeholder="Your Name" required class="form-control mb-2">
        <input type="email" name="email" placeholder="Your Email" class="form-control mb-2">
        <input type="number" step="0.01" name="amount" placeholder="Amount" required class="form-control mb-2">
        <button type="submit" class="btn btn-primary">Pay with EPS</button>
    </form>
</div>
@endsection
