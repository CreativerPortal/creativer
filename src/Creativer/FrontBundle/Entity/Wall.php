<?php
// src/AppBundle/Entity/Wall.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity
 * @ORM\Table(name="wall")
 * @JMS\ExclusionPolicy("all")
 */
class Wall
{
    public function __construct() {
        $this->posts = new ArrayCollection();
        $this->date = new \DateTime();
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"getUser"})
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="wall")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;


    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Posts")
     * @ORM\OneToMany(targetEntity="Posts", mappedBy="wall")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Groups({"getUser"})
     **/
    private $posts;

    /**
     * @var datetime $date
     * @JMS\Expose
     * @ORM\Column(name="date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $date;

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
     * Add posts
     *
     * @param \Creativer\FrontBundle\Entity\Posts $posts
     * @return Wall
     */
    public function addPost(\Creativer\FrontBundle\Entity\Posts $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \Creativer\FrontBundle\Entity\Posts $posts
     */
    public function removePost(\Creativer\FrontBundle\Entity\Posts $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set user
     *
     * @param \Creativer\FrontBundle\Entity\User $user
     * @return Wall
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
     * Set date
     *
     * @param \DateTime $date
     * @return Wall
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
}
