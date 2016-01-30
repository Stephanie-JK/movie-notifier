<?php

namespace App;

class GCM
{
    public $url = 'https://android.googleapis.com/gcm/send';

    public $serverApiKey = '';

    public $devices = [];

    /**
     *  The constructor.
     */
    public function __construct()
    {
        $this->serverApiKey = config('gcm.key');
    }

    public static function to($devices)
    {
        $obj = new self();
        $obj->setDevices($devices);

        return $obj;
    }

    /**
     * Set the devices to send.
     *
     * @param $deviceIds
     */
    public function setDevices($deviceIds)
    {
        if (is_array($deviceIds)) {
            $this->devices = $deviceIds;
        } else {
            $this->devices = [$deviceIds];
        }
    }

    /**
     * Send the message to the device.
     *
     * @param       $message
     * @param array $data Array of data to accompany the message
     *
     * @return mixed
     */
    public function send($message, $data = [])
    {
        if (!is_array($this->devices) || count($this->devices) == 0) {
            $this->error('No devices set');
        }

        if (strlen($this->serverApiKey) < 8) {
            $this->error('Server API Key not set');
        }

        $fields = [
            'registration_ids' => $this->devices,
            'data'             => ['message' => $message],
        ];

        if (count($data)) {
            foreach ($data as $key => $value) {
                $fields['data'][$key] = $value;
            }
        }

        $headers = [
            'Authorization: key='.$this->serverApiKey,
            'Content-Type: application/json',
        ];

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Avoids problem with https certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

    public function error($msg)
    {
        echo 'Android send notification failed with error:';
        echo "\t".$msg;
        exit(1);
    }
}
