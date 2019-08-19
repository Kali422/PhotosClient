<?php


namespace App\Bridge;


use Symfony\Component\HttpClient\HttpClient;

class GooglePhotosClient
{
    const ALBUMSURL = "https://photoslibrary.googleapis.com/v1/albums";
    const MEDIAURL = "https://photoslibrary.googleapis.com/v1/mediaItems";
    private $httpClient;

    function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    function getAlbums($access_token)
    {
        $url = self::ALBUMSURL . "?access_token=" . $access_token;
        $dataRaw = $this->httpClient->request("GET", $url)->getContent();
        return json_decode($dataRaw);
    }

    function getPhotosFromAlbum($access_token, $albumId)
    {
        $page_token=null;
        $dataAll=[];
        do
        {
        $url = self::MEDIAURL . ":search?access_token=" . $access_token;
        $dataRaw = $this->httpClient->request("POST", $url, [
            'body' => [
                'albumId' => $albumId,
                'pageSize' => 100,
                'pageToken'=>$page_token
            ]
        ])->getContent();
        $data= json_decode($dataRaw);
            if (isset($data->mediaItems)) {
                $dataAll=array_merge($dataAll, $data->mediaItems);
            }
            if (isset($data->nextPageToken)) {
                $page_token = $data->nextPageToken;
            } else $page_token = null;
        } while ($page_token != null);

        return $dataAll;
    }

    function getAllPhotos($access_token, $page_token = null)
    {
        $page_token = null;
        $dataAll = [];
        do {
            $url = self::MEDIAURL . "?access_token=" . $access_token . "&pageToken=" . $page_token . "&pageSize=100";
            $dataRaw = $this->httpClient->request("GET", $url)->getContent();
            $data = json_decode($dataRaw);
            if (isset($data->mediaItems)) {
                $dataAll = array_merge($dataAll, $data->mediaItems);
            }
            if (isset($data->nextPageToken)) {
                $page_token = $data->nextPageToken;
            } else $page_token = null;
        } while ($page_token != null);

        return $dataAll;
    }

    public function getOnePhoto($access_token, $id)
    {
        $url = self::MEDIAURL."/".$id."?access_token=".$access_token;
        $dataRaw = $this->httpClient->request("GET", $url)->getContent();
        return json_decode($dataRaw);
    }
}