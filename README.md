# php-push-notification
Send push notificaiton to Android and Ios devices with php

How to Use
```
$message = 'Hello World';
$title = 'My App';

//$title is optional
PushNotification::sen($device_token, $message, $title);
```
