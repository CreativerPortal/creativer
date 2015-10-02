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
     * @JMS\Groups({"getUser", "getPost"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=11)
     * @JMS\Expose
     * @JMS\Groups({"getUser", "getPost"})
     */
    private $wall_id;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\User")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Groups({"getUser", "getPost"})
     **/
    private $user;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"getUser", "getPost"})
     */
    private $date;


    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"getUser", "getPost"})
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
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\PostImages")
     * @ORM\OneToMany(targetEntity="PostImages", mappedBy="post")
     * @JMS\Groups({"getUser", "getPost"})
     **/
    private $post_images;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set user
     *
     * @param \Creativer\FrontBundle\Entity\User $user
     * @return Posts
     */
    public function setUser(\Creativer\FrontBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Creativer\FrontBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
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
     * Add post_images
     *
     * @param \Creativer\FrontBundle\Entity\PostImages $postImages
     * @return Posts
     */
    public function addPostImage(\Creativer\FrontBundle\Entity\PostImages $postImages)
    {
        $this->post_images[] = $postImages;

        return $this;
    }

    /**
     * Remove post_images
     *
     * @param \Creativer\FrontBundle\Entity\PostImages $postImages
     */
    public function removePostImage(\Creativer\FrontBundle\Entity\PostImages $postImages)
    {
        $this->post_images->removeElement($postImages);
    }

    /**
     * Get post_images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostImages()
    {
        return $this->post_images;
    }
}
