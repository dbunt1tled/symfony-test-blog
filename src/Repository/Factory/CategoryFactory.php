<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 24.08.18
 * Time: 9:02
 */

namespace App\Repository\Factory;

use App\Entity\Category;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManager;

class CategoryFactory
{
    
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    
    public function __construct(EntityManager $em)
    {
        $this->categoryRepository = $em->getRepository('App:Category');
    }
    
    /**
     * @param array $data
     *
     * @return Category
     */
    public function makeFromArray(array $data): Category
    {
        $category = new Category();
        return $this->updateFromArray($category,$data);
    }
    
    /**
     * @param Category $category
     * @param array    $data
     *
     * @return Category
     */
    public function updateFromArray(Category $category,array $data): Category
    {
        foreach ($data as $key => $value) {
            $key = 'set'.ucfirst(Inflector::camelize($key));
            if ($key === "setParent" && method_exists($category, $key)) {
                if(is_numeric($value) && $value > 0) {
                    $parent = $this->categoryRepository->find((int)$value);
                    if(!empty($parent)){
                        $category->setParent($parent);
                    }
                }elseif ($value instanceof Category){
                    $category->setParent($value);
                }
            }elseif (method_exists($category, $key)) {
                $category->{$key}($value);
            }
        }
        return $category;
    }
}