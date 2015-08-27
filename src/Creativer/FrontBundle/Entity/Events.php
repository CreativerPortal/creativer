<?php
// src/AppBundle/Entity/Albums.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 * @JMS\ExclusionPolicy("all")
 */
class Events
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events", fetch="EAGER")
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
     * @JMS\Groups({"getUser", "getAlbumById", "getCatalogProductAlbums"})
     */
    private $name;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Events")
     * @ORM\ManyToOne(targetEntity="EventSections", inversedBy="events")
     * @ORM\JoinColumn(name="event_sections_id", referencedColumnName="id")
     * @JMS\Groups({"getPostById"})
     **/
    private $event_sections;

    /**
     * @JMS\Expose
     * @ORM\ManyToOne(targetEntity="EventCity", inversedBy="event")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @JMS\Groups({"getCity"})
     **/
    private $event_city;


    /**
     * @JMS\Expose
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"getAlbumById"})
     */
    private $description;

    /**
     * @JMS\Expose
     * @var \DateTime $start_date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $start_date;

    /**
     * @JMS\Expose
     * @var \DateTime $end_date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $end_date;

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
        $this->start_date = new \DateTime();
        $this->end_date = new \DateTime();
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
     * @return Events
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
     * @return Events
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
     * @return Events
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
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return Events
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return Events
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Events
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
     * @return Events
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
     * @return Events
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
     * Set event_sections
     *
     * @param \Creativer\FrontBundle\Entity\EventSections $eventSections
     * @return Events
     */
    public function setEventSections(\Creativer\FrontBundle\Entity\EventSections $eventSections = null)
    {
        $this->event_sections = $eventSections;

        return $this;
    }

    /**
     * Get event_sections
     *
     * @return \Creativer\FrontBundle\Entity\EventSections 
     */
    public function getEventSections()
    {
        return $this->event_sections;
    }

    /**
     * Set event_city
     *
     * @param \Creativer\FrontBundle\Entity\EventCity $eventCity
     * @return Events
     */
    public function setEventCity(\Creativer\FrontBundle\Entity\EventCity $eventCity = null)
    {
        $this->event_city = $eventCity;

        return $this;
    }

    /**
     * Get event_city
     *
     * @return \Creativer\FrontBundle\Entity\EventCity 
     */
    public function getEventCity()
    {
        return $this->event_city;
    }
}
