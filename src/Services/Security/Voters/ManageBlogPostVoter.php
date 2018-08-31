<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 30.08.18
 * Time: 9:39
 */

namespace App\Services\Security\Voters;

use App\Entity\BlogPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ManageBlogPostVoter extends Voter
{
    const EDIT = 'editBlogPost';
    const ADD = 'addBlogPost';
    const VIEW = 'viewBlogPost';
    const DELETE = 'deleteBlogPost';
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
        if(!($subject instanceof BlogPost)) {
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
         * @var BlogPost $post
         */
        $post = $subject;
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($post);
            case self::EDIT:
                if(!is_object($user)){
                    return false;
                }
                return $this->canEdit($user, $post);
            case self::ADD:
                if(!is_object($user)){
                    return false;
                }
                return $this->canAdd($user, $post);
            case self::DELETE:
                return $this->canDelete();
        }
        throw new \LogicException('Something went wrong');
    }
    
    private function canView(BlogPost $post)
    {
        if (($post->getStatus() === BlogPost::STATUS_ACTIVE) || ($this->authorizationChecker->isGranted('ROLE_MODERATOR')) ) {
            return true;
        }
        return false;
    }
    private function canEdit(User $user, BlogPost $post)
    {
        if($post->getUser() === $user || ($this->authorizationChecker->isGranted('ROLE_MODERATOR')) ) {
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
    private function canAdd(User $user, BlogPost $post)
    {
        if($post->getUser() === $user || ($this->authorizationChecker->isGranted('ROLE_MODERATOR')) ) {
            return true;
        }
        return false;
    }
}