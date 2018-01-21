self.processMessage = payload => {
    try {
        const jsonData = JSON.parse(payload);
        const promises = [];
        for (key in jsonData) {
            if ('notification' === key) {
                promises.push(self.registration.showNotification(jsonData.notification.title, jsonData.notification));
            }
        }
        return Promise.race(promises);
    } catch (e) {
        return self.registration.showNotification('Notification', {
            body: payload
        });
    }
};

// Listen to `push` notification event. Define the text to be displayed
// and show the notification.
self.addEventListener('push', event => {
    console.log('SW received push event', event);
    const pushMessageData = event.data;
console.log(pushMessageData);
    const payload = pushMessageData ? pushMessageData.text() : undefined;
    event.waitUntil(self.processMessage(payload));
});


// Listen to  `pushsubscriptionchange` event which is fired when
// subscription expires. Subscribe again and register the new subscription
// in the server by sending a POST request with endpoint. Real world
// application would probably use also user identification.
self.addEventListener('pushsubscriptionchange', function(event) {
    console.log('Subscription expired');
    event.waitUntil(
        self.registration.pushManager.subscribe({ userVisibleOnly: true })
            .then(function(subscription) {
                console.log('Subscribed after expiration', subscription.endpoint);
                return fetch('register', {
                    method: 'post',
                    headers: {
                        'Content-type': 'application/json'
                    },
                    body: JSON.stringify({
                        endpoint: subscription.endpoint
                    })
                });
            })
    );
});
