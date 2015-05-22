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
 */
class Images
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"idUserByIdImage"})
     */
    private $id;


    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Albums")
     * @ORM\ManyToOne(targetEntity="Albums", inversedBy="images")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     * @MaxDepth(2)
     * @JMS\Groups({"idUserByIdImage"})
     **/
    private $album;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\ImageComments")
     * @ORM\OneToMany(targetEntity="ImageComments", mappedBy="image")
     * @JMS\Groups({"getImageComments"})
     **/
    private $image_comments;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getImageComments"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"getImageComments"})
     */
    private $text;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"getImageComments"})
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
}
