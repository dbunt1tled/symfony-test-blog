<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 17.08.18
 * Time: 9:02
 */

namespace App\Utils;

use Doctrine\ORM\QueryBuilder;

trait RepositoryTrait
{
    public function addOrderByArray(QueryBuilder $qb, array $orderBy = [], $alias='')
    {
        if(empty($orderBy)) {
            return $qb;
        }
        foreach ($orderBy as $field => $order) {
            $qb->addOrderBy($alias.'.'.$field, $order);
        }
        return  $qb;
    }
}