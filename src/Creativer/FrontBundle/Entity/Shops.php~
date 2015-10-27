<?php
// src/AppBundle/Entity/Albums.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity()
 * @ORM\Table(name="shops")
 * @JMS\ExclusionPolicy("all")
 */
class Shops
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"idUserByIdImage", "getUser", "getAlbumById", "getCatalogProductAlbums", "getCatalogServiceAlbums"})
     * @JMS\Expose
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"idUserByIdImage", "getUser", "getAlbumById", "getCatalogProductAlbums", "getCatalogServiceAlbums"})
     * @JMS\Expose
     */
    private $img;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Groups({"idUserByIdImage", "getUser", "getAlbumById", "getCatalogProductAlbums", "getCatalogServiceAlbums"})
     * @JMS\Expose
     */
    private $path;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"idUserByIdImage", "getUser", "getAlbumById", "getCatalogProductAlbums", "getCatalogServiceAlbums"})
     */
    private $name;


    /**
     * @JMS\Expose
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"idUserByIdImage", "getUser", "getAlbumById", "getCatalogProductAlbums", "getCatalogServiceAlbums"})
     */
    private $description;


    /**
     * @ORM\ManyToMany(targetEntity="Categories", inversedBy="albums")
     * @ORM\JoinTable(name="shops_categories")
     * @JMS\Expose
     * @JMS\Groups({"getAlbumById"})
     */
    private $categories;

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
        $this->categories = new ArrayCollection();
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
     * @return Shops
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
     * Set path
     *
     * @param string $path
     * @return Shops
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
     * Set name
     *
     * @param string $name
     * @return Shops
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
     * @return Shops
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

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Shops
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
     * @return Shops
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
     * Add categories
     *
     * @param \Creativer\FrontBundle\Entity\Categories $categories
     * @return Shops
     */
    public function addCategory(\Creativer\FrontBundle\Entity\Categories $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Creativer\FrontBundle\Entity\Categories $categories
     */
    public function removeCategory(\Creativer\FrontBundle\Entity\Categories $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
