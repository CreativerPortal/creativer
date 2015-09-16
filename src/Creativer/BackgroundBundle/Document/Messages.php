<?php
namespace Creativer\BackgroundBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;


/**
 * @MongoDB\Document(collection="messages")
 */
class Messages
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Int
     */
    protected $sender;

    /**
     * @MongoDB\Int
     */
    protected $receiver;

    /**
     * @MongoDB\String
     */
    protected $text;

    /**
     * @MongoDB\Boolean
     */
    protected $reviewed;

    /**
     * @MongoDB\Date
     */
    protected $date;

    /**
     * @MongoDB\Collection
     */
    protected $id_users;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sender
     *
     * @param int $sender
     * @return self
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Get sender
     *
     * @return int $sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set receiver
     *
     * @param int $receiver
     * @return self
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
        return $this;
    }

    /**
     * Get receiver
     *
     * @return int $receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set reviewed
     *
     * @param boolean $reviewed
     * @return self
     */
    public function setReviewed($reviewed)
    {
        $this->reviewed = $reviewed;
        return $this;
    }

    /**
     * Get reviewed
     *
     * @return boolean $reviewed
     */
    public function getReviewed()
    {
        return $this->reviewed;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set idUsers
     *
     * @param collection $idUsers
     * @return self
     */
    public function setIdUsers($idUsers)
    {
        $this->id_users = $idUsers;
        return $this;
    }

    /**
     * Get idUsers
     *
     * @return collection $idUsers
     */
    public function getIdUsers()
    {
        return $this->id_users;
    }
}
