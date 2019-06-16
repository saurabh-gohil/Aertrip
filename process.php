<?php
ini_set("display_errors", 0);
error_reporting(0);

require_once ("resources/lib/libMultiCurl.php");

/**
 * This the mail class which handles the validation of received URLs, getting the curl response from parent class and giving out the final response as required
 */
class ProcessURLs extends MultiCURL
{
    public $receivedURLs = array();
    public $finalResult  = array();

    /**
     * Validate the received URL and if an invalid URL found proper message is added against the URL
     * @param array $urls Used passes array of URLs
     */
    public function ValidateURLs($urls)
    {
        foreach ($urls as $url)
        {
            if (filter_var($url, FILTER_VALIDATE_URL))
            {
                $this->receivedURLs[$url] = $url;
            }
            else
            {
                $this->finalResult[$url] = "Invalid URL Received";
            }
        } // ValidateURLs ends
    } // class ProcessURLs ends

    /**
     * Hit the parent class to get the curl output and do some other operation if required
     */
    public function GetDataFromMultiCURL()
    {
        return parent::GetURLData($this->receivedURLs);
    } //function GetDataFromMultiCURL ends

    /**
     * Get the final required output which will be sent to the client
     * @param array $result pass the result generated by GetDataFromMultiCURL
     */
    public function GetFinalResult($result)
    {
        foreach ($result as $key => $pageContent)
        {
            $dom = new DomDocument();
            @$dom->loadHTML($pageContent);
            $nodes             = $dom->getElementsByTagName('title');
            $finalResult[$key] = ($nodes->item(0)->nodeValue) ? $nodes->item(0)->nodeValue : "";
        } // foreach ends
        return array_merge($finalResult, $this->finalResult);
    } // function GetFinalResult ends
} // class ProcessURLs ends

$responseArray["status"] = 0;
$responseArray["urls"]   = array();

if ($_POST && count($_POST) > 0)
{
    $processURLs = new ProcessURLs();
    $processURLs->ValidateURLs($_POST["urls"]);
    $result                = $processURLs->GetDataFromMultiCURL();
    $responseArray["urls"] = $processURLs->GetFinalResult($result);
}
echo json_encode($responseArray);