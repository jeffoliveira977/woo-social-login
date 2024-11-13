<?php

namespace Woo_social_login\Modules;
use Exception;
use Google;

class GoogleLogin
{
    private $client;
    private $oauth;

    public function __construct(string $clint_id, string $client_secret, string $redirect_url)
    {

        $this->client = new Google\Client();
        $this->client->setClientId($clint_id);
        $this->client->setClientSecret($client_secret);
        $this->client->setRedirectUri($redirect_url);
        $this->client->addScope('email');
        $this->client->addScope('profile');

        $this->oauth = new Google\Service\Oauth2($this->client);
    }

    public function authenticate()
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token["error"])) {
            throw new Exception('Error: Unable to retrieve access token from google.');
        }

        $this->client->setAccessToken($token['access_token']);
    }

    public function get_auth_url()
    {
        return $this->client->createAuthUrl();
    }

    public function get_user_info()
    {
        return $this->oauth->userinfo->get();
    }
}