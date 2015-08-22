<?php

namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity()
 * @ORM\Table(name="categories_events")
 * @JMS\ExclusionPolicy("all")
 */
class CategoriesEvents
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     */
    private $name;


    /**
     * @ORM\OneToMany(targetEntity="CategoriesEvents", mappedBy="parent")
     * @JMS\Expose
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="CategoriesEvents", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @JMS\Expose
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
     * @return CategoriesEvents
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
     * @return CategoriesEvents
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
     * @return CategoriesEvents
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
     * Add children
     *
     * @param \Creativer\FrontBundle\Entity\CategoriesEvents $children
     * @return CategoriesEvents
     */
    public function addChild(\Creativer\FrontBundle\Entity\CategoriesEvents $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Creativer\FrontBundle\Entity\CategoriesEvents $children
     */
    public function removeChild(\Creativer\FrontBundle\Entity\CategoriesEvents $children)
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
     * @param \Creativer\FrontBundle\Entity\CategoriesEvents $parent
     * @return CategoriesEvents
     */
    public function setParent(\Creativer\FrontBundle\Entity\CategoriesEvents $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Creativer\FrontBundle\Entity\CategoriesEvents 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
