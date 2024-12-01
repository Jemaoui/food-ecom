<?php
namespace App\Security\Voter;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OrderVoter extends Voter
{
    // Constantes pour les actions
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const CANCEL = 'cancel';
    const PDF = 'pdf'; 

    protected function supports(string $attribute, $subject): bool
    {
        // Vérifie si l'attribut est un des types que nous voulons traiter et si l'objet est une instance d'Order
        return in_array($attribute, [
            self::VIEW, 
            self::EDIT, 
            self::DELETE, 
            self::CANCEL, 
            self::PDF
            ]) && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, $order, TokenInterface $token): bool
    {
        // Récupère l'utilisateur actuellement connecté
        $user = $token->getUser();

        if (!$user instanceof User) {
            // Si l'utilisateur n'est pas authentifié, l'accès est refusé
            return false;
        }

        // Vérifie le rôle de l'utilisateur
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($order, $user);
            case self::EDIT:
                return $this->canEdit($order, $user);
            case self::DELETE:
                return $this->canDelete($order, $user);
            case self::CANCEL:
                return $this->canCancel($order, $user);
            case self::PDF:
                return $this->canGeneratePdf($order, $user); 
        }

        return false;
    }

    private function canView(Order $order, User $user): bool
    {
        // L'admin peut voir toutes les commandes
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
        
        // Le client peut seulement voir ses propres commandes
        return $order->getCustomer()->getId() === $user->getId();
    }

    private function canEdit(Order $order, User $user): bool
    {
        // L'admin peut modifier toutes les commandes
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Le client peut modifier ses propres commandes uniquement si le statut est 'pending'
        return $order->getCustomer()->getId() === $user->getId()
        && $order->getStatus() === 'pending';
    }

    private function canDelete(Order $order, User $user): bool
    {
        // L'admin peut supprimer toutes les commandes
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Le client peut supprimer ses propres commandes uniquement si le statut est 'pending'
        return $order->getCustomer()->getId() === $user->getId()
        && $order->getStatus() === 'pending';
    }

    private function canCancel(Order $order, User $user): bool
    {
        // L'admin peut annuler toutes les commandes
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Le client peut annuler ses propres commandes uniquement si le statut est 'pending'
        return $order->getCustomer()->getId() === $user->getId()
        && $order->getStatus() === 'pending';
    }

    private function canGeneratePdf(Order $order, User $user): bool
    {
        // L'admin peut générer le PDF de toutes les commandes
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Le client peut générer le PDF de ses propres commandes
        return $order->getCustomer()->getId() === $user->getId();
    }

}
