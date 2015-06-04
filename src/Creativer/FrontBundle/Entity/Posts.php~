<?php
// src/AppBundle/Entity/Posts.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 * @JMS\ExclusionPolicy("all")
 */
class Posts
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=11)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $wall_id;

    /**
     * @ORM\Column(type="integer", length=11)
     * @JMS\Groups({"getUser"})
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=25)
     * @JMS\Groups({"getUser"})
     * @JMS\Expose
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=25)
     * @JMS\Groups({"getUser"})
     * @JMS\Expose
     */
    private $lastname;

    /**
     * @JMS\Expose
     * @ORM\ManyToOne(targetEntity="Avatar")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     * @JMS\Groups({"getUser"})
     */
    private $avatar;

    /**
     * @var \DateTime $date
     * @JMS\Expose
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"getUser"})
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"getUser"})
     * @JMS\Expose
     */
    private $text;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Wall")
     * @ORM\ManyToOne(targetEntity="Wall", inversedBy="posts")
     * @ORM\JoinColumn(name="wall_id", referencedColumnName="id")
     **/
    private $wall;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Comments")
     * @ORM\OneToMany(targetEntity="Comments", mappedBy="post")
     * @JMS\Groups({"getUser"})
     **/
    private $comments;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set wall_id
     *
     * @param integer $wallId
     * @return Posts
     */
    public function setWallId($wallId)
    {
        $this->wall_id = $wallId;

        return $this;
    }

    /**
     * Get wall_id
     *
     * @return integer 
     */
    public function getWallId()
    {
        return $this->wall_id;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return Posts
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Posts
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set wall
     *
     * @param \Creativer\FrontBundle\Entity\Wall $wall
     * @return Posts
     */
    public function setWall(\Creativer\FrontBundle\Entity\Wall $wall = null)
    {
        $this->wall = $wall;

        return $this;
    }

    /**
     * Get wall
     *
     * @return \Creativer\FrontBundle\Entity\Wall 
     */
    public function getWall()
    {
        return $this->wall;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->date = new \DateTime();

    }

    /**
     * Add comments
     *
     * @param \Creativer\FrontBundle\Entity\Comments $comments
     * @return Posts
     */
    public function addComment(\Creativer\FrontBundle\Entity\Comments $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Creativer\FrontBundle\Entity\Comments $comments
     */
    public function removeComment(\Creativer\FrontBundle\Entity\Comments $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Posts
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Posts
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }


    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Posts
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set avatar
     *
     * @param \Creativer\FrontBundle\Entity\Avatar $avatar
     * @return Posts
     */
    public function setAvatar(\Creativer\FrontBundle\Entity\Avatar $avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return \Creativer\FrontBundle\Entity\Avatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}
