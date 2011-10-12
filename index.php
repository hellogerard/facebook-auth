<?php


require('lib/facebook.php');


$params = array('appId' => 'XXXXXX',
                'secret' => 'XXXXXX');
$facebook = new Facebook($params);
$facebookAppId = $params['appId'];



// if logging out
if (isset($_GET['logout']))
{
    session_unset();
    session_destroy();
    header('HTTP/1.1 302 Found');
    header('Location: /');
    exit;
}

// else we have a valid application session
else if (isset($_SESSION['user']))
{
    try
    {
        // if we need to make an API call, the Facebook PHP-SDK stored our
        // access_token in $_SESSION.
        $user = (object) $facebook->api("/{$_SESSION['user']}");
    }
    catch (FacebookApiException $e)
    {
        // if our access_token is now invalid (i.e. user removed our app, or
        // logged out of Facebook, etc.), we can recover here. this is the only
        // way I know of to know if a token has been invalidated.
        if ($e->getType() == 'OAuthException')
        {
            session_unset();
            session_destroy();
            header('HTTP/1.1 302 Found');
            header('Location: /');
            exit;
        }
        else
        {
            throw $e;
        }
    }
}

// else attempting to log in
else if ($facebook->getUser())
{
    $id = $facebook->getUser();

    if ($id)
    {
        // login successful
        $_SESSION['user'] = $id;
    }

    header('HTTP/1.1 302 Found');
    header('Location: /');
    exit;
}

// if user not logged in, or access token invalidated
else
{
    $loginUrl = $facebook->getLoginUrl(array('scope' => 'offline_access'));
}

require('view.phtml');
