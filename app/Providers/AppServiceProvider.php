<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(S3Client::class, function ($app) {
            return new S3Client([
                'version' => 'latest',
                'region' => config('services.s3.region'),
                'credentials' => new Credentials(
                    config('services.s3.key'),
                    config('services.s3.secret')
                ),
            ]);
        });
    }
}
