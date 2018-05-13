

FB.init({
    appId: fb_appId,
    status: true, // check login status
    cookie: true, // enable cookies to allow the server to access the session
    xfbml: true  // parse XFBML
});
function fb_login() {
    FB.getLoginStatus(function (response) {
        if (response.status === 'connected') {
            // connected
            document.location = url_redirect;
        } else {
            // not_authorized
            FB.login(function (response) {
                if (response.authResponse) {
                    document.location = url_redirect;
                } else {
                    document.location = url_register;
                }
            }, {scope: 'email'});
        }
    });
}