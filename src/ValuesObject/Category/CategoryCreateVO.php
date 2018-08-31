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

class CategoryCreateVO
{
    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank(message="Name must been initialized")
     * @Assert\Type(type="string", message="Name - {{ value }}  wrong data type")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $slug;

    /**
     * @Assert\Type(type="string", message="Description - {{ value }}  wrong data type")
     * @Serializer\Type("string")
     */
    public $description;

    /**
     * @Serializer\Type("int")
     * @Assert\NotBlank(message="Name must been initialized")
     * @Assert\Type(type="int", message="Status - {{ value }} wrong data type")
     */
    public $status;
    /*public $image;/**/

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
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}