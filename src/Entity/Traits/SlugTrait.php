<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 17.08.18
 * Time: 20:41
 */

namespace App\Entity\Traits;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait SlugTrait
 *
 * @package App\Entity\Traits
 * @ORM\HasLifecycleCallbacks
 */
trait SlugTrait
{
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     */
    private $slug;

    /**
     * @return null|string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return SlugTrait
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }
}