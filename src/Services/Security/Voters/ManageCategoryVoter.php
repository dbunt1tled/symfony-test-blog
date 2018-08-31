<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 30.08.18
 * Time: 9:39
 */

namespace App\Services\Security\Voters;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ManageCategoryVoter extends Voter
{
    const EDIT = 'editCategory';
    const ADD = 'addCategory';
    const VIEW = 'viewCategory';
    const DELETE = 'deleteCategory';
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }
    
    protected function supports($attribute, $subject)
    {
        if(!in_array($attribute, [self::EDIT,self::VIEW,self::DELETE,self::ADD])) {
            return false;
        }
        if(!($subject instanceof Category)) {
            return false;
        }
        return true;
    }
    
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /**
         * @var User $user
         */
        //$user = $token->getUser();
        /**
         * @var Category $category
         */
        $category = $subject;
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($category);
            case self::EDIT:
                return $this->canEdit();
            case self::ADD:
                return $this->canAdd();
            case self::DELETE:
                return $this->canDelete();
        }
        throw new \LogicException('Something went wrong');
    }
    
    private function canView(Category $category)
    {
        if ($category->getStatus() || ($this->authorizationChecker->isGranted('ROLE_MODERATOR')) ) {
            return true;
        }
        return false;
    }
    private function canEdit()
    {
        if($this->authorizationChecker->isGranted('ROLE_MODERATOR')) {
            return true;
        }
        return false;
    }
    private function canDelete()
    {
        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return false;
    }
    private function canAdd()
    {
        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return false;
    }
}