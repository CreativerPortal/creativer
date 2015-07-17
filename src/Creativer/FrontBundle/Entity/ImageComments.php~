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
     * @JMS\Groups({"getAlbumComments"})
     * @JMS\Expose
     */
    private $id;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\User")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="image_comments", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Groups({"idUserByIdImage", "getAlbumById"})
     **/
    private $user;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Images")
     * @ORM\ManyToOne(targetEntity="Images", inversedBy="image_comments")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\Groups({"getAlbumComments"})
     **/
    private $image;


    /**
     * @var datetime $date
     * @JMS\Expose
     * @ORM\Column(name="date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @JMS\Groups({"getAlbumComments"})
     */
    private $date;

    /**
     * @JMS\Expose
     * @ORM\Column(type="text")
     * @JMS\Groups({"getAlbumComments"})
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
     * Set user
     *
     * @param \Creativer\FrontBundle\Entity\User $user
     * @return ImageComments
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
