<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 30.08.18
 * Time: 9:39
 */

namespace App\Services\Security\Voters;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ManageUserVoter extends Voter
{
    const EDIT = 'editUser';
    const ADD = 'addUser';
    const VIEW = 'viewUser';
    const DELETE = 'deleteUser';
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
        if(!($subject instanceof User)) {
            return false;
        }
        return true;
    }
    
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /**
         * @var User $user
         */
        $user = $token->getUser();
        /**
         * @var User $author
         */
        $author = $subject;
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($author);
            case self::EDIT:
                if(!is_object($user)){
                    return false;
                }
                return $this->canEdit($user, $author);
            case self::DELETE:
                return $this->canDelete();
            case self::ADD:
                return $this->canAdd();
        }
        throw new \LogicException('Something went wrong');
    }
    
    private function canView(User $author)
    {
        return true;
    }
    private function canEdit(User $user, User $author)
    {
        if($author === $user || ($this->authorizationChecker->isGranted('ROLE_MODERATOR')) ) {
            return true;
        }
        return false;
    }
    private function canDelete()
    {
        if($this->authorizationChecker->isGranted('ROLE_MODERATOR')) {
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