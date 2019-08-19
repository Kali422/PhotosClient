<?php


namespace App\Entity;


class Photo
{
    private $id;
    private $url;
    private $width;
    private $height;
    private $created_time;
    private $text;
    private $likes;
    private $comments;
    private $link;

    function __construct($id, $url, $width, $height, $created_time, $text=null, $likes=null, $comments=null, $link=null)
    {

        $this->id = $id;
        $this->url = $url;
        $this->width = $width;
        $this->height = $height;
        $this->created_time = $created_time;
        $this->text = $text;
        $this->likes = $likes;
        $this->comments = $comments;
        $this->link = $link;
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return mixed
     */
    public function getCreatedTime()
    {
        return $this->created_time;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }



}