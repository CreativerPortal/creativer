<?php
// src/AppBundle/Entity/Albums.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity
 * @ORM\Table(name="albums")
 */
class Albums
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @JMS\Expose
     * @JMS\Type("Albums")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="albums", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $img;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $name;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer", name="is_active")
     */
    private $isActive = 0;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Images")
     * @ORM\OneToMany(targetEntity="Images", mappedBy="album")
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private $images;



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
     * Set img
     *
     * @param string $img
     * @return Albums
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
     * @return Albums
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
     * Add images
     *
     * @param \Creativer\FrontBundle\Entity\Images $images
     * @return Albums
     */
    public function addImage(\Creativer\FrontBundle\Entity\Images $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Creativer\FrontBundle\Entity\Images $images
     */
    public function removeImage(\Creativer\FrontBundle\Entity\Images $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set user
     *
     * @param \Creativer\FrontBundle\Entity\User $user
     * @return Albums
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
     * Set isActive
     *
     * @param integer $isActive
     * @return Albums
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return integer 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Albums
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
     * Set description
     *
     * @param string $description
     * @return Albums
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
