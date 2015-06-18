<?php
// src/AppBundle/Entity/Comments.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity
 * @ORM\Table(name="post_comments")
 * @JMS\ExclusionPolicy("all")
 */
class PostComments
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"getUser"})
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=25)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=25)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
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
     * @var datetime $date
     * @JMS\Expose
     * @ORM\Column(name="date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"getUser"})
     */
    private $date;

    /**
     * @JMS\Expose
     * @ORM\Column(type="text")
     * @JMS\Groups({"getUser"})
     */
    private $text;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\PostComments")
     * @ORM\ManyToOne(targetEntity="PostBaraholka", inversedBy="post_comments")
     * @ORM\JoinColumn(name="post_baraholka_id", referencedColumnName="id")
     */
    private $post_baraholka;



    public function __construct()
    {
        $this->date = new \DateTime();
    }

   

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
     * Set user_id
     *
     * @param integer $userId
     * @return PostComments
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
     * Set username
     *
     * @param string $username
     * @return PostComments
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
     * @return PostComments
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
     * @return PostComments
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
     * Set text
     *
     * @param string $text
     * @return PostComments
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
     * Set avatar
     *
     * @param \Creativer\FrontBundle\Entity\Avatar $avatar
     * @return PostComments
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

    /**
     * Set post_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka
     * @return PostComments
     */
    public function setPostBaraholka(\Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka = null)
    {
        $this->post_baraholka = $postBaraholka;

        return $this;
    }

    /**
     * Get post_baraholka
     *
     * @return \Creativer\FrontBundle\Entity\PostBaraholka 
     */
    public function getPostBaraholka()
    {
        return $this->post_baraholka;
    }
}
