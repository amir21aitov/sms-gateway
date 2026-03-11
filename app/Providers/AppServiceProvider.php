<?php

namespace App\Providers;

use App\Services\Sms\Providers\EskizProvider;
use App\Services\Sms\Providers\FakeProvider;
use App\Services\Sms\Providers\PlaymobileProvider;
use App\Services\Sms\Providers\TwilioProvider;
use App\Services\Sms\SmsProviderFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsProviderFactory::class);

        $this->app->bind(EskizProvider::class, function () {
            return new EskizProvider(config('sms.providers.eskiz'));
        });

        $this->app->bind(PlaymobileProvider::class, function () {
            return new PlaymobileProvider(config('sms.providers.playmobile'));
        });

        $this->app->bind(TwilioProvider::class, function () {
            return new TwilioProvider(config('sms.providers.twilio'));
        });

        $this->app->bind(FakeProvider::class, function () {
            return new FakeProvider();
        });
    }

    public function boot(): void
    {
        //
    }
}
