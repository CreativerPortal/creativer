<?php
// src/AppBundle/Entity/Albums.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity()
 * @ORM\Table(name="address")
 * @JMS\ExclusionPolicy("all")
 */
class Address
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"idUserByIdImage", "getUser", "getAlbumById", "getCatalogProductAlbums", "getCatalogServiceAlbums", "getShopById"})
     * @JMS\Expose
     */
    private $id;


    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"getShopById"})
     * @JMS\Expose
     */
    private $address;


    /**
     * @JMS\Expose
     * @ORM\ManyToOne(targetEntity="Shops", inversedBy="address")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     * @JMS\Groups({"getShopById"})
     **/
    private $shop;


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
     * Set address
     *
     * @param string $address
     * @return Address
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Address
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
     * Set isActive
     *
     * @param integer $isActive
     * @return Address
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
     * Set shop
     *
     * @param \Creativer\FrontBundle\Entity\Shops $shop
     * @return Address
     */
    public function setShop(\Creativer\FrontBundle\Entity\Shops $shop = null)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop
     *
     * @return \Creativer\FrontBundle\Entity\Shops 
     */
    public function getShop()
    {
        return $this->shop;
    }
}
