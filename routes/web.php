<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    // Ignores notices and reports all other kinds... and warnings
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    // error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}

App::singleton('oauth2', function () {
    $storage = new OAuth2\Storage\Pdo(DB::connection()->getPdo());
    // specify your audience (typically, the URI of the oauth server)
    // $issuer = env('URI', false);
    $issuer = URL::to('/');
    $audience = 'https://' . $issuer;
    $config['use_openid_connect'] = true;
    $config['issuer'] = $issuer;
    $config['allow_implicit'] = true;
    $config['use_jwt_access_tokens'] = true;
    $config['refresh_token_lifetime'] = 0;
    $refresh_config['always_issue_new_refresh_token'] = false;
    $refresh_config['unset_refresh_token_after_use'] = false;
    // create server
    $server = new OAuth2\Server($storage, $config);
    if (env('DOCKER') == '1') {
        $publicKey = env('PUBKEY');
        $privateKey = env('PRIVKEY');
    } else {
        $publicKey  = File::get(base_path() . "/.pubkey.pem");
        $privateKey = File::get(base_path() . "/.privkey.pem");
    }
    // create storage for OpenID Connect
    $keyStorage = new OAuth2\Storage\Memory(['keys' => [
        'public_key'  => $publicKey,
        'private_key' => $privateKey
    ]]);
    $server->addStorage($keyStorage, 'public_key');
    // set grant types
    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
    $server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
    $server->addGrantType(new OAuth2\OpenID\GrantType\AuthorizationCode($storage));
    $server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, $refresh_config));
    $server->addGrantType(new OAuth2\GrantType\JwtBearer($storage, $audience));
    return $server;
});

