<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 05.08.18
 * Time: 15:58
 */

namespace App\UseCases\Images;

use App\Entity\Category;
use App\Utils\FileUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    /**
     * @var FileUploader
     */
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function uploadFile($entity,$destinationFileName = false)
    {
        if(!method_exists($entity,'getImage') || !method_exists($entity,'setImage')) {
            return;
        }
        $file = $entity->getImage();
        if ($file instanceof UploadedFile) {
            $fileName = $destinationFileName;
            if(empty($fileName)){
                if(method_exists($entity,'getTitle')) {
                    $fileName = $entity->getTitle();
                }elseif (method_exists($entity,'getName')) {
                    $fileName = $entity->getName();
                }
            }
            $fileName = $this->uploader->upload($file,$fileName);
            $entity->setImage($fileName);
        }
    }
}