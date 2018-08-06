<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 05.08.18
 * Time: 15:58
 */

namespace App\EventListener;

use App\Entity\Category;
use App\Utils\FileUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadListener
{
    /**
     * @var FileUploader
     */
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Category) {
            return;
        }
        $this->uploadFile($entity);
    }
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Category) {
            return;
        }
        if ($fileName = $entity->getImage()) {
            $entity->setImage(new File($this->uploader->getTargetDirectory().'/'.$fileName));
        }
    }
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Category) {
            return;
        }
        $this->uploadFile($entity);
    }
    private function uploadFile($entity)
    {
        if(!method_exists($entity,'getImage')) {
            return;
        }
        $file = $entity->getImage();
        if ($file instanceof UploadedFile) {
            $fileName = '';
            if(method_exists($entity,'getTitle')) {
                $fileName = $entity->getTitle();
            }elseif (method_exists($entity,'getName')) {
                $fileName = $entity->getName();
            }
            $fileName = $this->uploader->upload($file,$fileName);
            $entity->setImage($fileName);
        }
    }
}