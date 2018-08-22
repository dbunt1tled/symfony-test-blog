<?php

namespace App\Entity;

use App\Entity\Traits\ImageTrait;
use App\Entity\Traits\SlugTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Utils\Globals;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 * 
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable
{
    use SlugTrait, TimestampableTrait, ImageTrait;

    const ROLE_USER = 'ROLE_USER';
    const ROLE_MODERATOR = 'ROLE_MODERATOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';

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
     * @ORM\Column(type="string", length=255)
     */
    private $job;

    /**
     * @ORM\Column(type="string", length=70, unique=true)
     *
     * @Assert\NotBlank(
     *     message="Email не может быть пустым"
     * )
     * @Assert\Email(
     *     message="Email имеет не верный формат"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $shortBio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $github;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Не может быть пустым"
     * )
     * @Assert\Length(
     *     min=6,
     *     max=4096,
     *     minMessage="Минимум шесть символов"
     * )
     */
    protected $plainPassword;

    /**
     * @var array
     * @ORM\Column(name="role", type="array")
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="user")
     */
    private $blogPosts;

    public function __construct()
    {
        $this->role = [self::ROLE_USER];
        $this->blogPosts = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->email;
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
    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getShortBio(): ?string
    {
        return $this->shortBio;
    }

    public function setShortBio(string $shortBio): self
    {
        $this->shortBio = $shortBio;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): self
    {
        $this->github = $github;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): self
    {
        $this->plainPassword = $password;
        return $this;
    }

    public function getSalt(): ?string
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function setRole(array $role): self
    {
        $this->role = $role;
        return $this;
    }
    /**
     * @return array
     */
    public function getRole()
    {
        $role = $this->role;
        if (empty($role)) {
            $role[] = self::ROLE_USER;
        }
        return array_unique($role);
    }
    /**
     * @return array
     */
    public function getRoles()
    {
        $role = $this->role;
        if (empty($role)) {
            $role[] = self::ROLE_USER;
        }
        return array_unique($role);
    }

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
        ]);
    }
    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->email,
            $this->password) = unserialize($serialized);
    }
    public function eraseCredentials()
    {
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
            $blogPost->setUser($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->contains($blogPost)) {
            $this->blogPosts->removeElement($blogPost);
            // set the owning side to null (unless already changed)
            if ($blogPost->getUser() === $this) {
                $blogPost->setUser(null);
            }
        }

        return $this;
    }
    /**
     * @return string
     */
    public function getTargetDirectory()
    {
        return Globals::getUserImagesDir();
    }

    /**
     * @return string
     */
    public function getTargetImageUrl()
    {
        return Globals::getUserImagesUrl();
    }
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
