<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Facebook Authentication</title>
    <style type="text/css">
      body { font-family: 'Lucida Grande', Verdana, Arial, sans-serif; }
    </style>
  </head>
  <body>
    <h1>Facebook Authentication</h1>

    <?php if (isset($user)) { ?>

      <p>Logged in as Facebook user ID <?= $user->id ?>.</p>

      <p><a href="/index.php?logout">Logout</a></p>

    <?php } else { ?>

      <a id="fb-login" href="<?= $loginUrl ?>">
        <img src="http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif">
      </a>

    <?php } ?>

    <div id="fb-root"></div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
    <script src="//connect.facebook.net/en_US/all.js"></script>

    <script type="text/javascript">

        window.fbAsyncInit = function() {

            FB.init( { appId: '<?= $facebookAppId ?>', status: true, cookie: false, oauth: true } );

            $('#fb-login').click(function(e) {
              e.preventDefault();

              FB.getLoginStatus(function(response) {

                // maintain application anchor/query string, if any
                q = window.location.search.substring(1);
                if (window.location.hash) {
                  q = window.location.hash.split('?')[1];
                }

                // if already logged in, redirect
                if (response.authResponse) {
                  window.location.href = '/?signed_request=' + response.authResponse.signedRequest + '&' + q;

                } else {

                  // else present user with FB auth popup
                  FB.login(function(response) {

                    // if user logs in successfully, redirect
                    if (response.authResponse) {
                      window.location.href = '/?signed_request=' + response.authResponse.signedRequest + '&' + q;
                    }
                  }, { scope:'offline_access' } );
                }
              });
            });

        };

    </script>
  </body>
</html>
