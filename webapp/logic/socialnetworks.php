<?php
/**
 * Followers count service from Social Networks
 * @Author Fran López <ll@bloombees.com>
 * @version 1.0
 */
use CloudFramework\Service\SocialNetworks\SocialNetworks;

/** @var ADNBP $this */
$sc = SocialNetworks::getInstance();
$google = (array_key_exists("google_form_credentials", $_SESSION)) ? $_SESSION["google_form_credentials"] : array();
$instagram = (array_key_exists("instagram_form_credentials", $_SESSION)) ? $_SESSION["instagram_form_credentials"] : array();
$twitter = (array_key_exists("twitter_form_credentials", $_SESSION)) ? $_SESSION["twitter_form_credentials"] : array();
$facebook = (array_key_exists("facebook_form_credentials", $_SESSION)) ? $_SESSION["facebook_form_credentials"] : array();


function getFromArray($key, array $array = array()) {
    if(array_key_exists($key, $array)) {
        return $array[$key];
    }
    return '';
}

$requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
if ($requestMethod === 'POST') {
    $postData = json_decode(file_get_contents("php://input"), true);
    if (array_key_exists("social", $postData)) {
        $redirectUrl = SocialNetworks::generateRequestUrl() . "socialnetworks?".strtolower($postData["social"])."OAuthCallback";
        if ("Instagram" === $postData["social"]) {
            $redirectUrl .= "=1";
        }

        $credentials = $sc->auth($postData["social"], $postData, $redirectUrl);

        // Get profile
        //$profile = $sc->getProfile($postData["social"], $credentials);

        // Get images
        //$images = $sc->import($postData["social"], $credentials, "./");

        // Export
        $exportdto = null;
        if ("" !== $postData["export_content"]) {
            if ("Google" === $postData["social"]) {
                $parameters = array(
                    "userId" => "me",
                    "content" => $postData["export_content"]
                );
            } else if (("Instagram" === $postData["social"]) && (count($images) > 0)) {
                $parameters = array(
                    "mediaId" => $images[0]["id"],
                    "content" => $postData["export_content"]
                );
            }
            $exportdto = $sc->export($postData["social"], $credentials, $parameters);
        }
        SocialNetworks::jsonResponse(array(
            "social" => $postData['social'],
            "images" => array(),
            "count" => 0,
            "exportdto" => $exportdto
        ));
        exit;
    } else {
        SocialNetworks::generateErrorResponse("Need a social to make followers request", 400);
    }

} else {
    if (array_key_exists("googleOAuthCallback", $_REQUEST)) {
        $params = array(
            "client" => $_SESSION["google_apikeys"]["client"],
            "secret" => $_SESSION["google_apikeys"]["secret"],
            "code" => $_REQUEST["code"],
            "redirectUrl" => SocialNetworks::generateRequestUrl() . "socialnetworks?googleOAuthCallback"
        );
        $sc->saveInSession("Google", $params);
    } elseif (array_key_exists("instagramOAuthCallback", $_REQUEST)) {
            $params = array(
                "client" => $_SESSION["instagram_apikeys"]["client"],
                "secret" => $_SESSION["instagram_apikeys"]["secret"],
                "code" => $_REQUEST["code"],
                "error" => $_REQUEST["error"],
                "error_reason" => $_REQUEST["error_reason"],
                "error_description" => $_REQUEST["error_description"],
                "redirectUrl" => SocialNetworks::generateRequestUrl() . "socialnetworks?instagramOAuthCallback=1"
            );
            $sc->saveInSession("Instagram", $params);
    } elseif (array_key_exists("twitterOAuthCallback", $_REQUEST)) {
        $params = array(
            "client" => $this->getConf("TwitterOauth_KEY"),
            "secret" => $this->getConf("TwitterOauth_SECRET"),
            "verifier" => $_REQUEST["oauth_verifier"],
            "token" => $_REQUEST["oauth_token"],
            "redirectUrl" => SocialNetworks::generateRequestUrl() . "socialnetworks?twitterOAuthCallback"
        );
        $sc->saveInSession("Twitter", $params);
    } elseif (array_key_exists("facebookOAuthCallback", $_REQUEST)) {
        $params = array(
            "client" => $this->getConf("FacebookOauth_APP_ID"),
            "secret" => $this->getConf("FacebookOauth_APP_SECRET"),
            "code" => $_REQUEST["code"],
            "state" => $_REQUEST["state"],
            "redirectUrl" => SocialNetworks::generateRequestUrl() . "socialnetworks?facebookOAuthCallback"
        );
        $sc->saveInSession("Facebook", $params);
    }
}