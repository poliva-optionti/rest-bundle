<?php

namespace MNC\RestBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This voter evaluates if a resource implementing OwnableInterface can be seen,
 * updated or deleted by a determined user.
 * @package Security
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class OwnableResourceVoter extends Voter
{
    const VIEW = 'view';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * @param string $attribute
     * @param mixed  $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // We only support objects that implement OwnableInterface
        if (!$subject instanceof OwnableInterface) {
            return false;
        }

        // And we only support these attributes
        if (!in_array($attribute, [self::VIEW, self::UPDATE, self::DELETE])) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var OwnableInterface $entity */
        $entity = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $entity->isVisibleBy($user);
            case self::UPDATE:
                return $entity->isEditableBy($user);
            case self::DELETE:
                return $entity->isDeletableBy($user);
        }

        throw new \LogicException('This code should not be reached!');
    }
}