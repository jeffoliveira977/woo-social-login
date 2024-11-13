<?php

namespace Woo_social_login\Modules;

use Exception;
use Facebook\Facebook;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphUser;
use Facebook\Helpers\FacebookRedirectLoginHelper;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class FacebookLogin
{

    private Facebook $fb;
    private string $fb_app_id;
    private string $fb_app_secret;
    private string $callback_url;
    private string $access_token;
    private FacebookRedirectLoginHelper $helper;

    /**
     * Initializes the FacebookAPI class with the required credentials.
     *
     * @param string $appId      The Facebook App ID.
     * @param string $app_secret  The Facebook App Secret.
     * @param string $callback_url The redirect URI after Facebook login.
     */
    public function __construct(string $appId, string $app_secret, string $callback_url)
    {
        $this->fb_app_id = $appId;
        $this->fb_app_secret = $app_secret;
        $this->callback_url = $callback_url;

        $this->fb = new Facebook([
            'app_id' => $this->fb_app_id,
            'app_secret' => $this->fb_app_secret,
            'default_graph_version' => 'v17.0', // Facebook API version
        ]);

        $this->helper = $this->fb->getRedirectLoginHelper();

    }

    /**
     * Get the Facebook user access token.
     *
     * @return AccessToken|null The Facebook access token or null if not available.
     * @throws Exception If an error occurs during access token retrieval.
     */
    public function getAccessToken(): string
    {

        if (!isset($this->access_token)) {
            try {
                // Get the access token
                $this->access_token = $this->helper->getAccessToken()->getValue();
            } catch (FacebookSDKException $e) {
                throw new Exception('Facebook SDK Error: ' . $e->getMessage());
            }
        }

        return $this->access_token;
    }

    /**
     * Get the user's Facebook profile information using the provided access token.
     *
     * @return GraphUser The user's Facebook profile data.
     * @throws Exception If an error occurs during profile retrieval.
     */
    public function getUserProfile(): GraphUser
    {
        try {
            // Get the access token
            $access_token = $this->getAccessToken();

            // Use the access token to make a request to Facebook Graph API
            $response = $this->fb->get('/me?fields=id,name,first_name,last_name,email,birthday,gender', $access_token);

            return $response->getGraphUser();
        } catch (FacebookResponseException $e) {
            throw new Exception('Facebook Error: ' . $e->getMessage());
        } catch (FacebookSDKException $e) {
            throw new Exception('Facebook SDK Error: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Access token not available.');
        }
    }

    /**
     * Get the Facebook login URL for authentication.
     *
     * @param array $permissions The requested permissions (default is ['email']).
     * @return string The Facebook login URL.
     */
    public function getLoginUrl($permissions = ['email']): string
    {

        // Get the Facebook login URL
        $url = $this->helper->getLoginUrl($this->callback_url, $permissions);
        return $url;
    }
}