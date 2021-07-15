<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class AuthController extends Controller
{

    public function MicrosoftRedirect()
    {
        // Initialize the OAuth client
        $oauthClient = new GenericProvider([
            'clientId'                => config('azure.appId'),
            'clientSecret'            => config('azure.appSecret'),
            'redirectUri'             => config('azure.redirectUri'),
            'urlAuthorize'            => config('azure.authority') . config('azure.authorizeEndpoint'),
            'urlAccessToken'          => config('azure.authority') . config('azure.tokenEndpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => config('azure.scopes')
        ]);

        return $oauthClient->authorize();
    }

    public function MicrosoftCallback(Request $request)
    {
        // Authorization code should be in the "code" query param
        $authCode = $request->query('code');
        if (isset($authCode)) {
            // Initialize the OAuth client
            $oauthClient = new GenericProvider([
                'clientId'                => config('azure.appId'),
                'clientSecret'            => config('azure.appSecret'),
                'redirectUri'             => config('azure.redirectUri'),
                'urlAuthorize'            => config('azure.authority') . config('azure.authorizeEndpoint'),
                'urlAccessToken'          => config('azure.authority') . config('azure.tokenEndpoint'),
                'urlResourceOwnerDetails' => '',
                'scopes'                  => config('azure.scopes')
            ]);
                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $authCode
                ]);
                dd($accessToken);
        }
    }


    public function microsoftAuth(Request $request)
    {
        try {
            $graph = new Graph();
            $graph->setAccessToken($request->accessToken);

            $user = $graph->createRequest('GET', '/me')
                ->setReturnType(Model\User::class)
                ->execute();
            return $this->authenticateUser($user->getProperties());
        } catch (\Exception $e) {
            $errorArray[] = [
                'status' => 401,
                "title" => "Microsoft user token",
                "detail" => 'token not valid'
            ];
            return Response()->json(['errors' => $errorArray]);
        }
    }

    //userPrincipalName is the Email
    public function authenticateUser($microsoftUser)
    {

        $user = User::where('microsoft_id', $microsoftUser['id'])->first();
        //if the user didn't have Microsoft ID
        if (is_null($user)) {
            if ($microsoftUser['userPrincipalName'] && User::where('email', $microsoftUser['userPrincipalName'])->exists()) {
                $user = User::where('email', $microsoftUser['userPrincipalName'])->first();
                $user->microsoft_id = $microsoftUser['id'];
                $user->save();
            } else {
                $user = User::create([
                    'email' => $microsoftUser['userPrincipalName'],
                    'password' => bcrypt(rand(1000000, 9000000)),
                    'name' => $microsoftUser['displayName'],
                    'email_verified_at' => Carbon::now(),
                    "microsoft_id" => $microsoftUser['id'],
                ]);
            }
        }

        if ($token = Auth::guard('api')->login($user, $user)) {
            return $this->respondWithTokenWithCompanyConfig($token, $user);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    protected function respondWithTokenWithCompanyConfig($token, $user)
    {
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
