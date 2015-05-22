<?php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity
 * @ORM\Table(name="image_comments")
 */
class ImageComments
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"getImageComments"})
     */
    private $id;


    /**
     * @JMS\Expose
     * @ORM\ManyToOne(targetEntity="Avatar")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     * @JMS\Groups({"getImageComments"})
     */
    private $avatar;


    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Images")
     * @ORM\ManyToOne(targetEntity="Images", inversedBy="image_comments")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * @JMS\Groups({"getImageComments"})
     **/
    private $image;


    /**
     * @var datetime $date
     * @JMS\Expose
     * @ORM\Column(name="date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"getImageComments"})
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"getImageComments"})
     */
    private $text;


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
     * @return ImageComments
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
     * @return ImageComments
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
     * @return ImageComments
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
     * Set image
     *
     * @param \Creativer\FrontBundle\Entity\Images $image
     * @return ImageComments
     */
    public function setImage(\Creativer\FrontBundle\Entity\Images $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Creativer\FrontBundle\Entity\Images 
     */
    public function getImage()
    {
        return $this->image;
    }
}
