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
    const pushMessageData = event.data;
    const payload = pushMessageData ? pushMessageData.text() : undefined;
    event.waitUntil(self.processMessage(payload));
});


// Listen to  `pushsubscriptionchange` event which is fired when
// subscription expires. Subscribe again and register the new subscription
// in the server by sending a POST request with endpoint. Real world
// application would probably use also user identification.
self.addEventListener('pushsubscriptionchange', function(event) {
    event.waitUntil(
        self.registration.pushManager.subscribe({ userVisibleOnly: true })
            .then(function(subscription) {
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

self.addEventListener('notificationclick', function(e) {
    var notification = e.notification;
    var url = notification.data.url;
    var action = e.action;

    if (action === 'close') {
        notification.close();
    } else {
        clients.openWindow(url);
        notification.close();
    }
});