// Core pages
Route::any('install', ['as' => 'install', 'uses' => 'OauthController@install']);
Route::any('setup_mail', ['as' => 'setup_mail', 'uses' => 'OauthController@setup_mail']);
Route::get('setup_mail_test', ['as' => 'setup_mail_test', 'uses' => 'OauthController@setup_mail_test']);
Route::get('/', ['as' => 'welcome', 'uses' => 'OauthController@welcome']);
Route::get('welcome0', ['as' => 'welcome0', 'uses' => 'OauthController@welcome0']);
Route::get('welcome1', ['as' => 'welcome1', 'uses' => 'OauthController@welcome1']);
Route::get('privacy_policy', ['as' => 'privacy_policy', 'uses' => 'OauthController@privacy_policy']);
Route::any('patients/{create?}', ['as' => 'patients', 'uses' => 'OauthController@patients']);
Route::get('clinicians', ['as' => 'clinicians', 'uses' => 'OauthController@clinicians']);
Route::get('others', ['as' => 'others', 'uses' => 'OauthController@others']);
Route::any('container_create/{code?}', ['as' => 'container_create', 'uses' => 'OauthController@container_create']);
Route::any('invite_cancel/{code}/{redirect?}', ['as' => 'invite_cancel', 'uses' => 'OauthController@invite_cancel']);
Route::get('invitation_list', ['as' => 'invitation_list', 'uses' => 'HomeController@invitation_list']);
Route::get('key_download/{file}', ['as' => 'key_download', 'uses' => 'OauthController@key_download']);
Route::any('search_welcome', ['as' => 'search_welcome', 'uses' => 'OauthController@search_welcome']);
Route::any('metadata/{type}', ['as' => 'metadata', 'uses' => 'OauthController@metadata']);
Route::get('check', ['as' => 'check', 'uses' => 'OauthController@check']);
Route::get('check_as', ['as' => 'check_as', 'uses' => 'OauthController@check_as']);
Route::post('mailgun', ['as' => 'mailgun', 'uses' => 'OauthController@mailgun']);
Route::any('login', ['as' => 'login', 'uses' => 'OauthController@login']);
Route::any('logout', ['as' => 'logout', 'uses' => 'OauthController@logout']);
Route::post('login_uport/{admin?}', ['as' => 'login_uport', 'middleware' => 'csrf', 'uses' => 'OauthController@login_uport']);
Route::any('uport_user_add', ['as' => 'uport_user_add', 'uses' => 'OauthController@uport_user_add']);
Route::any('remote_logout', ['as' => 'remote_logout', 'uses' => 'OauthController@remote_logout']);
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@index']);
Route::get('all_patients', ['as' => 'all_patients', 'uses' => 'HomeController@all_patients']);
Route::post('add_patient', ['as' => 'add_patient', 'middleware' => 'csrf', 'uses' => 'HomeController@add_patient']);
Route::post('remove_patient', ['as' => 'remove_patient', 'middleware' => 'csrf', 'uses' => 'HomeController@remove_patient']);
Route::any('search', ['as' => 'search', 'uses' => 'OauthController@search']);
Route::get('reports', ['as' => 'reports', 'uses' => 'HomeController@reports']);
Route::get('activity_logs', ['as' => 'activity_logs', 'uses' => 'HomeController@activity_logs']);
Route::get('resources/{id}', ['as' => 'resources', 'uses' => 'HomeController@resources']);
Route::get('login_authorize', ['as' => 'login_authorize', 'uses' => 'HomeController@login_authorize']);
Route::get('login_authorize_action/{type}', ['as' => 'login_authorize_action', 'uses' => 'HomeController@login_authorize_action']);
Route::any('client_register', ['as' => 'client_register', 'uses' => 'OauthController@client_register']);
Route::any('oauth_login', ['as' => 'oauth_login', 'uses' => 'OauthController@oauth_login']);
Route::get('clients', ['as' => 'clients', 'uses' => 'HomeController@clients']);
Route::get('users', ['as' => 'users', 'uses' => 'HomeController@users']);
Route::get('resource_view/{type}', ['as' => 'resource_view', 'uses' => 'HomeController@resource_view']);
Route::any('settings', ['as' => 'settings', 'uses' => 'HomeController@settings']);
Route::any('uma_aat', ['as' => 'uma_aat', 'uses' => 'HomeController@uma_aat']);
Route::any('uma_api', ['as' => 'uma_api', 'uses' => 'HomeController@uma_api']);
Route::any('uma_aat_search', ['as' => 'uma_aat_search', 'uses' => 'HomeController@uma_aat_search']);
Route::any('uma_api_search', ['as' => 'uma_api_search', 'uses' => 'HomeController@uma_api_search']);
Route::any('uma_auth', ['as' => 'uma_auth', 'uses' => 'OauthController@uma_auth']);
Route::any('uma_list', ['as' => 'uma_list', 'uses' => 'HomeController@uma_list']);
Route::any('uma_register', ['as' => 'uma_register', 'uses' => 'OauthController@uma_register']);
Route::any('uma_register_auth', ['as' => 'uma_register_auth', 'uses' => 'HomeController@uma_register_auth']);
Route::any('uma_register_url', ['as' => 'uma_register_url', 'uses' => 'OauthController@uma_register_url']);
Route::get('uma_resources/{id}', ['as' => 'uma_resources', 'uses' => 'HomeController@uma_resources']);
Route::get('uma_resource_view/{type}', ['as' => 'uma_resource_view', 'uses' => 'HomeController@uma_resource_view']);
Route::any('directory_auth', ['as' => 'directory_auth', 'uses' => 'OauthController@directory_auth']);
Route::get('directory_check/{id}', ['as' => 'directory_check', 'uses' => 'OauthController@directory_check']);
Route::any('directory_default_policy_type', ['as' => 'directory_default_policy_type', 'uses' => 'OauthController@directory_default_policy_type']);
Route::any('directory_registration/{id?}', ['as' => 'directory_registration', 'uses' => 'OauthController@directory_registration']);
Route::post('directory_remove/{id}', ['as' => 'directory_remove', 'uses' => 'OauthController@directory_remove']);
Route::post('directory_update/{id}', ['as' => 'directory_update', 'uses' => 'OauthController@directory_update']);
Route::any('signup', ['as' => 'signup', 'uses' => 'OauthController@signup']);
Route::any('signup_confirmation/{code}', ['as' => 'signup_confirmation', 'uses' => 'OauthController@signup_confirmation']);
Route::any('signup_hieofone', ['as' => 'signup_hieofone', 'uses' => 'OauthController@signup_hieofone']);
Route::any('support', ['as' => 'support', 'uses' => 'OauthController@support']);
Route::any('support_oauth', ['as' => 'support_oauth', 'uses' => 'OauthController@support_oauth']);
Route::any('oidc_relay/{state?}', ['as' => 'oidc_relay', 'uses' => 'OauthController@oidc_relay']);
Route::any('oidc_relay_start/{state}', ['as' => 'oidc_relay_start', 'uses' => 'OauthController@oidc_relay_start']);
Route::any('oidc_relay_connect', ['as' => 'oidc_relay_connect', 'uses' => 'OauthController@oidc_relay_connect']);
// Route::get('change_permission/{id}', ['as' => 'change_permission', 'uses' => 'HomeController@change_permission']);
// Route::get('change_permission_add_edit/{id}', ['as' => 'change_permission_add_edit', 'uses' => 'HomeController@change_permission_add_edit']);
// Route::get('change_permission_remove_edit/{id}', ['as' => 'change_permission_remove_edit', 'uses' => 'HomeController@change_permission_remove_edit']);
// Route::get('change_permission_delete/{id}', ['as' => 'change_permission_delete', 'uses' => 'HomeController@change_permission_delete']);
// Route::get('consents_resource_server', ['as' => 'consents_resource_server', 'uses' => 'HomeController@consents_resource_server']);
// Route::get('authorize_resource_server', ['as' => 'authorize_resource_server', 'uses' => 'HomeController@authorize_resource_server']);
// Route::post('rs_authorize_action', ['as' => 'rs_authorize_action', 'uses' => 'HomeController@rs_authorize_action']);
// Route::get('authorize_client', ['as' => 'authorize_client', 'uses' => 'HomeController@authorize_client']);
// Route::get('authorize_client_action/{id}', ['as' => 'authorize_client_action', 'uses' => 'HomeController@authorize_client_action']);
// Route::get('authorize_client_disable/{id}', ['as' => 'authorize_client_disable', 'uses' => 'HomeController@authorize_client_disable']);
Route::get('authorize_user', ['as' => 'authorize_user', 'uses' => 'HomeController@authorize_user']);
Route::get('authorize_user_action/{id}', ['as' => 'authorize_user_action', 'uses' => 'HomeController@authorize_user_action']);
Route::get('authorize_user_disable/{id}', ['as' => 'authorize_user_disable', 'uses' => 'HomeController@authorize_user_disable']);
Route::get('proxy_add/{sub}', ['as' => 'proxy_add', 'uses' => 'HomeController@proxy_add']);
Route::get('proxy_remove/{sub}', ['as' => 'proxy_remove', 'uses' => 'HomeController@proxy_remove']);
Route::any('add_owner', ['as' => 'add_owner', 'uses' => 'HomeController@add_owner']);
Route::any('make_invitation', ['as' => 'make_invitation', 'uses' => 'HomeController@make_invitation']);
Route::any('accept_invitation/{id}', ['as' => 'accept_invitation', 'uses' => 'OauthController@accept_invitation']);
Route::get('resend_invitation/{id}', ['as' => 'resend_invitation', 'uses' => 'HomeController@resend_invitation']);
Route::any('process_invitation', ['as' => 'process_invitation', 'uses' => 'HomeController@process_invitation']);
Route::any('password_email', ['as' => 'password_email', 'uses' => 'OauthController@password_email']);
Route::any('password_reset/{id}', ['as' => 'password_reset', 'uses' => 'OauthController@password_reset']);
Route::any('change_password', ['as' => 'change_password', 'uses' => 'HomeController@change_password']);
Route::get('my_info', ['as' => 'my_info', 'uses' => 'HomeController@my_info']);
Route::any('my_info_edit', ['as' => 'my_info_edit', 'uses' => 'HomeController@my_info_edit']);
Route::get('default_policies', ['as' => 'default_policies', 'uses' => 'HomeController@default_policies']);
Route::post('change_policy', ['as' => 'change_policy', 'uses' => 'HomeController@change_policy']);
Route::post('fhir_edit', ['as' => 'fhir_edit', 'middleware' => 'csrf', 'uses' => 'HomeController@fhir_edit']);

