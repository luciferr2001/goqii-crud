<?php

namespace App\Traits;

use App\Models\CommonModel;

trait CommonTraits
{

    public function makeOutput($data, $code, $message)
    {
        return json_encode(array('data' => $data, 'code' => (int) $code, 'message' => $message));
    }

    public static function generate_password($random, $length = 10)
    {
        if ($random) {
            // Characters to be used in the password
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
            // Get the length of the character list
            $chars_length = strlen($chars);
            // Initialize the password variable
            $password = '';
            // Generate random characters to form the password
            for ($i = 0; $i < $length; $i++) {
                $password .= $chars[rand(0, $chars_length - 1)];
            }
            return $password;
        } else {
            return CONSTANT_PASSWORD;
        }
    }

    public function generate_uuidv4()
    {
        // Set maximum number of retries
        $maxRetries = 10;
        // Retry counter
        $retry = 0;
        do {
            // Generate 16 bytes (128 bits) of random data
            $data = random_bytes(16);
            // Set version to 0100
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            // Set bits 6-7 to 10
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            // Output the 36 character UUID.
            $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
            // Check if UUID already exists in the system
            if (!$this->uuid_exists($uuid)) {
                // Unique UUID generated, return
                return $uuid;
            }
            // If UUID exists, increment retry counter
            $retry++;
        } while ($retry < $maxRetries);

        // If maximum retries reached without success, throw an exception or handle accordingly
        throw new \Exception("Failed to generate a unique UUID after $maxRetries retries.");
    }

    // Example function to check if UUID already exists in the system
    private function uuid_exists($uuid)
    {
        $common_model = new CommonModel();
        $common_model->checkRecordExists(array('uuid' => $uuid), MAIN_USER);
        if ($common_model) {
            return false;
        } else {
            return true;
        }
    }
}
