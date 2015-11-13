<?php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\MaxDepth;



/**
 * @ORM\Entity
 * @ORM\Table(name="tariffs")
 * @JMS\ExclusionPolicy("all")
 */
class Tariffs
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"getUser"})
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $text;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $cost_month;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $cost_day;

    /**
     * @JMS\Expose
     * @ORM\OneToMany(targetEntity="User", mappedBy="tariff")
     * @JMS\Groups({"getUser"})
     **/
    private $users;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"getUser"})
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
     * @return Tariffs
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
     * Set image
     *
     * @param string $image
     * @return Tariffs
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set cost_month
     *
     * @param integer $costMonth
     * @return Tariffs
     */
    public function setCostMonth($costMonth)
    {
        $this->cost_month = $costMonth;

        return $this;
    }

    /**
     * Get cost_month
     *
     * @return integer 
     */
    public function getCostMonth()
    {
        return $this->cost_month;
    }

    /**
     * Set cost_day
     *
     * @param integer $costDay
     * @return Tariffs
     */
    public function setCostDay($costDay)
    {
        $this->cost_day = $costDay;

        return $this;
    }

    /**
     * Get cost_day
     *
     * @return integer 
     */
    public function getCostDay()
    {
        return $this->cost_day;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Tariffs
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
     * Add users
     *
     * @param \Creativer\FrontBundle\Entity\User $users
     * @return Tariffs
     */
    public function addUser(\Creativer\FrontBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Creativer\FrontBundle\Entity\User $users
     */
    public function removeUser(\Creativer\FrontBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Tariffs
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
}
