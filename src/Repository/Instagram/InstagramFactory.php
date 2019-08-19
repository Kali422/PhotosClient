<?php


namespace App\Repository\Instagram;


use App\Entity\InstagramComment;
use App\Entity\Photo;
use DateTime;
use stdClass;

class InstagramFactory
{

    function createArrayOfPhotos(stdClass $result): array
    {
        $counter = count($result->data);
        $photos = [];
        for ($i = 0; $i < $counter; $i++) {
            $photo = $this->createPhotoInstance($result->data[$i]);
            array_push($photos, $photo);
        }
        return $photos;
    }

    function createPhotoInstance(stdClass $data): Photo
    {
        $id = $data->id;
        $url = $data->images->standard_resolution->url;
        $width = $data->images->standard_resolution->width;
        $height = $data->images->standard_resolution->height;
        $created_time = $this->convertDateTime($data->created_time);
        $text = null;
        if (null != $data->caption) {
            $text = $data->caption->text;
        }
        $likes = $data->likes->count;
        $comments = $data->comments->count;
        $link = $data->link;

        $photo = new Photo($id, $url, $width, $height, $created_time, $text, $likes, $comments, $link);
        return $photo;
    }

    function createArrayOfComments($data): array
    {
        $comments = [];
        foreach ($data as $comment) {
            $comment = $this->createComment($comment);
            array_push($comments, $comment);
        }
        return $comments;
    }

    function createComment(stdClass $data): InstagramComment
    {
        $id = $data->id;
        $from = $data->from->username;
        $text = $data->text;
        $createdTime = $this->convertDateTime($data->created_time);
        return new InstagramComment($id, $from, $text, $createdTime);
    }

    function convertDateTime(int $time): string
    {
        $date = new DateTime();
        $date->setTimestamp($time);
        return $date->format('Y-m-d H:i:s');
    }
}