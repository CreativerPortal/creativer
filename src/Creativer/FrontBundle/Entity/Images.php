<?php
// src/AppBundle/Entity/Images.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\MaxDepth;



/**
 * @ORM\Entity
 * @ORM\Table(name="images")
 * @JMS\ExclusionPolicy("all")
 */
class Images
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"idUserByIdImage", "getUser", "getAlbumComments"})
     * @JMS\Expose
     */
    private $id;


    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Albums")
     * @ORM\ManyToOne(targetEntity="Albums", inversedBy="images")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     * @JMS\MaxDepth(2)
     * @JMS\Groups({"idUserByIdImage"})
     **/
    private $album;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\ImageComments")
     * @ORM\OneToMany(targetEntity="ImageComments", mappedBy="image", cascade={"remove"})
     * @JMS\Groups({"getAlbumComments"})
     **/
    private $image_comments;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getImageComments", "getUser"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose
     */
    private $price = 0;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":0})
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $views;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"getImageComments", "getUser"})
     * @JMS\Expose
     */
    private $text;

    /**
     * @ORM\Column(type="integer", name="likes", nullable=false)
     * @JMS\Expose
     * @JMS\Groups({"getImageComments", "getUser"})
     */
    private $likes = 0;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"getImageComments", "getUser"})
     */
    private $date;


    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Images
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
     * Set views
     *
     * @param integer $views
     * @return Images
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Images
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
     * Set date
     *
     * @param \DateTime $date
     * @return Images
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
     * Set album
     *
     * @param \Creativer\FrontBundle\Entity\Albums $album
     * @return Images
     */
    public function setAlbum(\Creativer\FrontBundle\Entity\Albums $album = null)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return \Creativer\FrontBundle\Entity\Albums 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Add image_comments
     *
     * @param \Creativer\FrontBundle\Entity\ImageComments $imageComments
     * @return Images
     */
    public function addImageComment(\Creativer\FrontBundle\Entity\ImageComments $imageComments)
    {
        $this->image_comments[] = $imageComments;

        return $this;
    }

    /**
     * Remove image_comments
     *
     * @param \Creativer\FrontBundle\Entity\ImageComments $imageComments
     */
    public function removeImageComment(\Creativer\FrontBundle\Entity\ImageComments $imageComments)
    {
        $this->image_comments->removeElement($imageComments);
    }

    /**
     * Get image_comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImageComments()
    {
        return $this->image_comments;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return Images
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set likes
     *
     * @param integer $likes
     * @return Images
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer 
     */
    public function getLikes()
    {
        return $this->likes;
    }
}
