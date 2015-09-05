<?php
// src/AppBundle/Entity/Images.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\MaxDepth;



/**
 * @ORM\Entity
 * @ORM\Table(name="post_images")
 * @JMS\ExclusionPolicy("all")
 */
class PostImages
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
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $path;

    /**
     * @ORM\Column(type="integer", length=11)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $height;

    /**
     * @ORM\Column(type="integer", length=11)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $width;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\PostImages")
     * @ORM\ManyToOne(targetEntity="Posts", inversedBy="post_images")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * @JMS\Groups({"getUser"})
     **/
    private $post;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $date;


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
     * Set name
     *
     * @param string $name
     * @return PostImages
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return PostImages
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return PostImages
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return PostImages
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return PostImages
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
     * Set post
     *
     * @param \Creativer\FrontBundle\Entity\Posts $post
     * @return PostImages
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
