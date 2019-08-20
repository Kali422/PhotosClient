<?php


namespace App\Bridge;


use Symfony\Component\HttpClient\HttpClient;


class InstagramClient
{
    private $httpClient;

    function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    function getAllData($access_token)
    {
        $url = $_ENV['IG_MEDIA_URL'] . "?access_token=" . $access_token;
        $result = $this->httpClient->request("GET", $url);
        return json_decode($result->getContent());
    }

    function getComments($access_token, $mediaId)
    {
        $url = $_ENV['IG_ONE_MEDIA_URL'] . $mediaId . "/comments?access_token=" . $access_token;
        $result = $this->httpClient->request("GET", $url);
        return json_decode($result->getContent());
    }

    function getOnePhoto($access_token, $mediaId)
    {
        $url = $_ENV['IG_ONE_MEDIA_URL'] . $mediaId . "?access_token=" . $access_token;
        $result = $this->httpClient->request("GET", $url);
        return json_decode($result->getContent());
    }

}