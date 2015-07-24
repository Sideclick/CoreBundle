<?php
namespace Sc\CoreBundle\Sms\Service\Bulksms;

class Bulksms
{
    
    const username = 'rowanreid';
    const password = 'shizer';

    const port = 5567;
    const url = 'http://bulksms.2way.co.za/eapi/submission/send_sms/2/2.0';

    const sleep = 2;
    const retries = 1;
    
    
    public static function send($message, $msisdn, $username = null, $password = null)
    {
        $sleepTime = self::sleep;

        $postBody = self::sevenBitSms(
            $message, self::msisdnFormat($msisdn), $username, $password
        );
        
        for ($i=0; $i < self::retries; $i++) {

            $result = self::sendMessage($postBody);
            
            if ($result['success']) {

                break 1;
            }

            sleep($sleepTime);
        }

        return $result;
    }

    public static function msisdnFormat( $msisdn )
    {

        $new_msisdn = preg_replace("[^0-9]", "", $msisdn);

        if (substr(ltrim($msisdn), 0, 1) == '+') {

            return $new_msisdn;
        }

        $new_msisdn = (substr($new_msisdn, 0, 2) != '27') ?
            $new_msisdn = '27' . substr($new_msisdn, 1, 11) : $new_msisdn;

        return $new_msisdn;
    }

    /**
    * Taken directly from the bulkSMS website.
    */
    public static function sendMessage($postBody)
    {
        /*
        * Do not supply $postFields directly as an argument to
        * CURLOPT_POSTFIELDS,
        * despite what the PHP documentation suggests: cUrl will turn
        * it into in a
        * multipart formpost, which is not supported:
        */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::url);
        //curl_setopt($ch, CURLOPT_PORT, self::port);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);


