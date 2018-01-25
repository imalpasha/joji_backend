<?php

namespace Api\Controllers;

use App\User;
use App\ActiveUser;
use App\UserProfile;
use Dingo\Api\Facade\API;
use Illuminate\Http\Request;
use Api\Requests\UserRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ChatController extends BaseController
{

    public function chat(Request $request)
    {
        $this->push();
        return response()->json(array(
            'status' => 'success'
        ));
    }


    public function push()
    {

        $registration_ids = array();

        array_push($registration_ids, "APA91bHGMWi4N4GXscyU5kdhSxhmiliSj-BWIpxp8qXlFBPoCAnk1pMa7PuGCaaQa6Y-gCRzmLSfiL5SlUq6UhHiF2E5JvEH0icbbkPDzfomH8waNcTvt-0");
        array_push($registration_ids, "APA91bEOFL3wEIEyTLEpkbU2EKkNEhxE26KV_vWCbBW3cRdsqF1-B9hoSXt9c_cvd7aKnfX3WjY-V8jQH9U0LkzD7v5nuY6m-V6jthEzng2ifTscoIGQRGA");

        $apiKey = 'AIzaSyCl_y1BmC_7HfP1dadPe8SnOgcqZyzQaqE';

        $data = array();
        $data['title'] = "TITLE";
        $data['body'] = "MESSAGE";
        $data['sound'] = 'default';

        $fields = array(
            'registration_ids' => $registration_ids,
            'priority' => 'high',
            'content-available' => 'true',
            'data'=>array('message'=>$data)
        );

        // Set POST variables
        $url = 'https://gcm-http.googleapis.com/gcm/send';

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $response = array();

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            $response['error'] = TRUE;
            $response['message'] = 'Unable to send test push notification';
            return $response;
            exit;
        }

        // Close connection
        curl_close($ch);
        $response['error'] = FALSE;
        $response['message'] = 'Test push message sent successfully!';

        /*
        echo "send funcv ";
        global $db2;

        // Put your private key's passphrase here:
        $passphrase = 'firefly123';

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://gateway.sandbox.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp){
            //exit("Failed to connect: $err $errstr" . PHP_EOL);
    // 		notification_log("Error", "Send IOS Notification Failed", "Failed to connect with error no ".$err." ".$errstr);
            set_process_status($pid,"Failed","Failed to connect with error: ".$err." ".$errstr." for deviceToken '".$deviceToken."'");
        }

        /* if connected to APNS, continue send message */
        /*echo "connected to APNS";

        // Create the payload body
        $body['aps'] = array(
            //'title' => '' ?
            'alert' => $message,
            'sound' => 'default',
            'badge' => 1,
            );

        // Encode the payload as JSON
        $payload = json_encode($body);

        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);*/

        return $result;//s$registration_ids; /* true on success send */

    }


    public function sendNotification(){

    }



}