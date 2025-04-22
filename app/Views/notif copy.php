<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Bootstrap demo</title>
    <link rel="manifest" href="/manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>

    <button id="enableNotifications">Aktifkan Notifikasi</button>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
        import {
            getMessaging,
            getToken,
            onMessage
        } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-messaging.js";

        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyBzDSVmYe15ppdfGK16ODjyZlqzw3JC6bg",
            authDomain: "playground-1379a.firebaseapp.com",
            projectId: "playground-1379a",
            storageBucket: "playground-1379a.firebasestorage.app",
            messagingSenderId: "673795209986",
            appId: "1:673795209986:web:f641545a89209adcda201b"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        // Pendaftaran Service Worker
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(function(registration) {
                    console.log('Service Worker registered with scope:', registration.scope);
                    $('#notificationStatus').text('Service Worker berhasil terdaftar.');
                    // Setelah Service Worker terdaftar, Anda bisa langsung meminta izin
                    requestPermission();
                })
                .catch(function(error) {
                    console.error('Service Worker registration failed:', error);
                    $('#notificationStatus').text('Gagal mendaftarkan Service Worker: ' + error);
                });
        } else {
            console.warn('Push messaging is not supported.');
            $('#notificationStatus').text('Push messaging tidak didukung di browser ini.');
        }


        function requestPermission() {
            console.log('Requesting permission...');
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                    getToken(messaging, {
                        vapidKey: 'BNM9NxMTSTyvhZsfCNV2SL1C1bM-MB_ODMZu6i6X9v1UXzKvqsCa46TMG61P9WY0fMTOjUdOQOSJ8QyWxhOrGp8'
                    }).then((currentToken) => {
                        if (currentToken) {
                            console.log('FCM registration token:', currentToken);
                            sendTokenToServer(currentToken);
                        } else {
                            console.log('Can not get token. Are permissions blocked?');
                        }
                    }).catch((err) => {
                        console.log('An error occurred while retrieving token. ', err);
                    });
                } else {
                    console.log('Denied notification permission :(', permission);
                }
            });
        }

        function sendTokenToServer(token) {
            $.ajax({
                url: '/subscribe-fcm', // Ganti dengan URL endpoint di CI4 Anda
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    token: token
                }),
                dataType: 'json',
                success: function(data) {
                    console.log('Token sent to server:', data);
                },
                error: function(error) {
                    console.error('Error sending token to server:', error);
                }
            });
        }

        onMessage(messaging, (payload) => {
            console.log('Message received. ', payload);
            showLocalNotification(payload.notification.title, payload.notification.body, payload.data.click_action);
        });

        function showLocalNotification(title, body, url) {
            const options = {
                body: body,
                icon: '/logo.png', // Sesuaikan dengan path icon Anda
                data: {
                    url: url
                }
            };
            navigator.serviceWorker.ready.then(registration => {
                registration.showNotification(title, options);
            });
        }

        // Contoh memicu permintaan izin saat halaman dimuat (opsional)
        // requestPermission();

        // Atau, Anda bisa memicu permintaan izin berdasarkan interaksi pengguna, misalnya klik tombol:
        $(document).ready(function() {
            $('#enableNotifications').on('click', function() {
                requestPermission();
            });
        });
    </script>
</body>



</html>