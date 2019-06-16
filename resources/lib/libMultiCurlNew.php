<?php
/**
 *
 */
class MultiCURL
{
    public $data;
    private $result;

    public function __construct()
    {
        $this->data   = array();
        $this->result = array();
    } // function __construct ends

    public function ProcessData($data)
    {
        // Add a validation
        $this->data = $data;
        $result     = self::GetURLData();
        self::GetFinalResult($result);
    } // function ProcessData ends

    private function GetURLData()
    {
        $rollingWindow = 5;
        $rollingWindow = (count($this->data) < $rollingWindow) ? count($this->data) : $rollingWindow;
        print_r($rollingWindow);
        // initialize the CURL requests
        $mCurl = curl_multi_init();
        for ($i = 0; $i < $rollingWindow; $i++)
        {
            // echo "{$URL}\n";
            $ch[$i] = curl_init();
            curl_setopt_array($ch[$i], array(
                CURLOPT_URL            => $this->data[$i],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_POST           => false,
                CURLOPT_HEADER         => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_HTTPHEADER     => array('User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/600.7.12 (KHTML, like Gecko) Version/8.0.7 Safari/600.7.12'),
            ));
            curl_multi_add_handle($mCurl, $ch[$i]);
        } // foreach ends
        $i--;
        // print_r($mCurl);
        $running = null;
        do
        {
            while (($execrun = curl_multi_exec($mCurl, $running)) == CURLM_CALL_MULTI_PERFORM);
            if ($execrun != CURLM_OK)
            {
                break;
            }
            while ($done = curl_multi_info_read($mCurl))
            {
                $info = curl_getinfo($done["handle"]);
                print_r($info);
                if ($info["http_code"] == 200)
                {
                    // print_r(parse_url($info["url"]));
                    $seq[] = parse_url($info["url"])["host"];
                    $out[] = curl_multi_getcontent($info["handle"]);

                    if (isset($this->data[$i + 1]))
                    {
                        // start a new request (it's important to do this before removing the old one)
                        $ch = curl_init();
                        // $options[CURLOPT_URL] = $urls[$i++]; // increment i
                        curl_setopt_array($ch[$i], array(
                            CURLOPT_URL            => $this->data[$i++],
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT        => 30,
                            CURLOPT_CONNECTTIMEOUT => 30,
                            CURLOPT_POST           => false,
                            CURLOPT_HEADER         => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_MAXREDIRS      => 5,
                            CURLOPT_HTTPHEADER     => array('User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/600.7.12 (KHTML, like Gecko) Version/8.0.7 Safari/600.7.12'),
                        ));
                        curl_multi_add_handle($mCurl, $ch);
                    }
                    curl_multi_remove_handle($mCurl, $done['handle']);
                }
            }
            // print_r($running);
            /*if ($done = curl_multi_info_read($mCurl))
            {
            $info = curl_getinfo($done["handle"]);
            print_r($info);
            if ($info["http_code"] == 200)
            {
            // print_r(parse_url($info["url"]));
            $seq[] = parse_url($info["url"])["host"];
            }
            }*/
            // curl_multi_exec($mCurl, $running);
        } while ($running);
        // print_r($seq);
        /*print_r(curl_multi_info_read($mCurl));
        foreach ($ch as $key => $r)
        {
        $result[$key] = curl_multi_getcontent($r);
        curl_multi_remove_handle($mCurl, $r);
        } // foreach ends
         */
        curl_multi_close($mCurl);

        // print_r($result);
        return $result;
    } // function GetURLData ends

    private function GetFinalResult($result)
    {
        foreach ($result as $key => $pageContent)
        {
            $dom = new DomDocument();
            @$dom->loadHTML($pageContent);
            $nodes              = $dom->getElementsByTagName('title');
            $this->result[$key] = ($nodes->item(0)->nodeValue) ? $nodes->item(0)->nodeValue : "";
        } // foreach ends
        print_r($this->result);
    } // function GetFinalResult ends
} // class MultiCURL ends

$data[] = "http://google.com";
$data[] = "http://yahoo.com";
$data[] = "http://facebook.com";

$multi  = new MultiCURL();
$result = $multi->ProcessData($data);
print_r($result);
