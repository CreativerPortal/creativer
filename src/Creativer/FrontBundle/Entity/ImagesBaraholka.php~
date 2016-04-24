<?php
// src/AppBundle/Entity/Images.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\MaxDepth;



/**
 * @ORM\Entity
 * @ORM\Table(name="images_baraholka")
 * @JMS\ExclusionPolicy("all")
 */
class ImagesBaraholka
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"idUserByIdImage", "getUser", "getPostsByCategory", "getPostById"})
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getPostsByCategory", "getPostById"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getPostsByCategory", "getPostById"})
     */
    private $path;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\ImagesBaraholka")
     * @ORM\ManyToOne(targetEntity="PostBaraholka", inversedBy="images_baraholka")
     * @ORM\JoinColumn(name="post_baraholka_id", referencedColumnName="id")
     * @JMS\MaxDepth(2)
     **/
    private $post_baraholka;

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
     * Set name
     *
     * @param string $name
     * @return ImagesBaraholka
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
     * @return ImagesBaraholka
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
     * @return ImagesBaraholka
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
     * Set post_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka
     * @return ImagesBaraholka
     */
    public function setPostBaraholka(\Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka = null)
    {
        $this->post_baraholka = $postBaraholka;

        return $this;
    }

    /**
     * Get post_baraholka
     *
     * @return \Creativer\FrontBundle\Entity\PostBaraholka 
     */
    public function getPostBaraholka()
    {
        return $this->post_baraholka;
    }
}
