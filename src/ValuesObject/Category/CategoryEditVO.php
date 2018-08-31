<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 23.08.18
 * Time: 22:45
 */

namespace App\ValuesObject\Category;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryEditVO
{
    /**
     * @Serializer\Type("string")
     *
     */
    public $name;

    /**
     * @Serializer\Type("string")
     *
     */
    public $slug;

    /**
     * @Serializer\Type("string")
     *
     */
    public $description;

    /**
     * @Serializer\Type("int")
     *
     */
    public $status;
    
    /**
     * @Serializer\Type("int")
     *
     */
    public $parent;

    public function __construct(string $name, string $slug, string  $description, int $status)
    {

        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->status = $status;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        $data = json_decode(json_encode($this), true);
        return $data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
    
    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * @param mixed $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
}