@extends('frontend.main-template')
@section('title')
    Payment
@endsection
@section('custom-css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" /> --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('main-content')
<script src="https://js.stripe.com/v3/"></script>
<?php $totalamt = 0; ?>
@foreach ($cartallDetail as $cartallDetails)
        <?php $totalamt += $cartallDetails->price * $cartallDetails->quanitiy; ?>
@endforeach
<div class="section_margin">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="checkout_container">
                        <div class="d-flex flex-column">
                            <div class="row">
                                <div class="col-md-7 px-3">
                                    <div class="progress-container mb-5">
                                        <div class="progress" id="progress"></div>
                                        <div class="circle  number_1 active check">
                                            <div class="caption">Details</div>
                                        </div>
                                        <div class="circle current_tab active  number_2">
                                            <div class="caption">Payment</div>
                                        </div>
                                        <div class="circle number_3">
                                            <div class="caption">Completion</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout-content checkout-main">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="w-100 py-2">
                                            @if (session('success_msg'))
                                                <div class="alert alert-success">{{ session('success_msg') }}</div>
                                            @endif
                                            @if (session('error_msg'))
                                                <div class="alert alert-danger">{{ session('error_msg') }}</div>
                                            @endif

                                            <form id="payment-form" method="post" action="{{ route('process.payment') }}" class="form-inline form_payment form_checkout">
                                                @csrf
                                                <input type="hidden" name="payment_method_id" id="payment_method_id">
                                                <input type="hidden" name="req_amt" value="{{ number_format($totalamt, 2, '.', '') }}" id="req_amt">
                                                <!-- Your form fields -->
                                                <div class="row mb-3">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="cardholderName">Cardholder Name</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                                <input type="text" name="cardholderName" class="form-control" id="cardholderName"
                                                                       value="{{ auth()->guard('registration')->user()->name }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="amount">Amount</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                                <input type="text" value="{{ number_format($totalamt, 2) }}" name="amount" class="form-control" id="amount" required readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="card-element">Credit or debit card</label>
                                                            <div id="card-element" class="form-control">
                                                                <!-- A Stripe Element will be inserted here. -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <button class="btn btn-primary" id="submit-payment-btn" type="submit">
                                                            <i class="fas fa-credit-card"></i> Make Payment
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                            {{-- <form id="payment-form" method="post" action="{{ route('process.payment') }}" class="form-inline form_payment form_checkout">
                                                @csrf
                                                <input type="hidden" name="stripeToken" id="stripeToken">
                                                <input type="hidden" name="payment_method_id" id="payment_method_id"> <!-- Add hidden input field for payment_method_id -->
                                                <!-- Add your other form fields for cardholder name, amount, card number, CVV, expiration month, and year -->
                                                <!-- Make sure to include appropriate IDs for each input field -->
                                                <!-- Example: -->
                                                <div class="form-group">
                                                    <label for="cardholderName">Cardholder Name</label>
                                                    <input type="text" name="cardholderName" class="form-control" id="cardholderName" value="{{ auth()->guard('registration')->user()->name }}" required>
                                                </div>
                                                <!-- Add other form fields -->

                                                <div id="card-element">
                                                    <!-- A Stripe Element will be inserted here. -->
                                                </div>

                                                <div id="card-errors" role="alert"></div>

                                                <button class="btn btn-success" id="submit-payment-btn" type="submit">Pay Now</button>
                                            </form> --}}
                                        </div>
                                    </div>
                                    <!-- Right Column: Add your content here -->
                                    <div class="col-md-5 py-2">
                                        <div class="border_summary">
                                            <h4 class="mb-3">Order Summary</h4>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="order-summary-table">
                                                    <?php $total = 0; ?>
                                                    @foreach ($cartallDetail as $cartallDetails)
                                                        <tr>
                                                            <td>{{ $cartallDetails->title }}</td>
                                                            <td>{{ $cartallDetails->quanitiy }}</td>
                                                            <td>{{ number_format($cartallDetails->price * $cartallDetails->quanitiy, 2) }}
                                                            </td>
                                                            <?php $total += $cartallDetails->price * $cartallDetails->quanitiy; ?>
                                                        </tr>
                                                    @endforeach
                                                    <!-- Order summary items will be added here -->
                                                </tbody>
                                            </table>
                                            <h6 id="total-amount">Total: $ {{ number_format($total, 2) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<div class="bottom-statement footer">
    <p>Privacy & Cookies Statement</p>
    <p>© 1903 Aerospace Corp. All rights reserved.</p>
</div>

@section('custom-js')
<script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.all.min.js"></script>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        var card = elements.create('card', { style: style });
        card.mount('#card-element');

        var form = document.getElementById('payment-form');
        var cardholderName = document.getElementById('cardholderName');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
                billing_details: {
                    name: cardholderName.value,
                },
            });

            if (error) {
                // Display error.message in your UI
                console.error(error.message);
            } else {
                // Send Payment Method ID to your server
                const response = await fetch('/process-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        payment_method_id: paymentMethod.id,
                        req_amt: document.getElementById('amount').value // Ensure you have an element with ID 'amount'
                    }),
                }).then((res) => res.json());

                if (response.error) {
                    // Display error in your UI
                    console.error(response.error);
                } else if (response.requires_action) {
                    // Handle 3D Secure authentication
                    const { error: confirmationError, paymentIntent } = await stripe.confirmCardPayment(response.payment_intent_client_secret);

                    if (confirmationError) {
                        // Display error in your UI
                        console.error(confirmationError.message);
                    } else if (paymentIntent.status === 'succeeded') {
                        // Payment successful
                        alert('Payment successful!');
                        // Redirect to success URL or handle success
                        window.location.href = 'https://aerospace-corp.test/payment-success';
                    } else {
                        // Handle other statuses
                        console.error('Payment not successful. Status: ' + paymentIntent.status);
                    }
                } else {
                    // Payment successful without additional action
                    alert('Payment successful!');
                    // Redirect to success URL or handle success
                    window.location.href = 'https://aerospace-corp.test/payment-success';
                }
            }
        });
    });
    </script>

</body>

</html>
