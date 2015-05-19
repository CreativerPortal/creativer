<?php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity
 * @ORM\Table(name="avatar")
 */
class Avatar
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $img;

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
     * Set img
     *
     * @param string $img
     * @return Avatar
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string 
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Avatar
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
