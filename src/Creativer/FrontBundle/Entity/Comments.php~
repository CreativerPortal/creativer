<?php
// src/AppBundle/Entity/Comments.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
 * @JMS\ExclusionPolicy("all")
 */
class Comments
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
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\User")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Groups({"getUser"})
     **/
    private $user;

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
     * @JMS\Type("Posts")
     * @ORM\ManyToOne(targetEntity="Posts", inversedBy="comments")
     * @ORM\JoinColumn(name="posts_id", referencedColumnName="id")
     **/
    private $post;

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
     * @return Comments
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
     * @return Comments
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
     * @return Comments
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
     * Set post
     *
     * @param \Creativer\FrontBundle\Entity\Posts $post
     * @return Comments
     */
    public function setPost(\Creativer\FrontBundle\Entity\Posts $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Creativer\FrontBundle\Entity\Posts 
     */
    public function getPost()
    {
        return $this->post;
    }
}
