<?php


namespace App\Repository;


use App\Entity\GooglePhotoAlbum;
use App\Entity\InstagramComment;
use App\Entity\Photo;

class ControllerRepository
{
    function curlApiRequest($host, $code, $redirectUrl, $clientId, $clientSecret, $grantType = "authorization_code")
    {
        $apiData = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUrl,
            'grant_type' => $grantType,
            'code' => $code
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_POST, count($apiData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $jsonData = curl_exec($ch);
        curl_close($ch);


        return json_decode($jsonData);

    }

    function slicePhotosArray($photos)
    {
        $count = count($photos);
        $photosSliced = [
            '1stColumn' => [],
            '2ndColumn' => [],
            '3rdColumn' => [],
            '4thColumn' => [],
        ];
        for ($i = 0; $i < $count; $i = $i + 4) {
            if (isset($i)) {
                array_push($photosSliced['1stColumn'], $photos[$i]);
            }
            if (isset($photos[$i + 1])) {
                array_push($photosSliced['2ndColumn'], $photos[$i + 1]);
            }
            if (isset($photos[$i + 2])) {
                array_push($photosSliced['3rdColumn'], $photos[$i + 2]);
            }
            if (isset($photos[$i + 3])) {
                array_push($photosSliced['4thColumn'], $photos[$i + 3]);
            }

        }

        return $photosSliced;
    }


    function castToPhotos(array $photosBefore)
    {
        $photosAfter = [];
        foreach ($photosBefore as $photo) {
            $photoinstance = $this->castToPhoto($photo);
            array_push($photosAfter, $photoinstance);
        }
        return $photosAfter;
    }

    function castToPhoto(\stdClass $photo)
    {
        return new Photo($photo->id, $photo->url, $photo->width, $photo->height, $photo->createdTime, $photo->text, $photo->likes, $photo->comments, $photo->link);

    }

    function castToComments(array $commentsBefore)
    {
        $commentsAfter = [];
        foreach ($commentsBefore as $comment) {
            $commentInstance = $this->castToComment($comment);
            array_push($commentsAfter, $commentInstance);
        }
        return $commentsAfter;
    }

    function castToComment(\stdClass $comment)
    {
        return new InstagramComment($comment->id, $comment->userFrom, $comment->text, $comment->createdTime);

    }

    function castToAlbums(array $albums)
    {
        $output = [];
        foreach ($albums as $album)
        {
            $albumInstance = new GooglePhotoAlbum($album->id, $album->title, $album->mediaCount, $album->coverPhotoUrl, $album->url);
            array_push($output,$albumInstance);
        }
        return $output;
    }

}