Route::resource('LastActivity', 'LastActivityController');
// Route::post('pnosh_sync', ['as' => 'pnosh_sync', 'uses' => 'OauthController@pnosh_sync']);
// Route::any('reset_demo', ['as' => 'reset_demo', 'uses' => 'OauthController@reset_demo']);
// Route::any('invite_demo', ['as' => 'invite_demo', 'uses' => 'OauthController@invite_demo']);
// Route::get('check_demo', ['as' => 'check_demo', 'uses' => 'OauthController@check_demo']);
// Route::get('check_demo_self', ['as' => 'check_demo_self', 'middleware' => 'csrf', 'uses' => 'OauthController@check_demo_self']);

// Route::post('token', ['as' => 'token', function () {
//     $bridgedRequest = OAuth2\HttpFoundationBridge\Request::createFromRequest(Request::instance());
//     $bridgedResponse = new OAuth2\HttpFoundationBridge\Response();
//     $bridgedResponse = App::make('oauth2')->handleTokenRequest($bridgedRequest, $bridgedResponse);
//     return $bridgedResponse;
// }]);

// Route::get('authorize', ['as' => 'authorize', 'uses' => 'OauthController@oauth_authorize']);

// Route::get('jwks_uri', ['as' => 'jwks_uri', 'uses' => 'OauthController@jwks_uri']);

