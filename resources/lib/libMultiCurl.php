<?php
/**
 * This file is used to make the async curl request for the passed URL
 * For the sake of simplicity, this file is having limited functionality but it can be modified to turn it into a full independent library with multiple options
 */
class MultiCURL
{
    /**
     * This function is used to create the async curl requests and return the output array
     * @param array $data pass in the array of URLs
     * @return array of CURL output against passed URLs
     */
    public function GetURLData($data)
    {
        // initialize the CURL requests
        $mCurl = curl_multi_init();
        foreach ($data as $key => $URL)
        {
            // echo "{$URL}\n";
            $ch[$URL] = curl_init();
            curl_setopt_array($ch[$URL], array(
                CURLOPT_URL            => $URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_POST           => false,
                CURLOPT_HEADER         => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_HTTPHEADER     => array('User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/600.7.12 (KHTML, like Gecko) Version/8.0.7 Safari/600.7.12'), // This is required for the sites like Facebook
            ));
            curl_multi_add_handle($mCurl, $ch[$URL]);
        } // foreach ends
        $running = null;
        do
        {
            curl_multi_exec($mCurl, $running);
        } while ($running > 0);

        foreach ($ch as $URL => $r)
        {
            $result[$URL] = curl_multi_getcontent($r);
            curl_multi_remove_handle($mCurl, $r);
        } // foreach ends

        curl_multi_close($mCurl);

        return $result;
    } // function GetURLData ends
} // class MultiCURL ends
