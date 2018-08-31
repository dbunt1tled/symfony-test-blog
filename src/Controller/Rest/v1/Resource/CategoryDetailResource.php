<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 28.08.18
 * Time: 9:18
 */

namespace App\Controller\Rest\v1\Resource;

use App\Entity\Category;
use App\Utils\Globals;

class CategoryDetailResource
{
    public function listArray($categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            $result[] = $this->toArray($category);
        }
        return $result;
    }
    public function toArray(Category $category): array
    {
        $parent = $category->getParent();
        $image = $category->getImage();
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'description' => $category->getDescription(),
            'status' => $category->getStatus(),
            'image' => $image ? Globals::getCategoryImagesUrl() . '/' . $image : null,
            'parent' => $parent ? [
                'id' => $parent->getId(),
                'name' => $parent->getName(),
                'slug' => $parent->getSlug(),
            ]:null,
            
        ];
    }
    
}/**/