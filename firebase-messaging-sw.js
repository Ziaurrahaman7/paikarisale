importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-auth.js');

firebase.initializeApp({
    apiKey: "AIzaSyAfd_6YKL0IaBD8UEtUxlWJ2lbIQYgi0lY",
    authDomain: "ok-2-f549c.firebaseapp.com",
    projectId: "ok-2-f549c",
    storageBucket: "ok-2-f549c.firebasestorage.app",
    messagingSenderId: "541428640871",
    appId: "1:541428640871:web:45580765c947eaa5bcf8c2",
    measurementId: "G-52NCQRSDKS"
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    return self.registration.showNotification(payload.data.title, {
        body: payload.data.body || '',
        icon: payload.data.icon || ''
    });
});