        $responseString = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);

        $smsResult = array();
        $smsResult['success'] = 0;
        $smsResult['details'] = '';
        $smsResult['transient_error'] = 0;
        $smsResult['http_statusCode'] = $curlInfo['http_code'];
        $smsResult['api_statusCode'] = '';
        $smsResult['api_message'] = '';
        $smsResult['api_batch_id'] = '';

        if ($responseString == FALSE) {

            $smsResult['details'] .= "cURL error: " . curl_error($ch) . "\n";
        } elseif ($curlInfo['http_code'] != 200) {

            $smsResult['transient_error'] = 1;
            $smsResult['details'] .= "Error: non-200 HTTP status code: " .
            $curlInfo['http_code'] . "\n";
        } else {

            $smsResult['details'] .=
            "Response from server: $responseString\n";
            $apiResult = explode('|', $responseString);
            $statusCode = $apiResult[0];
            $smsResult['api_statusCode'] = $statusCode;
            $smsResult['api_message'] = $apiResult[1];

            if (count($apiResult) != 3) {

                $smsResult['details'] .= "Error: could not parse valid" .
                " return data from server.\n" . count($apiResult);
            } else {

                if ($statusCode == '0') {

                    $smsResult['success'] = 1;
                    $smsResult['api_batch_id'] = $apiResult[2];
                    $smsResult['details'] .= "Message sent - " .
                    "batch ID $apiResult[2]\n";
                } else if ($statusCode == '1') {

                    # Success: scheduled for later sending.
                    $smsResult['success'] = 1;
                    $smsResult['api_batch_id'] = $apiResult[2];
                } else {

                    $smsResult['details'] .= "Error sending: status code " .
                    "[$apiResult[0]] description [$apiResult[1]]\n";
                }

                if ($smsResult['transient_error'] == $statusCode) {

                    $smsResult['transient_error'] = 1;
                }

            }
        }
        curl_close($ch);
        $res = str_replace("\n", " ", $smsResult["details"]);
        
        return $smsResult;
    }

    public static function getApiResponse($apiResponseCode)
    {
        $responses = array(
            '0' => 'In progress',
            '1' => 'Scheduled',
            '22' => 'Internal fatal error',
            '23' => 'Authentication failure',
            '24' => 'Data validation failed',
            '25' => 'You do not have sufficient credits',
            '26' => 'Upstream credits not available',
            '27' => 'You have exceeded your daily quota',
            '28' => 'Upstream quota exceeded',
            '40' => 'Temporarily unavailable',
            '201' => 'Maximum batch size exceeded'
            );

        return $responses[$apiResponseCode];
    }


    public static function formattedServerSesponse($result)
    {
        $thisResult = "";

        if ($result['success']) {

            $thisResult .= "Success: batch ID " .
            $result['api_batch_id']. "API message: ".
            $result['api_message']. "\nFull details " . $result['details'];
        } else {

            $thisResult .= "Fatal error: HTTP status " .
            $result['http_statusCode']. ", API status " .
            $result['api_statusCode']. " API message " .
            $result['api_message']. " full details " .$result['details'];

            if ($result['transient_error']) {

                $thisResult .=  "This is a transient error" .
                " - will retry shortly...";
            }
        }

        return $thisResult;
    }

    public static function makePostBody($postFields)
    {
        $stop_dup_id = 0;
        $postBody = '';

        foreach ($postFields as $key => $value) {

            $postBody .= urlencode($key).'='.urlencode($value).'&';
        }

        $postBody = rtrim($postBody, '&');
        
        return $postBody;
    }

    public static function sevenBitSms($message, $msisdn, $username, $password)
    {
        $postFields = array(
            'username'  => (is_null($username) ? self::username : $username),
            'password'  => (is_null($password) ? self::password : $password),
            'message'   => self::characterResolve($message),
            'msisdn'    => $msisdn
            );

        return self::makePostBody($postFields);
    }

    public static function characterResolve($body)
    {
        $specialChrs = array(
            'ÃŽâ€�'=>'0xD0',
            'ÃŽÂ¦'=>'0xDE',
            'ÃŽâ€œ'=>'0xAC',
            'ÃŽâ€º'=>'0xC2',
            'ÃŽÂ©'=>'0xDB',
            'ÃŽÂ '=>'0xBA',
            'ÃŽÂ¨'=>'0xDD',
            'ÃŽÂ£'=>'0xCA',
            'ÃŽËœ'=>'0xD4',
            'ÃŽÅ¾'=>'0xB1',
            'Ã‚Â¡'=>'0xA1',
            'Ã‚Â£'=>'0xA3',
            'Ã‚Â¤'=>'0xA4',
            'Ã‚Â¥'=>'0xA5',
            'Ã‚Â§'=>'0xA7',
            'Ã‚Â¿'=>'0xBF',
            'Ãƒâ€ž'=>'0xC4',
            'Ãƒâ€¦'=>'0xC5',
            'Ãƒâ€ '=>'0xC6',
            'Ãƒâ€¡'=>'0xC7',
            'Ãƒâ€°'=>'0xC9',
            'Ãƒâ€˜'=>'0xD1',
            'Ãƒâ€“'=>'0xD6',
            'ÃƒËœ'=>'0xD8',
            'ÃƒÅ“'=>'0xDC',
            'ÃƒÅ¸'=>'0xDF',
            'ÃƒÂ '=>'0xE0',
            'ÃƒÂ¤'=>'0xE4',
            'ÃƒÂ¥'=>'0xE5',
            'ÃƒÂ¦'=>'0xE6',
            'ÃƒÂ¨'=>'0xE8',
            'ÃƒÂ©'=>'0xE9',
            'ÃƒÂ¬'=>'0xEC',
            'ÃƒÂ±'=>'0xF1',
            'ÃƒÂ²'=>'0xF2',
            'ÃƒÂ¶'=>'0xF6',
            'ÃƒÂ¸'=>'0xF8',
            'ÃƒÂ¹'=>'0xF9',
            'ÃƒÂ¼'=>'0xFC',
        );

        $retMsg = '';
        if (mb_detect_encoding($body, "UTF-8") != "UTF-8") {
            $body = utf8_encode($body);
        }
        for ($i = 0; $i < mb_strlen($body, 'UTF-8'); $i++) {
            $c = mb_substr($body, $i, 1, 'UTF-8');
            if (isset($specialChrs[$c])) {
                $retMsg .= chr($specialChrs[$c]);
            } else {
                $retMsg .= $c;
            }
        }
        return $retMsg;
    }
}
