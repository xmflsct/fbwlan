<?php 
/*
 *
 * Copyright 2015 Michael Haas
 *
 * This file is part of FBWLAN.

 * FBWLAN is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation in version 3.
 *
 * FBWLAN is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


// This module handles all interaction with the user's browser
// and Facebook

// TODO: this only works if the script is installed in root
define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__ . '/../include/facebook-php-sdk-v4/src/Facebook/');
require_once(__DIR__ . '/../include/facebook-php-sdk-v4/autoload.php');

require_once(__DIR__ . '/../tokens.php');

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

FacebookSession::setDefaultApplication(APP_ID,
     APP_SECRET);


Flight::set('retry_url', MY_URL .'login');


function render_boilerplate() {
    Flight::render('head',
        array(
            'my_url' => MY_URL,
            'title' => _('WLAN at ') . PAGE_NAME,
        ),
        'head');
    Flight::render('foot',
        array(
            'privacy_url' => MY_URL . 'privacy/',
            'imprint_url' => IMPRINT_URL,
        ),
        'foot');
    Flight::render('back_to_code_widget',
        array(
            'retry_url' => Flight::get('retry_url'),
        ),
        'back_to_code_widget');
    Flight::render('access_code_widget',
        array(
            'codeurl' => MY_URL . 'access_code/',
        ),
        'access_code_widget');
}


function check_permissions($session) {

    $request = new FacebookRequest(
        $session,
        'GET',
        '/me/permissions'
    );

    try {
        $response = $request->execute();
        $graphObject = $response->getGraphObject()->asArray();
        // http://stackoverflow.com/q/23527919
        foreach ($graphObject as $key => $permissionObject) {
            //print_r($permission);
            if ($permissionObject->permission == 'publish_actions') {
                return $permissionObject->status == 'granted';
            }
        }
    } catch (FacebookRequestException $ex) {
        Flight::error($ex);
    } catch (\Exception $ex) {
        Flight::error($ex);
    }
    return false;
}


function handle_root() {
    render_boilerplate();
    Flight::render('root',
        array(
            'page_name' => PAGE_NAME,
            'page_url' => MY_URL . 'login/?gw_id=demo&gw_address=localhost&gw_port=80',
    ));

}

// if the user does not grant publish_actions,
// we go here and ask again
function handle_rerequest_permission() {
    render_boilerplate();
    // Simplification: always assume we are not logged in!
    $helper = new FacebookRedirectLoginHelper(MY_URL . 'fb_callback/');
    // We do want to publish to the user's wall!
    $scope = array('publish_actions');
    $fb_login_url = $helper->getReRequestUrl($scope);
    Flight::render('rerequest_permission', array(
        'fburl' => $fb_login_url,
        ));
}

// In the FB callback, we show a form to the user
// or an error message if something went wrong.
function handle_fb_callback() {
    render_boilerplate();
    $helper = new FacebookRedirectLoginHelper(MY_URL . 'fb_callback/');
    try {
        $session = $helper->getSessionFromRedirect();
    } catch(FacebookRequestException $ex) {
      // When Facebook returns an error
        Flight::error($ex);
    } catch(\Exception $ex) {
        // When validation fails or other local issues
        Flight:error($ex);
    }
    if ($session) {
        $_SESSION['FBTOKEN'] = $session->getToken();
        if (check_permissions($session)) {
            $_SESSION['FB_CHECKIN_NONCE'] = make_nonce();
            Flight::render('fb_callback', array(
                'post_action' => MY_URL .'checkin',
                'place_name' => PAGE_NAME,
                'nonce' => $_SESSION['FB_CHECKIN_NONCE'],
                ));
        } else {
            if (ARRAY_KEY_EXISTS('FB_REREQUEST', $_SESSION) && $_SESSION['FB_REREQUEST']) {
                Flight::render('denied_fb', array(
                    'msg' => _('It seems that the check-in process encountered some error. No worries.'),
                ));
            } else {
                $_SESSION['FB_REREQUEST'] = True;
                Flight::redirect(MY_URL . 'rerequest_permission');
            }
        }
    }
    else {
        Flight::render('denied_fb', array(
            'msg' => _('It looks like you didn\'t login successfully!'),
        ));
    }
}

function handle_checkin() {
    render_boilerplate();
    // This happens if we unset the nonce below.
    // Or if the nonce was never set, in which case the user
    // shouldn't be here.
    $msg1 = _('It looks like you accidentally hit the refresh button or got here by accident.');
    $msg2 = _('We prevented a double post of your message.');

    if (! array_key_exists('FB_CHECKIN_NONCE', $_SESSION)) {
        Flight::render('denied_fb', array(
            'msg' => $msg1 . ' ' . $msg2,
        ));
        Flight::stop();
    }
    $nonce = $_SESSION['FB_CHECKIN_NONCE'];
    if (empty($nonce)) {
        Flight::render('denied_fb', array(
            'msg' => $msg1 . ' '. $msg2,
        ));
        Flight::stop();
    }
    $submitted_nonce = Flight::request()->query->nonce;
    if (empty($submitted_nonce)) {
        Flight::error(new Exception('No nonce in form submission!'));
    }
    if ($nonce !== $submitted_nonce) {
        Flight::error(new Exception('Nonces don\'t match!'));
    }
    // Now, make double submissions impossible by discarding the
    // nonce
    unset($_SESSION['FB_CHECKIN_NONCE']);

    $token = $_SESSION['FBTOKEN'];
    if (empty($token)) {
        Flight::error(new Exception('No FB token in session!'));
    }
    $session = new FacebookSession($token);
    $message = Flight::request()->query->message;

    $config = array(place => PAGE_ID);
    if (! empty($message)) {
        $config['message'] = $message;
    }
    $request = new FacebookRequest(
        $session,
        'POST',
        '/me/feed',
        $config
    );
    // Some exceptions can be caught and handled sanely,
    // e.g. Duplicate status message (506)
    try {
        $response = $request->execute()->getGraphObject();
    } catch (FacebookRequestException $ex) {
        Flight::error($ex);
    } catch (\Exception $ex) {
        Flight::error($ex);
    }
    $postid = $response->asArray()['id'];
    $posturl = 'https://www.facebook.com/' . $postid;
    Flight::render('checkin',
        array(
            'loginurl' => login_success(False),
            'posturl' => $posturl,
    ));
}

function fblogin() {

    // Simplification: always assume we are not logged in!
    $helper = new FacebookRedirectLoginHelper(MY_URL . 'fb_callback/');
    // We do want to publish to the user's wall!
    // Note: Facebook docs state that login and write permission request
    // should be two separate requests.
    // The code is already set up to handle this separately, but I believe
    // the combined flow provides better UX.
    // https://developers.facebook.com/docs/facebook-login/permissions/v2.2
    $scope = array('publish_actions');
    $fb_login_url = $helper->getLoginUrl($scope);
    $code_login_url = MY_URL . 'access_code/';
    Flight::render('login', array(
        'fburl' => $fb_login_url,
        'codeurl' =>  $code_login_url
        ));

}


function handle_access_code() {

    render_boilerplate();
    $request = Flight::request();
    $code = $request->query->access_code;
    $code = strtolower(trim($code));

    if (empty($code)) {
        Flight::render('denied_code', array(
            'msg' => _('Did you type anything?'),
        ));
    } else if ($code != ACCESS_CODE) {
        Flight::render('denied_code', array(
            'msg' => _('Did you have a typo?'),
        ));
    } else {
        login_success();
    }
}

function is_session_valid() {
    if (!(empty($_SESSION['gw_address']) || empty($_SESSION['gw_port']) || empty($_SESSION['gw_id']))) {
        return true;
        // reg_url is not that important and might be empty?
        //Flight::error(new Exception('Gateway parameters not set in login handler!'));
    }
    return false;

}

function update_session($request) {
    $gw_address = $request->query->gw_address;
    $gw_port = $request->query->gw_port;
    $gw_id = $request->query->gw_id;
    if (!(empty($gw_address) || empty($gw_port) || empty($gw_id))) {
        $_SESSION['gw_address'] = $gw_address;
        $_SESSION['gw_port'] = $gw_port;
        $_SESSION['gw_id'] = $gw_id;
    }
    $req_url = $request->query->url;
    if (! empty($req_url)) {
        $_SESSION['req_url'] = $req_url;
    }
}

// User request
function handle_login() {
    $request = Flight::request();
    //login/?gw_address=%s&gw_port=%d&gw_id=%s&url=%s
    // If we get called without the gateway parameters, then we better
    // have these in the session already.
    // Initialize or update session parameters
    update_session($request);
    // If we have no session parameters now, we never had them
    if (!is_session_valid()) {
        Flight::error(new Exception('Gateway parameters not set in login handler!'));
    }
    render_boilerplate();
    fblogin();
}


function login_success($redirect = True) {
    //  http://" . $gw_address . ":" . $gw_port . "/wifidog/auth?token=" . $token
    $token = make_token();
    $url = 'http://' . $_SESSION['gw_address'] . ':'
        . $_SESSION['gw_port'] . '/wifidog/auth?token=' . $token;
    if ($redirect) {
        Flight::redirect($url);
    } else {
        return $url;
    }
}


function handle_privacy() {
    render_boilerplate();
    Flight::render('privacy', array(
        'session_duration' => SESSION_DURATION,
        'cookie_session_duration' => COOKIE_SESSION_DURATION / 60,
        'extended_privacy_url' => EXTENDED_PRIVACY_URL,
        'imprint_url' => IMPRINT_URL,
    ));

}


function make_nonce() {
    return urlencode(uniqid ('', true));
}
