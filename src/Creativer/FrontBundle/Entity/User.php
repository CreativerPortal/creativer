<?php
// src/AppBundle/Entity/User.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity
 * @ORM\Table(name="app_users")
 * @JMS\ExclusionPolicy("all")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     * @JMS\Expose
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=25)
     * @JMS\Expose
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @JMS\Expose
     */
    private $email;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Albums")
     * @ORM\OneToMany(targetEntity="Albums", mappedBy="user")
     **/
    private $albums;

    /**
     * @ORM\Column(type="text",  nullable=true)
     * @JMS\Expose
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $position;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $info;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $specialization;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $worked;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $links;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $contacts;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Wall")
     * @ORM\OneToOne(targetEntity="Wall", mappedBy="user")
     */
    private $wall;

    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
        $this->roles = new ArrayCollection();
        $this->date = new \DateTime();

    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getImg()
    {
        return $this->img;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }


    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Set img
     *
     * @param string $img
     * @return User
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return User
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return User
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set specialization
     *
     * @param string $specialization
     * @return User
     */
    public function setSpecialization($specialization)
    {
        $this->specialization = $specialization;

        return $this;
    }

    /**
     * Get specialization
     *
     * @return string 
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * Set worked
     *
     * @param string $worked
     * @return User
     */
    public function setWorked($worked)
    {
        $this->worked = $worked;

        return $this;
    }

    /**
     * Get worked
     *
     * @return string 
     */
    public function getWorked()
    {
        return $this->worked;
    }

    /**
     * Set links
     *
     * @param string $links
     * @return User
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Get links
     *
     * @return string 
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set contacts
     *
     * @param string $contacts
     * @return User
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return string 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return User
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
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add albums
     *
     * @param \Creativer\FrontBundle\Entity\Albums $albums
     * @return User
     */
    public function addAlbum(\Creativer\FrontBundle\Entity\Albums $albums)
    {
        $this->albums[] = $albums;

        return $this;
    }

    /**
     * Remove albums
     *
     * @param \Creativer\FrontBundle\Entity\Albums $albums
     */
    public function removeAlbum(\Creativer\FrontBundle\Entity\Albums $albums)
    {
        $this->albums->removeElement($albums);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Add roles
     *
     * @param \Creativer\FrontBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Creativer\FrontBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Creativer\FrontBundle\Entity\Role $roles
     */
    public function removeRole(\Creativer\FrontBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Set wall
     *
     * @param \Creativer\FrontBundle\Entity\Wall $wall
     * @return User
     */
    public function setWall(\Creativer\FrontBundle\Entity\Wall $wall = null)
    {
        $this->wall = $wall;

        return $this;
    }

    /**
     * Get wall
     *
     * @return \Creativer\FrontBundle\Entity\Wall 
     */
    public function getWall()
    {
        return $this->wall;
    }
}
