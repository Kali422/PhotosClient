<?php


namespace App\Repository;


use Symfony\Component\HttpClient\HttpClient;

class ApiRepostiory
{
    function getApiData($endpoint, $access_token)
    {
        $url = 'http://localhost:8001/api/' . $endpoint . '?access_token=' . $access_token;
        $httpClient = HttpClient::create();
        return json_decode($httpClient->request('GET', $url)->getContent());
    }
}