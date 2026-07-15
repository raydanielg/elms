<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\PaymentManager;
use App\Services\Sms\SmsManager;

class GatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function ($app) {
            $manager = new PaymentManager();
            $manager->registerDriver('stripe', \App\Services\Payment\Drivers\StripeDriver::class);
            $manager->registerDriver('paypal', \App\Services\Payment\Drivers\PaypalDriver::class);
            $manager->registerDriver('mpesa', \App\Services\Payment\Drivers\MpesaDriver::class);
            $manager->registerDriver('flutterwave', \App\Services\Payment\Drivers\FlutterwaveDriver::class);
            return $manager;
        });

        $this->app->singleton(SmsManager::class, function ($app) {
            $manager = new SmsManager();
            $manager->registerDriver('africas_talking', \App\Services\Sms\Drivers\AfricasTalkingDriver::class);
            $manager->registerDriver('beem_africa', \App\Services\Sms\Drivers\BeemAfricaDriver::class);
            $manager->registerDriver('twilio', \App\Services\Sms\Drivers\TwilioDriver::class);
            return $manager;
        });
    }
}
