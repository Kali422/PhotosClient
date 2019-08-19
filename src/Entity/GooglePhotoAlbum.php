<?php


namespace App\Entity;


class GooglePhotoAlbum
{
    private $id;
    private $title;
    private $mediaCount;
    private $coverPhotoUrl;
    private $url;

    function __construct($id, $title, $mediaCount, $coverPhotoUrl, $url)
    {
        $this->id = $id;
        $this->title = $title;
        $this->coverPhotoUrl = $coverPhotoUrl;
        $this->url = $url;
        $this->mediaCount = $mediaCount;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getCoverPhotoUrl()
    {
        return $this->coverPhotoUrl;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getMediaCount()
    {
        return $this->mediaCount;
    }




}