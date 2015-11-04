<?php
// src/AppBundle/Entity/Images.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\MaxDepth;



/**
 * @ORM\Entity()
 * @ORM\Table(name="images_shops")
 * @JMS\ExclusionPolicy("all")
 */
class ImagesShops
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"getShopById", "getShopsByCategory", "getCatalogProductAlbums", "getCatalogServiceAlbums"})
     * @JMS\Expose
     */
    private $id;


    /**
     * @JMS\Expose
     * @ORM\ManyToOne(targetEntity="Shops", inversedBy="images")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     * @JMS\Groups({"getShopById", "getShopsByCategory"})
     **/
    private $shop;


    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getShopById", "getShopsByCategory", "getCatalogProductAlbums", "getCatalogServiceAlbums"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getShopById", "getShopsByCategory", "getCatalogProductAlbums", "getCatalogServiceAlbums"})
     */
    private $path;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"getShopById", "getShopsByCategory"})
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
     * @return ImagesShops
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
     * @return ImagesShops
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
     * Set date
     *
     * @param \DateTime $date
     * @return ImagesShops
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
     * Set shop
     *
     * @param \Creativer\FrontBundle\Entity\Shops $shop
     * @return ImagesShops
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
