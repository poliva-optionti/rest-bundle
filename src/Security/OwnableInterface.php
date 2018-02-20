<?php

namespace MNC\RestBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface OwnableInterface
{
    public function getOwner();

    public function setOwner(UserInterface $user);

    public function isVisibleBy(UserInterface $user = null);

    public function isEditableBy(UserInterface $user = null);

    public function isDeletableBy(UserInterface $user = null);
}