<?php


namespace App\Entity;


class InstagramComment
{
    private $id;
    private $userFrom;
    private $text;
    private $createdTime;

    function __construct($id, $userFrom, $text, $createdTime)
    {
        $this->id = $id;
        $this->userFrom = $userFrom;
        $this->text = $text;
        $this->createdTime = $createdTime;
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
    public function getUserFrom()
    {
        return $this->userFrom;
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
    public function getCreatedTime()
    {
        return $this->createdTime;
    }



}