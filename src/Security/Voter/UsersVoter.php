<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UsersVoter extends Voter
{
    const USER_EDIT = "user_edit";
    const USER_DELETE = "user_delete";
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $user): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::USER_EDIT, self::USER_DELETE])
            && $user instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $propertie, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        //On autorise à un administrateur de modifier un projet
        //if ($this->security->isGranted("ROLE_ADMIN")) return true;

        //Verifie si un projet a un auteur
        if (null === $propertie->getUserIdentifier()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::USER_EDIT:
                // logic to determine if the user can EDIT
                return $this->canEdit($propertie, $user);
                break;
            case self::USER_DELETE:
                // logic to determine if the user can VIEW
                return $this->canDelete($propertie, $user);
                break;
        }

        return false;
    }

    private function canEdit(User $propertie, User $user){
        return $user === $propertie->getParrain();
    }

    private function canDelete(User $propertie, User $user){
        if($this->security->isGranted("ROLE_ADMIN")) return true;
        return $user === $propertie->getUser();
    }
}
