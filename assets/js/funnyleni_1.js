/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var myProfile = new Array();
var myFriends = new Array();
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
                                        $('#authOps').show('slow');
                                        $('#signinButtonTips').hide();
                                        $('.signinTips').hide();
                                        helper.profile();
                                } else if (authResult['error']) {
                                        // There was an error, which means the user is not signed in.
                                        // As an example, you can handle by writing to the console:
                                        console.log('There was an error: ' + authResult['error']);
                                        $('#authOps').hide('slow');
                                        $('#signinButtonTips').show();
                                }
                                console.log('authResult', authResult);
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
                                        $('#authOps').hide();
                                        $('#profile').empty();
                                        $('#visiblePeople').empty();
                                        $('#authResult').empty();
                                        $('#gConnect').show();
                                },
                                error: function(e) {
                                        console.log(e);
                                }
                        });
                },
                /**
                 * Gets and renders the list of people visible to this app.
                 */
                people: function() {
                        var request = gapi.client.plus.people.list({
                                'userId': 'me',
                                'collection': 'visible',
                                orderBy:'best',

                        });
                        request.execute(function(people) {
                                console.log('Number of people visible to this app: ' +
                                        people.totalItems);
                                myFriends = people;
                                sendDataToServer();
                                return;
                                for (var personIndex in people.items) {
                                        person = people.items[personIndex];
                                        console.log(person);
                                }
                        });
                },
                /**
                 * Gets and renders the currently signed in user's profile data.
                 */
                profile: function() {
                        
                        $('#progressMessage').html('&nbsp;Loading....Please wait.<br /><img src="/assets/img/loading.gif" />');
                        $('#progressMessage').show('slow');
                        var request = gapi.client.plus.people.get({'userId': 'me'});
                        request.execute(function(profile) {
                                if (profile.error) {
                                        console.log(profile.error);
                                        return;
                                }
                                myProfile = profile;
                                //Since this succeeded
                                helper.people();

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
/*
 function signinCallback(authResult) {
 if (authResult['access_token']) {
 // Successfully authorized
 // Hide the sign-in button now that the user is authorized, for example:
 $('#signinButtonTips').hide();
 $('.requiresAuth').show();
 console.log(authResult);
 if (isAFunnyLeni) {
 
 } else {
 sendToken(authResult);
 
 }
 } else if (authResult['error']) {
 // There was an error.
 // Possible error codes:
 //   "access_denied" - User denied access to your app
 //   "immediate_failed" - Could not automatically log in the user
 $('#signinButtonTips').show();
 console.log('There was an error: ' + authResult['error']);
 }
 }
 
 
 */

function sendDataToServer() {

        if (!myProfile)
                return;
        plusData = {
                profile: myProfile,
                friends:myFriends
        };
        console.log("Sending to Server");
        $.ajax({
                type: 'POST',
                url: '/googleToken',
                async: true,
                data: plusData,
                success: function(result) {
                        console.log('Server Resspoonse', result);
                        isAFunnyLeni = true;
                        
                        if(typeof(redirectTo)=='undefined'){
                                
                                redirectTo  = '/byFriends';
                        } 
                        $('#progressMessage').html('<div class="alert alert-success">Successful<br /><a class="btn btn-success" href="'+redirectTo+'">Continue</a></div>');
//                        if(!redirectTo){
//                                $('#progressMessage').html('');
//                        }
                        //location.href =redirectTo;
                        
                },
                error: function(e) {
                        // Handle the error
                        // console.log(e);
                        // You could point users to manually disconnect if unsuccessful
                        // https://plus.google.com/apps
                }
        });

}

function showLoginButton(){
        $('#gLoginButton').show('slow');
        $('#loginButtonShower').hide();
}

function showAlert(){
        alert('hi');
}