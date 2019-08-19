<?php


namespace App\Repository\Instagram;


use App\Bridge\InstagramClient;

class InstagramService
{
    /**
     * @var InstagramFactory
     */
    private $factory;
    /**
     * @var InstagramClient
     */
    private $client;

    function __construct(InstagramFactory $factory, InstagramClient $client)
    {

        $this->factory = $factory;
        $this->client = $client;
    }

    function getPhotos($access_token)
    {
        $data = $this->client->getAllData($access_token);
        $photos = $this->factory->createArrayOfPhotos($data);
        return $photos;
    }

    function getComments($access_token, $mediaId)
    {
        $commentsRaw = $this->client->getComments($access_token, $mediaId);
        $comments = $this->factory->createArrayOfComments($commentsRaw->data);
        return $comments;
    }

    function getOnePhoto($access_token, $mediaId)
    {
        $photoRaw = $this->client->getOnePhoto($access_token, $mediaId);
        $photo = $this->factory->createPhotoInstance($photoRaw->data);
        return $photo;
    }

    function slicePhotosArray($photos)
    {
        $count = count($photos);
        $photosSliced = [
            '1stColumn' => [],
            '2ndColumn' => [],
            '3rdColumn' => [],
            '4thColumn' =>[],
        ];
        for ($i = 0; $i < $count; $i = $i + 4) {
            if (isset($i)) {
                array_push($photosSliced['1stColumn'], $photos[$i]);
            }
            if (isset($photos[$i + 1])) {
                array_push($photosSliced['2ndColumn'], $photos[$i+1]);
            }
            if (isset($photos[$i + 2])) {
                array_push($photosSliced['3rdColumn'], $photos[$i+2]);
            }
            if (isset($photos[$i + 3])) {
                array_push($photosSliced['4thColumn'], $photos[$i+3]);
            }

        }

        return $photosSliced;
    }

}