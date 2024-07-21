Route::get('/properties', PropertyList::class);
Route::post('/bookings', [\App\Http\Controllers\BookingController::class, 'store']);
Route::put('/bookings/{booking}', [\App\Http\Controllers\BookingController::class, 'update']);
Route::get('/bookings', [\App\Http\Controllers\BookingController::class, 'index']);
Route::get('/properties/{property}/book', [\App\Http\Livewire\PropertyBooking::class])->name('property.book');
Route::post('/payments/session', [\App\Http\Controllers\PaymentController::class, 'createSession']);
Route::get('/payments/success', [\App\Http\Controllers\PaymentController::class, 'handlePaymentSuccess']);
Route::get('/booking-calendar', [\App\Http\Livewire\BookingCalendar::class])->middleware('auth')->name('booking.calendar');

Route::get('/properties/{propertyId}', [\App\Http\Livewire\PropertyDetail::class])->name('property.detail');

Route::get('/properties/compare/{propertyIds}', PropertyComparison::class)->name('property.compare');

require __DIR__.'/socialstream.php';require __DIR__.'/socialstream.php';