var WebPushClient = function WebPushClient(options) {
    return {
        options: {},

        init: function init(options) {
            this.options = options || {};

            if (!options.subscriptionButton) {
                return;
            }

            if (!options.serviceWorker) {
                return;
            }

            this.options.subscriptionButton = options.subscriptionButton;
            this.options.serviceWorker = options.serviceWorker;
            return this.initSW();
        },

        initSW: function initSW() {
            var that = this;

            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register(serviceWorker.getAttribute('value'))
                    .then(function() {
                        if (that.options.subscriptionButton) {
                            that.options.subscriptionButton.removeAttribute('disabled');
                        }
                    });

                that.getSubscription()
                    .then(function(subscription) {
                        if (subscription) {
                            that.setUnsubscribeButton(that);
                        } else {
                            that.setSubscribeButton(that);
                        }
                    });
            }
        },

        getSubscription: function getSubscription() {
            return navigator.serviceWorker.ready
                .then(function(registration) {
                    return registration.pushManager.getSubscription();
                });
        },

        setSubscribeButton: function setSubscribeButton(webPush) {
            var that = webPush;

            if (that.options.subscriptionButton) {
                that.options.subscriptionButton.onclick = function () {
                    navigator.serviceWorker.ready.then(function(registration) {
                        return registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: that.encodeServerKey(that.options.subscriptionButton.getAttribute('data-vapidpublickey'))
                        });
                    }).then(function(subscription) {
                        return fetch(that.options.subscriptionButton.getAttribute('data-register'), {
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
                    }).then(that.setUnsubscribeButton(that));
                };
                var spanSubscribe = document.getElementById('webpush-subscriptionButton-register');
                var spanUnsubscribe = document.getElementById('webpush-subscriptionButton-unregister');
                spanSubscribe.style.display = 'block';
                spanUnsubscribe.style.display = 'none';
            }
        },

        setUnsubscribeButton: function setUnsubscribeButton(webPush) {
            var that = webPush;

            if (that.options.subscriptionButton) {
                that.options.subscriptionButton.onclick = function () {
                    that.getSubscription().then(function(subscription) {
                        return subscription.unsubscribe()
                            .then(function() {
                                return fetch(that.options.subscriptionButton.getAttribute('data-unregister'), {
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
                    }).then(that.setSubscribeButton(that));
                };
                var spanSubscribe = document.getElementById('webpush-subscriptionButton-register');
                var spanUnsubscribe = document.getElementById('webpush-subscriptionButton-unregister');
                spanSubscribe.style.display = 'none';
                spanUnsubscribe.style.display = 'block';
            }
        },

        unsubscribe: function unsubscribe(webPush) {
            var that = webPush;

            that.getSubscription().then(function(subscription) {
                return subscription.unsubscribe()
                    .then(function() {
                        return fetch(that.options.subscriptionButton.getAttribute('data-unregister'), {
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
            }).then(that.setSubscribeButton(that));
        },

        encodeServerKey: function encodeServerKey(serverKey) {
            var padding = '='.repeat((4 - serverKey.length % 4) % 4);
            var base64 = (serverKey + padding).replace(/\-/g, '+').replace(/_/g, '/');

            var rawData = window.atob(base64);
            var outputArray = new Uint8Array(rawData.length);

            for (var i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

    }.init(options);
};

var subButton = document.getElementById('webpush-subscriptionButton');
var servWorker = document.getElementById('serviceWorker');

var webpush = new WebPushClient({
    subscriptionButton: subButton,
    serviceWorker: servWorker
});
