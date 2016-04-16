<?php
/**
 * Opauth basic configuration file to quickly get you started
 * ==========================================================
 * To use: rename to opauth.conf.php and tweak as you like
 * If you require advanced configuration options, refer to opauth.conf.php.advanced
 * https://github.com/opauth/opauth/wiki/List-of-strategies
 */


$config = array(
/**
 * Path where Opauth is accessed.
 *  - Begins and ends with /
 *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
 *  - if Opauth is reached via http://auth.example.org/, path is '/'
 */
	'path' => ((strlen($this->getConf("OauthPath")))?$this->getConf("OauthPath"):'/CloudFrameWorkOauth/'),

/**
 * Callback URL: redirected to after authentication, successful or otherwise
 */
	'callback_url' => ((strlen($this->getConf("OauthCallBack")))?$this->getConf("OauthCallBack"):'/CloudFrameWorkOauth?auth=finished'),

/**
 * A random string used for signing of $auth response.
 *
 * NOTE: PLEASE CHANGE THIS INTO SOME OTHER RANDOM STRING
 */
	'security_salt' => 'LDFADNBPW10rx4W1KsVrieCloudFrameWorkzpTBWA5vJidQKDx8pMJbmw28R1C4m',

/**
 * Strategy
 * Refer to individual strategy's documentation on configuration requirements.
 *
 * eg.
 * 'Strategy' => array(
 *
 *   'Facebook' => array(
 *      'app_id' => 'APP ID',
 *      'app_secret' => 'APP_SECRET'
 *    ),
 *
 * )
 *
 */
	'Strategy' => array(
		// Define strategies and their respective configs here

		'Facebook' => array(
			'app_id' => $this->getConf("FacebookOauth_APP_ID"),
			'app_secret' => $this->getConf("FacebookOauth_APP_SECRET"),
			'scope' => $this->getConf("FacebookOauth_SCOPE")

		),
		'Google' => array(
			'client_id' => $this->getConf("GoogleOauth_CLIENT_ID"),
			'client_secret' => $this->getConf("GoogleOauth_CLIENT_SECRET"),
			'scope' => (is_array($this->getConf("GoogleOauth_SCOPE"))) ? implode(' ',$this->getConf("GoogleOauth_SCOPE")) : 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
			'access_type'=>'offline',
			'approval_prompt'=>(isset($_GET['approval_prompt']) || $this->isAuth())?'force':''
			),
		'Instagram' => array(
			'client_id' => $this->getConf("InstagramOauth_CLIENT_ID"),
			'client_secret' => $this->getConf("InstagramOauth_CLIENT_SECRET"),
			'scope' => (is_array($this->getConf("InstagramOauth_SCOPE"))) ? implode(' ',$this->getConf("InstagramOauth_SCOPE")) : 'basic public_content follower_list comments relationships likes',

		),
		'Twitter' => array(
			'key' => $this->getConf("TwitterOauth_KEY"),
			'secret' => $this->getConf("TwitterOauth_SECRET")
		),
		'VKontakte' => array(
		    'app_id' => $this->getConf("VKontakteOauth_APP_ID"),
		    'app_secret' => $this->getConf("VKontakteOauth_APP_SECRET")
		)

	),

);
