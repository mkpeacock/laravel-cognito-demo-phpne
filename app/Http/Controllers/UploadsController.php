<?php

namespace App\Http\Controllers;

use Aws\CognitoIdentity\CognitoIdentityClient;
use Illuminate\Support\Facades\Auth;

class UploadsController extends Controller
{
    protected $cognitoIdentityClient;

    public function __construct(CognitoIdentityClient $cognitoIdentityClient)
    {
        $this->cognitoIdentityClient = $cognitoIdentityClient;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function create()
        {
            $requestParams =[
                'IdentityPoolId' => config('services.cognito.identity_pool_id'),
                'Logins'=>[
                    // This doesn't like ID numbers, so prefix it
                    config('services.cognito.developer_authentication_name') => env('APP_ENV') . '/' . Auth::user()->id,
                ],
                'TokenDuration' => 60*60*6,
            ];

            $cognitoIdentity = $this->cognitoIdentityClient
                ->getOpenIdTokenForDeveloperIdentity($requestParams)
                ->toArray();

            $cognitoIdentityId = $cognitoIdentity['IdentityId'];
            $cognitoToken = $cognitoIdentity['Token'];
            $bucketName = config('services.s3.bucket');
            $bucketRegion = config('services.s3.region');
            $identityPoolId = config('services.cognito.identity_pool_id');
            $bucketPrefix = 'user-uploads/' . $cognitoIdentityId . '/';

            return view(
                'uploads.create', compact('cognitoIdentityId', 'cognitoToken', 'bucketName', 'bucketRegion',
                    'identityPoolId', 'bucketPrefix'
                )
            );
        }
}
