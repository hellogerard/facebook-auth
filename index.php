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
        // try an API call - only foolproof way to ensure a valid access token
        $user = (object) $facebook->api("/{$_SESSION['user']}");
    }
    catch (FacebookApiException $e)
    {
        if (strpos($e->getMessage(), 'Error validating access token') !== false ||
            strpos($e->getMessage(), 'Invalid OAuth access token') !== false)
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
