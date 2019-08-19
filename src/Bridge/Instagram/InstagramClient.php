<?php


namespace App\Bridge\Instagram;


use Symfony\Component\HttpClient\HttpClient;

const URL = "https://api.instagram.com/v1/users/self/media/recent/";
const COMMENTSURL="https://api.instagram.com/v1/media/";
const ONEPHOTOURL = "https://api.instagram.com/v1/media/";

class InstagramClient
{
    private $httpClient;
    function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    function getAllData($access_token)
    {
        $url = URL."?access_token=".$access_token;
        $result = $this->httpClient->request("GET", $url);
        return json_decode($result->getContent());
    }

    function getComments($access_token, $mediaId)
    {
        $url = COMMENTSURL.$mediaId."/comments?access_token=".$access_token;
        $result = $this->httpClient->request("GET", $url);
        return json_decode($result->getContent());
    }

    function getOnePhoto($access_token, $mediaId)
    {
        $url = ONEPHOTOURL.$mediaId."?access_token=".$access_token;
        $result = $this->httpClient->request("GET", $url);
        return json_decode($result->getContent());
    }

}