<?php


namespace App\Repository;


use Symfony\Component\HttpClient\HttpClient;

class ApiRepostiory
{
    function getApiData($endpoint, $access_token)
    {
        $url = $_ENV['API_URL'] . $endpoint . '?access_token=' . $access_token;
        $httpClient = HttpClient::create();
        return json_decode($httpClient->request('GET', $url)->getContent());
    }
}