// Route::get('userinfo', ['as' => 'userinfo', 'uses' => 'OauthController@userinfo']);

// Dynamic client registration
// Route::post('register', ['as' => 'register', 'uses' => 'UmaController@register']);

// Requesting party claims endpoint
// Route::get('rqp_claims', ['as' => 'rqp_claims', 'uses' => 'UmaController@rqp_claims']);

// Following routes need token authentiation
Route::group(['middleware' => 'token'], function () {
    // Resource set
    // Route::resource('resource_set', 'ResourceSetController');

    // Policy
    // Route::resource('policy', 'PolicyController');

    // Permission request
    // Route::post('permission', ['as' => 'permission', 'uses' => 'UmaController@permission']);

    // Requesting party token request
    // Route::post('authz_request', ['as' => 'authz_request', 'uses' => 'UmaController@authz_request']);

    // introspection
    // Route::post('introspect', ['as'=> 'introspect', 'uses' => 'OauthController@introspect']);

    // Revocation
    // Route::post('revoke', ['as' => 'revoke', 'uses' => 'OauthController@revoke']);
});

// OpenID Connect relying party routes
Route::get('google', ['as' => 'google', 'uses' => 'OauthController@google_redirect']);
Route::any('google_md/{npi?}', ['as' => 'google_md', 'uses' => 'OauthController@google_md']);
Route::any('google_md1', ['as' => 'google_md1', 'uses' => 'OauthController@google_md1']);
Route::get('account/google', ['as' => 'account/google', 'uses' => 'OauthController@google']);
Route::get('installgoogle', ['as' => 'installgoogle', 'uses' => 'OauthController@installgoogle']);
Route::get('doximity', ['as' => 'doximity', 'uses' => 'OauthController@doximity']);
Route::get('doximity_redirect', ['as' => 'doximity_redirect', 'uses' => 'OauthController@doximity_redirect']);
Route::get('doximity_start', ['as' => 'doximity_start', 'uses' => 'OauthController@doximity_start']);
Route::get('doximity_vc', ['as' => 'doximity_vc', 'uses' => 'OauthController@doximity_vc']);
Route::any('vc_offer', ['as' => 'vc_offer', 'uses' => 'OauthController@vc_offer']);
// Configuration endpoints
Route::get('.well-known/openid-configuration', ['as' => 'openid-configuration', function () {
    $scopes = DB::table('oauth_scopes')->get();
    $config = [
        'issuer' => URL::to('/'),
        'grant_types_supported' => [
            'authorization_code',
            'client_credentials',
            'user_credentials',
            'implicit',
            'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'urn:ietf:params:oauth:grant_type:redelegate'
        ],
        'registration_endpoint' => URL::to('register'),
        'token_endpoint' => URL::to('token'),
        'authorization_endpoint' => URL::to('authorize'),
        'introspection_endpoint' => URL::to('introspection'),
        'userinfo_endpoint' => URL::to('userinfo'),
        'scopes_supported' => $scopes,
        'jwks_uri' => URL::to('jwks_uri'),
        'revocation_endpoint' => URL::to('revoke')
    ];
    return $config;
}]);

// Update system call
Route::get('update_system/{type?}', ['as' => 'update_system', 'uses' => 'OauthController@update_system']);

// test and demo pages
Route::any('test1', ['as' => 'test1', 'uses' => 'OauthController@test1']);
Route::any('demo_patient_list/{login?}', ['as' => 'demo_patient_list', 'uses' => 'OauthController@demo_patient_list']);
