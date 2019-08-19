<?php


namespace App\Repository\GooglePhotos;


use App\Entity\GooglePhotoAlbum;
use App\Entity\Photo;

class GooglePhotosFactory
{
    function createAlbums($data)
    {
        $albums = [];
        foreach ($data as $album) {
            array_push($albums, $this->createAlbum($album));
        }
        return $albums;
    }

    function createAlbum($data)
    {
        $id = $data->id;
        $title = $data->title;
        $mediaCount = 0;
        if (isset($data->mediaItemsCount)) {
            $mediaCount = $data->mediaItemsCount;
        }
        $coverPhotoUrl = $data->coverPhotoBaseUrl;
        $url = $data->productUrl;

        return new GooglePhotoAlbum($id, $title, $mediaCount, $coverPhotoUrl, $url);
    }

    function createPhoto($data)
    {
        $id = $data->id;
        $url = $data->baseUrl;
        $width = $data->mediaMetadata->width;
        $height = $data->mediaMetadata->height;
        $created_time = $data->mediaMetadata->creationTime;
        $text = $data->filename;

        return new Photo($id, $url, $width, $height, $created_time, $text);
    }

    function createArrayOfPhotos($data)
    {
        $photos = [];
        foreach ($data as $photo) {
            array_push($photos, $this->createPhoto($photo));
        }
        return $photos;
    }

}