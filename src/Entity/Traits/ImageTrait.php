<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 19.08.18
 * Time: 14:13
 */

namespace App\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ImageTrait
 *
 * @package App\Entity\Traits
 * @ORM\HasLifecycleCallbacks()
 */
trait ImageTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default":""})
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/jpg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "Max file size 5MB.",
     *     mimeTypesMessage = "Allow only images."
     * )
     *
     */
    private $image;
    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param $image
     *
     * @return self
     */
    public function setImage($image): self
    {
        if(is_null($image)) {
            return $this;
        }
        if($image === '') {
            $image = null;
        }
        $this->image = $image;
        return $this;
    }
    public function deleteImage(): self
    {
        /**
         * @var File $file
         */
        $file = $this->getImage();
        if(!empty($file)) {
            unlink($this->getTargetDirectory().'/'.$file);
        }
        $this->setImage('');
        return $this;
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        /*if ($fileName = $this->getImage()) {
            $this->setImage(new File($this->getTargetDirectory().'/'.$fileName));
        }/**/
    }

    /**
     * @ORM\PreFlush()
     * @ORM\PreUpdate()
     */
    public function preSave()
    {
        $fileName = $this->getImage();
        if ($fileName instanceof File) {
            $this->setImage($fileName->getFilename());
        }
    }
    /**
     * @return string
     */
    public function getTargetDirectory()
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public function getTargetImageUrl()
    {
        return '/';
    }
}