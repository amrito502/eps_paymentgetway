<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<div class="container">
    <div class="card text-center border-primary mx-auto" style="max-width: 600px; margin: 5rem auto; padding: 2rem; border-radius: 1rem;">
        <h2 class="mb-4 text-primary">EPS Payment</h2>
        <p class="mb-3">Click below to start a test payment.</p>
        <a href="{{ route('eps.initiate') }}" class="btn btn-primary btn-lg">Pay Now</a>
    </div>

    <!-- Developer credit line -->
    <div class="text-center mt-5 mb-3 text-muted" style="font-size: 1rem; line-height: 1.4;">
        Developed By <strong>Emon Hossain</strong><br />
        Software Engineer<br />
        Eps - Easy Payment System
    </div>
</div>

