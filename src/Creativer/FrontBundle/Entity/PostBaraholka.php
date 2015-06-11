<?php
// src/AppBundle/Entity/Albums.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity()
 * @ORM\Table(name="post_baraholka")
 * @JMS\ExclusionPolicy("all")
 */
class PostBaraholka
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;


    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\User")
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
     * @JMS\Expose
     */
    private $name;


    /**
     * @JMS\Expose
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


    /**
     * @JMS\Expose
     * @ORM\Column(type="text", nullable=true)
     */
    private $full_description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose
     */
    private $price = 0;


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
     * @JMS\Type("Creativer\FrontBundle\Entity\PostBaraholka")
     * @ORM\ManyToOne(targetEntity="CategoriesBaraholka", inversedBy="post_baraholka")
     * @ORM\JoinColumn(name="categories_baraholka_id", referencedColumnName="id")
     **/
    private $categories_baraholka;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\PostBaraholka")
     * @ORM\OneToMany(targetEntity="ImagesBaraholka", mappedBy="post_baraholka")
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private $images_baraholka;


    /**
     * @ORM\ManyToOne(targetEntity="PostCategory", inversedBy="post_baraholka")
     * @ORM\JoinColumn(name="post_category_id", referencedColumnName="id")
     **/
    private $post_category;


    /**
     * @ORM\ManyToOne(targetEntity="PostCity", inversedBy="post_baraholka")
     * @ORM\JoinColumn(name="post_city_id", referencedColumnName="id")
     **/
    private $post_city;


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
     * @return PostBaraholka
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
     * Set name
     *
     * @param string $name
     * @return PostBaraholka
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
     * @return PostBaraholka
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
     * Set full_description
     *
     * @param string $fullDescription
     * @return PostBaraholka
     */
    public function setFullDescription($fullDescription)
    {
        $this->full_description = $fullDescription;

        return $this;
    }

    /**
     * Get full_description
     *
     * @return string 
     */
    public function getFullDescription()
    {
        return $this->full_description;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return PostBaraholka
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
     * @return PostBaraholka
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
     * Set user
     *
     * @param \Creativer\FrontBundle\Entity\User $user
     * @return PostBaraholka
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
     * Set categories_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\CategoriesBaraholka $categoriesBaraholka
     * @return PostBaraholka
     */
    public function setCategoriesBaraholka(\Creativer\FrontBundle\Entity\CategoriesBaraholka $categoriesBaraholka = null)
    {
        $this->categories_baraholka = $categoriesBaraholka;

        return $this;
    }

    /**
     * Get categories_baraholka
     *
     * @return \Creativer\FrontBundle\Entity\CategoriesBaraholka 
     */
    public function getCategoriesBaraholka()
    {
        return $this->categories_baraholka;
    }

    /**
     * Add images_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\ImagesBaraholka $imagesBaraholka
     * @return PostBaraholka
     */
    public function addImagesBaraholka(\Creativer\FrontBundle\Entity\ImagesBaraholka $imagesBaraholka)
    {
        $this->images_baraholka[] = $imagesBaraholka;

        return $this;
    }

    /**
     * Remove images_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\ImagesBaraholka $imagesBaraholka
     */
    public function removeImagesBaraholka(\Creativer\FrontBundle\Entity\ImagesBaraholka $imagesBaraholka)
    {
        $this->images_baraholka->removeElement($imagesBaraholka);
    }

    /**
     * Get images_baraholka
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImagesBaraholka()
    {
        return $this->images_baraholka;
    }

    /**
     * Set post_category
     *
     * @param \Creativer\FrontBundle\Entity\PostCategory $postCategory
     * @return PostBaraholka
     */
    public function setPostCategory(\Creativer\FrontBundle\Entity\PostCategory $postCategory = null)
    {
        $this->post_category = $postCategory;

        return $this;
    }

    /**
     * Get post_category
     *
     * @return \Creativer\FrontBundle\Entity\PostCategory 
     */
    public function getPostCategory()
    {
        return $this->post_category;
    }

    /**
     * Set post_city
     *
     * @param \Creativer\FrontBundle\Entity\PostCity $postCity
     * @return PostBaraholka
     */
    public function setPostCity(\Creativer\FrontBundle\Entity\PostCity $postCity = null)
    {
        $this->post_city = $postCity;

        return $this;
    }

    /**
     * Get post_city
     *
     * @return \Creativer\FrontBundle\Entity\PostCity 
     */
    public function getPostCity()
    {
        return $this->post_city;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return PostBaraholka
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
}
