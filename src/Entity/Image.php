<?php

namespace App\Entity;

use App\Utils\Globals;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "Максимальный размер файла 5MB.",
     *     mimeTypesMessage = "Загружать можно только изображения."
     * )
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="images")
     * @ORM\JoinColumn(name="blog_post_id", referencedColumnName="id")
     */
    private $blogPost;

    /**
     * @ORM\Column(type="integer")
     */
    private $blog_post_id;
    public function getId()
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Called before saving the entity
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $filename = $this->slug($this->getName().'_'.bin2hex(random_bytes(2)));
            $this->path = $this->imageGroupDir().'/'.$filename . '.' . $this->file->guessExtension();
        }
    }
    /**
     * Called before entity removal
     *
     * @ORM\PreRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // The file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // Use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to

        if(!is_dir($this->getTargetDirectory().'/'.$this->imageGroupDir())) {
            mkdir($this->getTargetDirectory().'/'.$this->imageGroupDir(),0777);
            chmod($this->getTargetDirectory().'/'.$this->imageGroupDir(),0777);
        }
        $this->file->move(
            $this->getTargetDirectory().'/'.$this->imageGroupDir(),
            $this->path
        );

        // Set the path property to the filename where you've saved the file
        //$this->path = $this->file->getClientOriginalName();

        // Clean up the file property as you won't need it anymore
        $this->file = null;
    }
    private function slug($slug) {
        $slug = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove; [:Punctuation:] Remove; Lower();',
            $slug
        );

        if (false == $slug) {
            throw new \RuntimeException('Unable to sluggize: ' . $name);
        }

        $slug = preg_replace('/\W/', ' ', $slug); //remove remaining nonword characters
        $slug = preg_replace('/[-\s]+/', '-', $slug);
        return $slug;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getTargetDirectory().'/'.$this->path;
    }
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }
    protected function getTargetDirectory()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        //return __DIR__.'/../../public/'.$this->getUploadDir();
        return Globals::getBlogImagesDir();
    }
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'images/posts';
    }

    /**
     * @return mixed
     */
    public function getBlogPostId()
    {
        return $this->blog_post_id;
    }

    /**
     * @param mixed $blog_post_id
     */
    public function setBlogPostId($blog_post_id): void
    {
        $this->blog_post_id = $blog_post_id;
    }
    public function imageGroupDir()
    {
        return date('Y-m-d');
    }
}
