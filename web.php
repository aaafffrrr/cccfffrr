Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('process.payment');
Route::get('/payment-success', [PaymentController::class, 'success'])->name('payment.success');
