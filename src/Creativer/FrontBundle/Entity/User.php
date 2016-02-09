<?php
// src/AppBundle/Entity/User.php
namespace Creativer\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity
 * @ORM\Table(name="app_users")
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity(fields="email", message="Такой email уже существует")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"idUserByIdImage", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getUser", "getPostsByCategory", "getPostById", "getEvent", "getCatalogProductAlbums", "searchProducts", "getCatalogServiceAlbums", "eventAttend"})
     */
    private $id;

    /** @ORM\Column(name="vkontakte_id", type="string", length=255, nullable=true) */
    protected $vkontakte_id;
    /** @ORM\Column(name="vkontakte_access_token", type="string", length=255, nullable=true) */
    protected $vkontakte_access_token;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;
    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /**
     * @ORM\Column(type="string", length=25)
     * @JMS\Expose
     * @Assert\NotBlank(message="Имя пользователя не может быть пустым")
     * @JMS\Groups({"getUser", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getCatalogProductAlbums", "searchProducts", "getCatalogServiceAlbums", "getPostsByCategory", "getPostById", "getEvent"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=25)
     * @JMS\Expose
     * @Assert\NotBlank(message="Фамилия пользователя не может быть пустым")
     * @JMS\Groups({"getUser", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getCatalogProductAlbums", "searchProducts", "getCatalogServiceAlbums", "getPostsByCategory", "getPostById", "getEvent"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     * @JMS\Groups({"getImageComments", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getUser", "getPostsByCategory", "getPostById", "getEvent", "getCatalogProductAlbums", "searchProducts", "getCatalogServiceAlbums"})
     */
    private $avatar=" ";


    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     * @JMS\Groups({"getImageComments", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getUser", "getPostsByCategory", "getPostById", "getEvent", "getCatalogProductAlbums", "searchProducts", "getCatalogServiceAlbums"})
     */
    private $color;


    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Пароль не может быть пустым")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $real_password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @JMS\Expose
     * @Assert\NotBlank(message="Email не может быть пустым")
     * @Assert\Email()
     */
    private $email;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Albums")
     * @ORM\OneToMany(targetEntity="Albums", mappedBy="user", fetch="EAGER")
     * @JMS\Groups({"getUser"})
     **/
    private $albums;


    /**
     * @JMS\Type("Creativer\FrontBundle\Entity\Events")
     * @ORM\OneToMany(targetEntity="Events", mappedBy="user", fetch="EAGER")
     * @JMS\Groups({"getUser","getEvent","eventAttend"})
     **/
    private $events;


    /**
     * @JMS\Type("Creativer\FrontBundle\Entity\Posts")
     * @ORM\OneToMany(targetEntity="Posts", mappedBy="user", fetch="EAGER")
     **/
    private $posts;

    /**
     * @JMS\Type("Creativer\FrontBundle\Entity\Comments")
     * @ORM\OneToMany(targetEntity="Comments", mappedBy="user", fetch="EAGER")
     **/
    private $comments;

    /**
     * @JMS\Type("Creativer\FrontBundle\Entity\PostComments")
     * @ORM\OneToMany(targetEntity="PostComments", mappedBy="user", fetch="EAGER")
     **/
    private $post_comments;

    /**
     * @JMS\Type("Creativer\FrontBundle\Entity\PostBaraholka")
     * @ORM\OneToMany(targetEntity="PostBaraholka", mappedBy="user", fetch="EAGER")
     **/
    private $post_baraholka;


    /**
     * @JMS\Type("Creativer\FrontBundle\Entity\EventComments")
     * @ORM\OneToMany(targetEntity="EventComments", mappedBy="user", fetch="EAGER")
     **/
    private $event_comments;

    /**
     * @JMS\Type("Creativer\FrontBundle\Entity\ImageComments")
     * @ORM\OneToMany(targetEntity="ImageComments", mappedBy="user", fetch="EAGER")
     **/
    private $image_comments;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Folders")
     * @ORM\OneToMany(targetEntity="Folders", mappedBy="user", fetch="EAGER")
     * @JMS\Groups({"getUser"})
     **/
    private $folders;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @JMS\Expose
     * @JMS\Groups({"getImageComments", "getUser"})
     */
    private $likes = 0;

    /**
     * @ORM\Column(type="text",  nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $position;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $info;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $specialization;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $worked;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $links;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $contacts;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" = 0})
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $balance;

    /**
     * @JMS\Expose
     * @ORM\ManyToOne(targetEntity="Tariffs", inversedBy="users", fetch="EAGER")
     * @ORM\JoinColumn(name="tariff_id", referencedColumnName="id")
     * @JMS\Groups({"getUser"})
     * @JMS\MaxDepth(2)
     **/
    private $tariff;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" = 0})
     * @JMS\Expose
     * @JMS\Groups({"getUser"})
     */
    private $views;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     * @JMS\Groups({"getUser"})
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"getUser", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getCatalogProductAlbums",  "getCatalogServiceAlbums", "getPostsByCategory", "getPostById", "getEvent"})
     */
    private $connection_status;

    /**
     * @JMS\Expose
     * @var \DateTime $date
     * @JMS\Groups({"getUser"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"getUser", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getCatalogProductAlbums",  "getCatalogServiceAlbums", "getPostsByCategory", "getPostById", "getEvent"})
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Groups({"getUser", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getCatalogProductAlbums",  "getCatalogServiceAlbums", "getPostsByCategory", "getPostById", "getEvent"})
     */
    private $autoscroll;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Groups({"getUser", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getCatalogProductAlbums",  "getCatalogServiceAlbums", "getPostsByCategory", "getPostById", "getEvent"})
     */
    private $notification_comment=1;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Groups({"getUser", "getAlbumComments", "getCommentBaraholka", "getComments", "getPost", "searchPeople", "getCatalogProductAlbums",  "getCatalogServiceAlbums", "getPostsByCategory", "getPostById", "getEvent"})
     */
    private $notification_message=1;

    /**
     * @JMS\Type("Creativer\FrontBundle\Entity\User")
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFavorits")
     * @JMS\Groups({"getUser"})
     * @JMS\MaxDepth(2)
     * @JMS\Expose
     */
    private $favoritsWithMe;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\User")
     * @ORM\ManyToMany(targetEntity="User", inversedBy="favoritsWithMe")
     * @ORM\JoinTable(name="favorits",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="favorit_user_id", referencedColumnName="id")}
     *      )
     * @JMS\Groups({"getUser"})
     * @JMS\MaxDepth(2)
     */
    private $myFavorits;


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
     * @JMS\Expose
     * @JMS\MaxDepth(1)
     */
    private $roles;

    /**
     * @JMS\Expose
     * @JMS\Type("Creativer\FrontBundle\Entity\Wall")
     * @ORM\OneToOne(targetEntity="Wall", mappedBy="user")
     * @JMS\Groups({"getUser"})
     * @JMS\MaxDepth(8)
     */
    private $wall;

    /**
     * @ORM\ManyToMany(targetEntity="Events", inversedBy="users_attend")
     * @ORM\JoinTable(name="users_attend_events")
     * @JMS\Groups({"getEvent","eventAttend"})
     * @JMS\Expose
     */
    private $events_attend;

    public function __construct()
    {
        $this->avatar = " ";
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
        $this->roles = new ArrayCollection();
        $this->date = new \DateTime();
        $this->connection_status = new \DateTime();
        $this->favoritsWithMe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->myFavorits = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events_attend = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getLastname()
    {
        return $this->lastname;
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
            $this->email,
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
            $this->email,
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
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
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
     * Set real_password
     *
     * @param string $realPassword
     * @return User
     */
    public function setRealPassword($realPassword)
    {
        $this->real_password = $realPassword;

        return $this;
    }

    /**
     * Get real_password
     *
     * @return string 
     */
    public function getRealPassword()
    {
        return $this->real_password;
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
     * Set likes
     *
     * @param integer $likes
     * @return User
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer 
     */
    public function getLikes()
    {
        return $this->likes;
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
     * Set views
     *
     * @param integer $views
     * @return User
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
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
     * Add events
     *
     * @param \Creativer\FrontBundle\Entity\Events $events
     * @return User
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
     * Add posts
     *
     * @param \Creativer\FrontBundle\Entity\Posts $posts
     * @return User
     */
    public function addPost(\Creativer\FrontBundle\Entity\Posts $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \Creativer\FrontBundle\Entity\Posts $posts
     */
    public function removePost(\Creativer\FrontBundle\Entity\Posts $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add comments
     *
     * @param \Creativer\FrontBundle\Entity\Comments $comments
     * @return User
     */
    public function addComment(\Creativer\FrontBundle\Entity\Comments $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Creativer\FrontBundle\Entity\Comments $comments
     */
    public function removeComment(\Creativer\FrontBundle\Entity\Comments $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add post_comments
     *
     * @param \Creativer\FrontBundle\Entity\PostComments $postComments
     * @return User
     */
    public function addPostComment(\Creativer\FrontBundle\Entity\PostComments $postComments)
    {
        $this->post_comments[] = $postComments;

        return $this;
    }

    /**
     * Remove post_comments
     *
     * @param \Creativer\FrontBundle\Entity\PostComments $postComments
     */
    public function removePostComment(\Creativer\FrontBundle\Entity\PostComments $postComments)
    {
        $this->post_comments->removeElement($postComments);
    }

    /**
     * Get post_comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostComments()
    {
        return $this->post_comments;
    }

    /**
     * Add post_baraholka
     *
     * @param \Creativer\FrontBundle\Entity\PostBaraholka $postBaraholka
     * @return User
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
     * Add event_comments
     *
     * @param \Creativer\FrontBundle\Entity\EventComments $eventComments
     * @return User
     */
    public function addEventComment(\Creativer\FrontBundle\Entity\EventComments $eventComments)
    {
        $this->event_comments[] = $eventComments;

        return $this;
    }

    /**
     * Remove event_comments
     *
     * @param \Creativer\FrontBundle\Entity\EventComments $eventComments
     */
    public function removeEventComment(\Creativer\FrontBundle\Entity\EventComments $eventComments)
    {
        $this->event_comments->removeElement($eventComments);
    }

    /**
     * Get event_comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventComments()
    {
        return $this->event_comments;
    }

    /**
     * Add image_comments
     *
     * @param \Creativer\FrontBundle\Entity\ImageComments $imageComments
     * @return User
     */
    public function addImageComment(\Creativer\FrontBundle\Entity\ImageComments $imageComments)
    {
        $this->image_comments[] = $imageComments;

        return $this;
    }

    /**
     * Remove image_comments
     *
     * @param \Creativer\FrontBundle\Entity\ImageComments $imageComments
     */
    public function removeImageComment(\Creativer\FrontBundle\Entity\ImageComments $imageComments)
    {
        $this->image_comments->removeElement($imageComments);
    }

    /**
     * Get image_comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImageComments()
    {
        return $this->image_comments;
    }

    /**
     * Add folders
     *
     * @param \Creativer\FrontBundle\Entity\Folders $folders
     * @return User
     */
    public function addFolder(\Creativer\FrontBundle\Entity\Folders $folders)
    {
        $this->folders[] = $folders;

        return $this;
    }

    /**
     * Remove folders
     *
     * @param \Creativer\FrontBundle\Entity\Folders $folders
     */
    public function removeFolder(\Creativer\FrontBundle\Entity\Folders $folders)
    {
        $this->folders->removeElement($folders);
    }

    /**
     * Get folders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Add favoritsWithMe
     *
     * @param \Creativer\FrontBundle\Entity\User $favoritsWithMe
     * @return User
     */
    public function addFavoritsWithMe(\Creativer\FrontBundle\Entity\User $favoritsWithMe)
    {
        $this->favoritsWithMe[] = $favoritsWithMe;

        return $this;
    }

    /**
     * Remove favoritsWithMe
     *
     * @param \Creativer\FrontBundle\Entity\User $favoritsWithMe
     */
    public function removeFavoritsWithMe(\Creativer\FrontBundle\Entity\User $favoritsWithMe)
    {
        $this->favoritsWithMe->removeElement($favoritsWithMe);
    }

    /**
     * Get favoritsWithMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFavoritsWithMe()
    {
        return $this->favoritsWithMe;
    }

    /**
     * Add myFavorits
     *
     * @param \Creativer\FrontBundle\Entity\User $myFavorits
     * @return User
     */
    public function addMyFavorit(\Creativer\FrontBundle\Entity\User $myFavorits)
    {
        $this->myFavorits[] = $myFavorits;

        return $this;
    }

    /**
     * Remove myFavorits
     *
     * @param \Creativer\FrontBundle\Entity\User $myFavorits
     */
    public function removeMyFavorit(\Creativer\FrontBundle\Entity\User $myFavorits)
    {
        $this->myFavorits->removeElement($myFavorits);
    }

    /**
     * Get myFavorits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMyFavorits()
    {
        return $this->myFavorits;
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

    /**
     * Add events_attend
     *
     * @param \Creativer\FrontBundle\Entity\Events $eventsAttend
     * @return User
     */
    public function addEventsAttend(\Creativer\FrontBundle\Entity\Events $eventsAttend)
    {
        $this->events_attend[] = $eventsAttend;

        return $this;
    }

    /**
     * Remove events_attend
     *
     * @param \Creativer\FrontBundle\Entity\Events $eventsAttend
     */
    public function removeEventsAttend(\Creativer\FrontBundle\Entity\Events $eventsAttend)
    {
        $this->events_attend->removeElement($eventsAttend);
    }

    /**
     * Get events_attend
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventsAttend()
    {
        return $this->events_attend;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return User
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set autoscroll
     *
     * @param integer $autoscroll
     * @return User
     */
    public function setAutoscroll($autoscroll)
    {
        $this->autoscroll = $autoscroll;

        return $this;
    }

    /**
     * Get autoscroll
     *
     * @return integer 
     */
    public function getAutoscroll()
    {
        return $this->autoscroll;
    }

    /**
     * Set notification_comment
     *
     * @param boolean $notificationComment
     * @return User
     */
    public function setNotificationComment($notificationComment)
    {
        $this->notification_comment = $notificationComment;

        return $this;
    }

    /**
     * Get notification_comment
     *
     * @return boolean 
     */
    public function getNotificationComment()
    {
        return $this->notification_comment;
    }

    /**
     * Set notification_message
     *
     * @param boolean $notificationMessage
     * @return User
     */
    public function setNotificationMessage($notificationMessage)
    {
        $this->notification_message = $notificationMessage;

        return $this;
    }

    /**
     * Get notification_message
     *
     * @return boolean 
     */
    public function getNotificationMessage()
    {
        return $this->notification_message;
    }


    /**
     * Set connection_status
     *
     * @param string $connectionStatus
     * @return User
     */
    public function setConnectionStatus($connectionStatus)
    {
        $this->connection_status = $connectionStatus;

        return $this;
    }

    /**
     * Get connection_status
     *
     * @return string 
     */
    public function getConnectionStatus()
    {
        return $this->connection_status;
    }

    /**
     * Set balance
     *
     * @param integer $balance
     * @return User
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return integer 
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set tariff
     *
     * @param \Creativer\FrontBundle\Entity\Tariffs $tariff
     * @return User
     */
    public function setTariff(\Creativer\FrontBundle\Entity\Tariffs $tariff = null)
    {
        $this->tariff = $tariff;

        return $this;
    }

    /**
     * Get tariff
     *
     * @return \Creativer\FrontBundle\Entity\Tariffs 
     */
    public function getTariff()
    {
        return $this->tariff;
    }

    /**
     * Set vkontakte_id
     *
     * @param string $vkontakteId
     * @return User
     */
    public function setVkontakteId($vkontakteId)
    {
        $this->vkontakte_id = $vkontakteId;

        return $this;
    }

    /**
     * Get vkontakte_id
     *
     * @return string 
     */
    public function getVkontakteId()
    {
        return $this->vkontakte_id;
    }

    /**
     * Set vkontakte_access_token
     *
     * @param string $vkontakteAccessToken
     * @return User
     */
    public function setVkontakteAccessToken($vkontakteAccessToken)
    {
        $this->vkontakte_access_token = $vkontakteAccessToken;

        return $this;
    }

    /**
     * Get vkontakte_access_token
     *
     * @return string 
     */
    public function getVkontakteAccessToken()
    {
        return $this->vkontakte_access_token;
    }

    /**
     * Set facebook_id
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }
}
