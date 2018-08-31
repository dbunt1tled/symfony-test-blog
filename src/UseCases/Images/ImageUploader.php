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
                if (method_exists($entity,'getName')) {
                    $fileName = $entity->getName();
                }
            }
            $targetDirectory = '';
            if (method_exists($entity,'getTargetDirectory')) {
                $targetDirectory = $entity->getTargetDirectory();
            }
            $fileName = $this->uploader->upload($file,$fileName,$targetDirectory);
            $entity->setImage($fileName);
        }
    }
    
    /**
     * @param string $originalFilePath
     * @param string $fileName
     *
     * @return UploadedFile
     * @throws \Exception
     */
    public function getPutFile(string $originalFilePath,string $fileName = 'image')
    {
        if(!($content = file_get_contents("php://input"))) {
            throw new \Exception('Image not found in request');
        }
        file_put_contents($originalFilePath, $content);
    
        $info = pathinfo($fileName);
        $mime = mime_content_type($originalFilePath);
        $filename = $fileName;
        $extension = explode('/', $mime )[1];
        if(!isset($info["extension"]) || (mb_strtolower($info["extension"]) !== $extension)) {
            $filename .= '.'.$extension;
        }
        return new UploadedFile($originalFilePath, $filename, $mime, null, null, true);
    }
}