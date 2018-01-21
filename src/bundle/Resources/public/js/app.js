// [Working example](/push-subscription-management_demo.html).

var subscriptionButton = document.getElementById('subscriptionButton');
var serviceWorker = document.getElementById('serviceWorker');

// As subscription object is needed in few places let's create a method which
// returns a promise.
function getSubscription() {
    return navigator.serviceWorker.ready
        .then(function(registration) {
            return registration.pushManager.getSubscription();
        });
}

// Register service worker and check the initial subscription state.
// Set the UI (button) according to the status.
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register(serviceWorker.getAttribute('value'))
        .then(function() {
            console.log('service worker registered');
            if (subscriptionButton) {
                subscriptionButton.removeAttribute('disabled');
            }
        });
    getSubscription()
        .then(function(subscription) {
            if (subscription) {
                console.log('Already subscribed', subscription.endpoint);
                setUnsubscribeButton();
            } else {
                setSubscribeButton();
            }
        });
}

// Get the `registration` from service worker and create a new
// subscription using `registration.pushManager.subscribe`. Then
// register received new subscription by sending a POST request with its
// endpoint to the server.
function subscribe() {
    navigator.serviceWorker.ready.then(function(registration) {
        return registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: encodeServerKey(subscriptionButton.getAttribute('data-vapidpublickey'))
        });
    }).then(function(subscription) {
        console.log('Subscribed', subscription.endpoint);
        return fetch('/admin/webpush/register', {
            method: 'post',
            mode: 'cors',
            credentials: 'include',
            cache: 'default',
            headers: new Headers({
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }),
            body: JSON.stringify(subscription)
        });
    }).then(setUnsubscribeButton);
}

function encodeServerKey(serverKey) {
    var padding = '='.repeat((4 - serverKey.length % 4) % 4);
    var base64 = (serverKey + padding).replace(/\-/g, '+').replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Get existing subscription from service worker, unsubscribe
// (`subscription.unsubscribe()`) and unregister it in the server with
// a POST request to stop sending push messages to
// unexisting endpoint.
function unsubscribe() {
    getSubscription().then(function(subscription) {
        return subscription.unsubscribe()
            .then(function() {
                console.log('Unsubscribed', subscription.endpoint);
                return fetch('/admin/webpush/unregister', {
                    method: 'post',
                    mode: 'cors',
                    credentials: 'include',
                    cache: 'default',
                    headers: new Headers({
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }),
                    body: JSON.stringify({
                        endpoint: subscription.endpoint
                    })
                });
            });
    }).then(setSubscribeButton);
}

// Change the subscription button's text and action.
function setSubscribeButton() {
    if (subscriptionButton) {
        subscriptionButton.onclick = subscribe;
        subscriptionButton.textContent = 'Subscribe!';
    }
}

function setUnsubscribeButton() {
    if (subscriptionButton) {
        subscriptionButton.onclick = unsubscribe;
        subscriptionButton.textContent = 'Unsubscribe!';
    }
}
