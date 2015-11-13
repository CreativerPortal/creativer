<?php
namespace Creativer\FrontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ImageServices
{

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }


    public function base64_to_jpeg($base64_string) {

        while(true){
            $filename = uniqid('cre', false).'.jpg';
            if(!file_exists(sys_get_temp_dir().$filename)) break;
        }

        $path = $this->container->getParameter('path_img_avatar');

        $year = date("Y");
        $maonth = date("m");
        $day = date("d");
        $name_path = $year."/".$maonth."/".$day."/";
        $path = $path.$name_path;

        if (!file_exists($path))
            mkdir($path, 0777, true);

        $base64_string = substr($base64_string,22);

        $img = imagecreatefromstring(base64_decode($base64_string));

        if($img != false){
            imagejpeg($img, $path.$filename);
        }

        return $path.$filename;
    }
}