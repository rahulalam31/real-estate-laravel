<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentController = new PaymentController();
    }

    public function testValidateCreateSessionRequest()
    {
        $request = new Request([
            'amount' => 1000,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        $this->assertTrue($this->paymentController->validateCreateSessionRequest($request));

        $invalidRequest = new Request([]);
        $this->assertFalse($this->paymentController->validateCreateSessionRequest($invalidRequest));
    }

    public function testSetStripeApiKey()
    {
        $expectedApiKey = 'test_stripe_api_key';
        config(['services.stripe.secret' => $expectedApiKey]);

        $stripeMock = $this->createMock(Stripe::class);
        $stripeMock->expects($this->once())
            ->method('setApiKey')
            ->with($expectedApiKey);

        $this->app->instance(Stripe::class, $stripeMock);

        $this->paymentController->setStripeApiKey();
    }

    public function testCreatePaymentIntent()
    {
        $amount = 1000;
        $currency = 'usd';

        $paymentIntentMock = $this->createMock(PaymentIntent::class);
        $paymentIntentMock->id = 'pi_123456789';
        $paymentIntentMock->client_secret = 'secret_123456789';

        PaymentIntent::shouldReceive('create')
            ->once()
            ->with([
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_types' => ['card'],
            ])
            ->andReturn($paymentIntentMock);

        $result = $this->paymentController->createPaymentIntent($amount, $currency);

        $this->assertEquals($paymentIntentMock->id, $result['id']);
        $this->assertEquals($paymentIntentMock->client_secret, $result['client_secret']);
    }

    public function testValidateHandlePaymentSuccessRequest()
    {
        $request = new Request([
            'payment_intent' => 'pi_123456789',
            'payment_intent_client_secret' => 'secret_123456789',
        ]);

        $this->assertTrue($this->paymentController->validateHandlePaymentSuccessRequest($request));

        $invalidRequest = new Request([]);
        $this->assertFalse($this->paymentController->validateHandlePaymentSuccessRequest($invalidRequest));
    }

    public function testCreateAndSaveTransaction()
    {
        $paymentIntentId = 'pi_123456789';
        $amount = 1000;
        $status = 'succeeded';

        $user = User::factory()->create();
        $this->actingAs($user);

        $transaction = $this->paymentController->createAndSaveTransaction($paymentIntentId, $amount, $status);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($paymentIntentId, $transaction->payment_intent_id);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals($status, $transaction->status);
        $this->assertEquals($user->id, $transaction->user_id);

        $this->assertDatabaseHas('transactions', [
            'payment_intent_id' => $paymentIntentId,
            'amount' => $amount,
            'status' => $status,
            'user_id' => $user->id,
        ]);
    }
}
