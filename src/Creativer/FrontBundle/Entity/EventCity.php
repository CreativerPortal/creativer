<?php
// src/AppBundle/Entity/Albums.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity()
 * @ORM\Table(name="event_city")
 * @JMS\ExclusionPolicy("all")
 */
class EventCity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"getCity"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getCity"})
     */
    private $name;


    /**
     * @ORM\OneToMany(targetEntity="Events", mappedBy="event_city")
     * @JMS\Groups({"getCity"})
     **/
    private $event;

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
     * @return EventCity
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
     * Add event
     *
     * @param \Creativer\FrontBundle\Entity\Events $event
     * @return EventCity
     */
    public function addEvent(\Creativer\FrontBundle\Entity\Events $event)
    {
        $this->event[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \Creativer\FrontBundle\Entity\Events $event
     */
    public function removeEvent(\Creativer\FrontBundle\Entity\Events $event)
    {
        $this->event->removeElement($event);
    }

    /**
     * Get event
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvent()
    {
        return $this->event;
    }
}
