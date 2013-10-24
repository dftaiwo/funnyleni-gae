/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var myProfile = new Array();
var myAuthResult = false;
if (typeof(console) == "undefined") {
        window.console = {
                log: function() {
                }
        };
}



//This helper was cusotmized from https://developers.google.com/+/quickstart/javascript
var helper = (function() {
        var BASE_API_PATH = 'plus/v1/';

        return {
                /**
                 * Hides the sign in button and starts the post-authorization operations.
                 *
                 * @param {Object} authResult An Object which contains the access token and
                 *   other authentication information.
                 */
                onSignInCallback: function(authResult) {
                        gapi.client.load('plus', 'v1', function() {

                                if (authResult['access_token']) {
                                        myAuthResult = authResult;
                                        helper.profile();
                                } else if (authResult['error']) {
                                        // There was an error, which means the user is not signed in.
                                        // As an example, you can handle by writing to the console:
                                        console.log('There was an error: ' + authResult['error']);

                                }
                        });
                },
                /**
                 * Calls the OAuth2 endpoint to disconnect the app for the user.
                 */
                disconnect: function() {
                        // Revoke the access token.
                        $.ajax({
                                type: 'GET',
                                url: 'https://accounts.google.com/o/oauth2/revoke?token=' +
                                        gapi.auth.getToken().access_token,
                                async: false,
                                contentType: 'application/json',
                                dataType: 'jsonp',
                                success: function(result) {
                                        console.log('revoke response: ' + result);
                                },
                                error: function(e) {
                                        console.log(e);
                                }
                        });
                },
                /**
                 * Gets and renders the currently signed in user's profile data.
                 */
                profile: function() {

                        var request = gapi.client.plus.people.get({'userId': 'me'});
                        request.execute(function(profile) {
                                if (profile.error) {
                                        console.log(profile.error);
                                        return;
                                }
                                myProfile = profile;
                                console.log('Got User Profile');
                                //Since this succeeded
                                sendDataToServer();

                        });
                }
        };
})();

/**
 * Calls the helper method that handles the authentication flow.
 *
 * @param {Object} authResult An Object which contains the access token and
 *   other authentication information.
 */
function onSignInCallback(authResult) {
        helper.onSignInCallback(authResult);
}

function sendDataToServer() {

        if (isAFunnyLeni) {
                console.log('This guy is a funnyleni already....so no sending again');
                return;
        }
        if (!myProfile)
                return;
        plusData = {
                profile: myProfile,
                authResult: myAuthResult
        };

        console.log("Sending to Server", plusData);
        $.ajax({
                type: 'POST',
                url: '/silentAuth',
                async: true,
                data: plusData,
                success: function(result) {
                        console.log('Server Response', result);
                        isAFunnyLeni = true;

                        if (typeof(redirectTo) == 'undefined') {

                                redirectTo = '/byFriends';
                        }

                },
                error: function(e) {
                        // Handle the error
                        // console.log(e);
                        // You could point users to manually disconnect if unsuccessful
                        // https://plus.google.com/apps
                }
        });

}
 