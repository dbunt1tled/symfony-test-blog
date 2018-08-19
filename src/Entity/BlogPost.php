<?php

namespace App\Entity;

use App\Entity\Traits\SlugTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Utils\Globals;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="blog_post")
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BlogPost
{
    use SlugTrait,TimestampableTrait;

    const STATUS_ACTIVE = 'active';
    const STATUS_MODERATE = 'moderate';
    const STATUS_DISABLE = 'disable';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="blogPosts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="blogPosts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="blogPost", cascade={"persist"})
     */
    private $images;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "Максимальный размер файла 5MB.",
     *     mimeTypesMessage = "Загружать можно только изображения."
     * )
     */
    private $uploadedFile;

    public function __construct()
    {
        $this->status = self::STATUS_DISABLE;
        $this->images = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setStatus($status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function getImage()
    {
        return $this->getImages()->first();
    }

    public function setImages($images): self
    {
        if(empty($images)) {
            $this->images = new ArrayCollection();
        }else {
            foreach ($images as $image) {
                $this->addImage($image);
            }
        }
        return $this;
    }

    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }


    public function setUploadedFile($uploadedFile): self
    {
        $this->uploadedFile = $uploadedFile;
        return $this;
    }



    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setBlogPost($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getBlogPost() === $this) {
                $image->setBlogPost(null);
            }
        }

        return $this;
    }
    public static function getStatuses()
    {
        return [
            'Disable' => BlogPost::STATUS_DISABLE,
            'Moderate' => BlogPost::STATUS_MODERATE,
            'Active' => BlogPost::STATUS_ACTIVE,
        ];
    }
    public static function getDefaultStatus()
    {
        return BlogPost::STATUS_DISABLE;
    }
    /**
     * @return string
     */
    public function getTargetDirectory()
    {
        return Globals::getBlogImagesDir();
    }
    /**
     * @return string
     */
    public function getTargetImageUrl()
    {
        return Globals::getBlogImagesUrl();
    }
}
