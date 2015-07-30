<?php

namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity()
 * @ORM\Table(name="categories_baraholka")
 * @JMS\ExclusionPolicy("all")
 */
class CategoriesBaraholka
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"idUserByIdImage", "getUser", "getCategoriesBaraholka", "getPostById"})
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getCategoriesBaraholka"})
     */
    private $name;


    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\CategoriesBaraholka")
     * @ORM\OneToMany(targetEntity="PostBaraholka", mappedBy="categories_baraholka")
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private $post_baraholka;

    /**
     * @ORM\OneToMany(targetEntity="CategoriesBaraholka", mappedBy="parent")
     * @JMS\Expose
     * @JMS\Groups({"getCategoriesBaraholka"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="CategoriesBaraholka", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Groups({"getCategoriesBaraholka"})
     */
    private $parent;

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
    private $isActive = 1;


    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CategoriesBaraholka
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
     * Set date
     *
     * @param \DateTime $date
     * @return CategoriesBaraholka
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
     * @return CategoriesBaraholka
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
     * Add post_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka
     * @return CategoriesBaraholka
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

    /**
     * Add children
     *
     * @param \Creativer\FrontBundle\Entity\CategoriesBaraholka $children
     * @return CategoriesBaraholka
     */
    public function addChild(\Creativer\FrontBundle\Entity\CategoriesBaraholka $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Creativer\FrontBundle\Entity\CategoriesBaraholka $children
     */
    public function removeChild(\Creativer\FrontBundle\Entity\CategoriesBaraholka $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Creativer\FrontBundle\Entity\CategoriesBaraholka $parent
     * @return CategoriesBaraholka
     */
    public function setParent(\Creativer\FrontBundle\Entity\CategoriesBaraholka $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Creativer\FrontBundle\Entity\CategoriesBaraholka 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
