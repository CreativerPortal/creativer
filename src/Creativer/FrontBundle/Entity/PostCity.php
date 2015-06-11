<?php
// src/AppBundle/Entity/Albums.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity()
 * @ORM\Table(name="post_city")
 * @JMS\ExclusionPolicy("all")
 */
class PostCity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $name;


    /**
     * @ORM\OneToMany(targetEntity="PostBaraholka", mappedBy="post_city")
     **/
    private $post_baraholka;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->post_baraholka = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return PostCity
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
     * Add post_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka
     * @return PostCity
     */
    public function addPostBaraholka(\Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka)
    {
        $this->post_baraholka[] = $postBaraholka;

        return $this;
    }

    /**
     * Remove post_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka
     */
    public function removePostBaraholka(\Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka)
    {
        $this->post_baraholka->removeElement($postBaraholka);
    }

    /**
     * Get post_baraholka
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostBaraholka()
    {
        return $this->post_baraholka;
    }
}
