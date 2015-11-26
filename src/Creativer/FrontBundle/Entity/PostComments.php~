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
     * @JMS\Groups({"getUser", "getPostById", "getPostById" ,"getCommentBaraholka"})
     * @JMS\Expose
     */
private $id;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\User")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="post_comments", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Groups({"getUser", "getPostById", "getCommentBaraholka"})
     **/
    private $user;

    /**
     * @var datetime $date
     * @JMS\Expose
     * @ORM\Column(name="date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"getUser", "getPostById", "getPostById", "getCommentBaraholka"})
     */
    private $date;

    /**
     * @JMS\Expose
     * @ORM\Column(type="text")
     * @JMS\Groups({"getUser", "getPostById", "getPostById", "getCommentBaraholka"})
     */
    private $text;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\PostBaraholka")
     * @ORM\ManyToOne(targetEntity="PostBaraholka", inversedBy="post_comments")
     * @ORM\JoinColumn(name="post_baraholka_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
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
     * Set user
     *
     * @param \Creativer\FrontBundle\Entity\User $user
     * @return PostComments
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
