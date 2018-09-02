<?php

namespace App\Entity;

use App\Entity\Traits\SlugTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
{
    use SlugTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\BlogPost", mappedBy="tags", cascade={"persist","remove"})
     * @ORM\JoinTable(name="tag_blog_post")
     */
    private $blogPost;

    public function __construct()
    {
        $this->blogPost = new ArrayCollection();
    }

    public function getId(): ?int
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

    /**
     * @return Collection|BlogPost[]
     */
    public function getBlogPost(): Collection
    {
        return $this->blogPost;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPost->contains($blogPost)) {
            $this->blogPost[] = $blogPost;
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPost->contains($blogPost)) {
            $this->blogPost->removeElement($blogPost);
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getName();
    }
}
