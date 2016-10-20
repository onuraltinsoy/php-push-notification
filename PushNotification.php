<?php

class PushNotification
{

// API access key from Google API's Console
const API_ACCESS_KEY = 'your access key';

public static function send($device_token, $message, $title = '')
{

    if (strlen($device_token) > 64){
            $registrationIds = $device_token;

    // prep the bundle
        $msg = array
        (
            'message' => $message,
            'title' => $title,
            'subtitle' => '',
            'tickerText' => '',
            'vibrate' => 1,
            'sound' => 1,
            'msgcnt' => 1,
            'largeIcon' => 'ic_launcher.png',
            'smallIcon' => 'ic_launcher.png'
        );

        $fields = array
        (
            'registration_ids' => array($registrationIds),
            'data' => $msg
        );

        $headers = array
        (
            'Authorization: key=' . self::API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }else{

        // My private key's passphrase here:
        $passphrase = 'ypur private key password';

        //badge
        $badge = 1;

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__ . DIRECTORY_SEPARATOR . 'aps.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://gateway.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        echo 'Connected to APNS' . PHP_EOL;

        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'badge' => $badge,
            'sound' => 'newMessage.wav'
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result)
            echo 'Error, notification not sent' . PHP_EOL;
        else
            echo 'notification sent!' . PHP_EOL;

        // Close the connection to the server
        fclose($fp);

    }
}

}
