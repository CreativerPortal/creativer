<?php

namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity()
 * @ORM\Table(name="event_sections")
 * @JMS\ExclusionPolicy("all")
 */
class EventSections
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"getEventSections"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getEventSections"})
     */
    private $name;


    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\EventSections")
     * @ORM\OneToMany(targetEntity="Events", mappedBy="event_sections")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Groups({"getEventSections"})
     **/
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="EventSections", mappedBy="parent")
     * @JMS\Expose
     * @JMS\Groups({"getEventSections"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="EventSections", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Groups({"getEventSections"})
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
     * @return EventSections
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
     * @return EventSections
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
     * @return EventSections
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
     * Add events
     *
     * @param \Creativer\FrontBundle\Entity\Events $events
     * @return EventSections
     */
    public function addEvent(\Creativer\FrontBundle\Entity\Events $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Creativer\FrontBundle\Entity\Events $events
     */
    public function removeEvent(\Creativer\FrontBundle\Entity\Events $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add children
     *
     * @param \Creativer\FrontBundle\Entity\EventSections $children
     * @return EventSections
     */
    public function addChild(\Creativer\FrontBundle\Entity\EventSections $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Creativer\FrontBundle\Entity\EventSections $children
     */
    public function removeChild(\Creativer\FrontBundle\Entity\EventSections $children)
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
     * @param \Creativer\FrontBundle\Entity\EventSections $parent
     * @return EventSections
     */
    public function setParent(\Creativer\FrontBundle\Entity\EventSections $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Creativer\FrontBundle\Entity\EventSections 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
