<?php
/**
 * Провайдер аутентификации (не Авторизации) - возвращает менеджеру ( Symfony AuthenticationManager ), а тот, в свою
 * очередь listener’y авторизованный (или не авторизванный) token. На данном этапе задействуется UserProvider, который
 * и работает с данными о пользователе.
 */
namespace Creativer\FrontBundle\Handler;

use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Creativer\FrontBundle\Entity\User;
use Creativer\FrontBundle\Entity\Wall;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;



class Provider extends OAuthUserProvider
{

    private $doctrine;

    public function __construct($doctrine,$container)
    {
        $this->doctrine = $doctrine;
        $this->container = $container;
    }

    /**
     * находит и возвращает экземпляр класса User или пустой массив (а по версии Symfony должен возвращать false).
     * @param string $username - ID пользователя
     * @return User|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        if(empty($username)){
            return;
        }
        // получаем данные о пользователе
        /** @var $user \Acme\DemoBundle\Entity\User */
        $user=$this->getUserByWindowsLive($username);

        if($user&&$user->getId()){
            $user->setPassword(sha1($username));
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.',$username));
    }

    /**
     * Предположительно: метод вызывается, когда процесс Аутентификации (не Авторизации) успешно выполнен
     * проверяет сущестование пользователя в Б.Д., заводит сессию и возвращает данные пользователя по $username
     * @param UserResponseInterface $response
     * @return User|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

        $email=$response->getEmail();// емэйл пользователя, например:totx@narod.ru
        $name=$response->getRealName();// имя пользователя на стороне oAuth-сервера, например:Nikolay Lebedenko
        $oAuthID=$response->getUsername();// уникальный ID пользователя на стороне oAuth-сервера, например:8d86a051742940e3
        $first_name=$response->getFirstName();// уникальный ID пользователя на стороне oAuth-сервера, например:8d86a051742940e3
        $last_name=$response->getLastName();// уникальный ID пользователя на стороне oAuth-сервера, например:8d86a051742940e3
        $avatar = $response->getProfilePicture();
        $response->getAccessToken();// токен (уникальный идентификатор) для авторизации, например:ZxC1/2+3 (более 255 символов)
        $response->getExpiresIn();// предположительно: через какое время токен становится недействительным, например:3600 (секунд)
        $response->getProfilePicture();// изображение профиля, может не быть, например:пусто
        $response->getRefreshToken();// например:пусто
        $response->getTokenSecret();// например:пусто

        $color = sprintf( '#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255) );

        $em = $this->doctrine->getManager();
        $user_by_email = $em->getRepository('CreativerFrontBundle:User')->findOneByEmail($email);
        $facebook_id = $em->getRepository('CreativerFrontBundle:User')->findOneBy(array('facebook_id' => $oAuthID, 'email' => $email));

        $user=$this->getUserByWindowsLive($oAuthID);// находим пользователя

        if(!$email){
            die("Для регистрации необходимо вписать свой email в аккаунте социальной сети");
        }

        // если пользователя нет в базе данных - добавим его
        if(!$user && empty($user_by_email) && empty($facebook_id)){
            $user=new User();
            $wall = new Wall();
            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($oAuthID, $user->getSalt());
            $user->setUsername($first_name);
            $user->setLastname($last_name);
            $user->setEmail($email);
            $user->setFacebookId($oAuthID);
            if($avatar){
                $user->setAvatar($avatar);
            }
            $user->setColor($color);

            $user->setPassword($password);
            $user->setRealPassword($oAuthID);
            $user->setAutoscroll(0);
            $user->setWall($wall);
            $wall->setUser($user);
            $role = $em->getRepository('CreativerFrontBundle:Role')->findOneByName('USER');
            $user->addRole($role);

            if ($user) {
                $token = new UsernamePasswordToken(
                    $user,
                    null,
                    'secured_area',
                    $user->getRoles());
                $request = $this->container->get('request');
                $this->container->get('security.context')->setToken($token);
                $this->container->get('session')->set('_security_secured_area',serialize($token));
                $event = new InteractiveLoginEvent($request, $token);
                $this->container->get("event_dispatcher")->dispatch("security.interactive_login", $event);
            }

            try{
                $this->doctrine->getManager()->persist($wall);
                $this->doctrine->getManager()->persist($user);
                $this->doctrine->getManager()->persist($role);
                $this->doctrine->getManager()->flush();
            }catch (Exception $e) {
                $this->container->get('request')->getSession()->invalidate();
                header('Location: http://creativer.by?social_email=true');
                exit;
            }
            $user_id=$user->getId();
        }else if(!empty($user_by_email) && empty($facebook_id)){
            $this->container->get('request')->getSession()->invalidate();
            header('Location: http://creativer.by?social_email=true');
            exit;
        }

        return $this->loadUserByUsername($oAuthID);
    }

    public function refreshUser(UserInterface $user)
    {
        if(!$user instanceof User){
        throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.',get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Метод проверки класса пользователя
     * предположение: нужен чтобы Symfony использовал правильный класс Пользователя для получения объекта пользователя
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class==='Creativer\\FrontBundle\\Entity\\User';
    }

    private function getUserByWindowsLive($facebook_id='')
    {
        return $user=$this->doctrine->getRepository('CreativerFrontBundle:User')->findOneBy(array('facebook_id'=>$facebook_id));
    }
}