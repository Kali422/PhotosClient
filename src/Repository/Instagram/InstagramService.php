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



}