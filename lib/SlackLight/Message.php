<?php
/**
 * Created by PhpStorm.
 * User: clk
 * Date: 27.05.18
 * Time: 09:46
 */

namespace SlackLight;


class Message extends Entity {
    private $authorId;
    private $channelId;
    private $text;
    private $timestamp;
    private $edited;

    public function __construct(int $id, int $authorId, int $channelId, string $text, string $creationDate, int $edited) {
        parent::__construct($id);
        $this->authorId = $authorId;
        $this->channelId = $channelId;
        $this->text = $text;
        $this->timestamp = $creationDate;
        $this->edited = $edited != 0;
    }

    public function getId()
    {
        return $this->id;
    }


    public function getAuthorId()
    {
        return $this->authorId;
    }


    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }


    public function getChannelId()
    {
        return $this->channelId;
    }

    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    }


    public function getText()
    {
        return $this->text;
    }


    public function setText($text)
    {
        $this->text = $text;
    }


    public function getTimestamp()
    {
        return $this->timestamp;
    }


    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }


    public function isEdited()
    {
        return $this->edited;
    }


    public function setEdited($edited)
    {
        $this->edited = $edited;
    }


}