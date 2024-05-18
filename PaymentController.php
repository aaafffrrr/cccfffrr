<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\PaymentLog;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Services\QuickBooksService;
use Illuminate\Support\Facades\Session;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\controller as AnetController;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\controller\CreateTransactionController;
use App\Models\CartProduct;
use Stripe\StripeClient;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Charge;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        // Define $paymentIntentResponse variable here
        $paymentIntentResponse = // Your code to obtain $paymentIntentResponse variable

        // Define $cartallDetail variable here
        $cartallDetail = CartProduct::where('user_id', auth()->guard('registration')->user()->id)->get();

       //dd($cartallDetail);

        // Pass both variables to the view
        return view('frontend.payment', compact('paymentIntentResponse', 'cartallDetail'));
    }
    public function processPayment(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $amountInCents = (int) ($request->req_amt * 100);

        try {
            // Create a Customer
            $customer = \Stripe\Customer::create([
                'name' => 'Parwez Usmani', // Customer's name
                'address' => [
                    'city' => 'City', // Customer's city
                    'country' => 'IN', // Customer's country code (IN for India)
                    'line1' => 'Street Address', // Customer's street address
                    'postal_code' => '123456', // Customer's postal code
                    'state' => 'State', // Customer's state
                ],
            ]);

            // Create a Payment Intent with automatic confirmation method
            $paymentIntent = PaymentIntent::create([
                'payment_method' => $request->payment_method_id,
                'amount' => $amountInCents,
                'currency' => 'inr',
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'description' => 'Payment for XYZ',
                'return_url' => route('payment.success'), // Adjust route if necessary
                'customer' => $customer->id,
            ]);

            // Handle different payment statuses
            if ($paymentIntent->status === 'requires_action' || $paymentIntent->status === 'requires_source_action') {
                // Payment requires additional action (3DS authentication)
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_client_secret' => $paymentIntent->client_secret,
                ]);
            } elseif ($paymentIntent->status === 'succeeded') {
                // Payment is successful
                // Handle post-payment logic, such as updating order status
                return response()->json(['success' => true]);
            } else {
                // Payment failed
                return response()->json(['error' => 'Invalid PaymentIntent status'], 500);
            }
        } catch (\Exception $e) {
            // Handle payment failure
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

public function success(){
    return view('frontend.thank-you');
}


}
