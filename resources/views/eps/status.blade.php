<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<div class="container">
    @php
        $color = "secondary";
        $icon = "ℹ";
        $msg = "Payment Status Unknown<br>We could not determine the payment status. Please contact support.";

        if ($status === 'SUCCESS' || $status === 'COMPLETED')  {
            $color = "success";
            $icon = "✔";
            $msg = "Payment successful";
        } elseif ($status === 'FAILED' || $status === 'FAILURE') {
            $color = "danger";
            $icon = "✖";
            $msg = "Payment failed";
        } elseif ($status === 'CANCEL' || $status === 'CANCELED') {
            $color = "warning";
            $icon = "⚠";
            $msg = "Payment cancelled";
        }
    @endphp

    <div class="card border-{{ $color }} text-center mx-auto" style="max-width: 600px; margin: 5rem auto; padding: 2rem; border-radius: 1rem;">
        <div class="icon text-{{ $color }} mb-3" style="font-size: 3rem;">{{ $icon }}</div>
        <h3 class="text-{{ $color }}">{!! $msg !!}</h3>
        @if($transactionId)
            <p class="text-muted">Transaction ID: <code>{{ $transactionId }}</code></p>
        @endif
        <a href="{{ route('eps.payment') }}" class="btn btn-outline-{{ $color }} mt-3">Try Again</a>
    </div>

    <!-- Developer credit line -->
    <div class="text-center mt-5 mb-3 text-muted" style="font-size: 1rem; line-height: 1.4;">
        Developed By <strong>Emon Hossain</strong><br />
        Software Engineer<br />
        Eps - Easy Payment System
    </div>
</div>

