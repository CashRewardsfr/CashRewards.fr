<?php

namespace App\Twig;

use App\Repository\UserRepository;
use App\Repository\AvatarRepository;
use App\Repository\PaiementRepository;
use App\Repository\MissionRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $userRepository;

    private $paiementRepository;

    private $avatarRepository;

    private $missionRepository;

    public function __construct(UserRepository $userRepository, MissionRepository $missionRepository, PaiementRepository $paiementRepository, AvatarRepository $avatarRepository){
        $this->userRepository = $userRepository;
        $this->missionRepository = $missionRepository;
        $this->paiementRepository = $paiementRepository;
        $this->avatarRepository = $avatarRepository;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('userFlexslider', [$this, 'users']),
            new TwigFunction('userAffiliers', [$this, 'getUserAffiliers']),
            new TwigFunction('userMissions', [$this, 'getUserMissions']),
            new TwigFunction('userPaiements', [$this, 'getUserPaiements']),
            new TwigFunction('usersListe', [$this, 'getUsers']),
            new TwigFunction('missionsListe', [$this, 'getMissions']),
            new TwigFunction('paiementsListe', [$this, 'getPaiements']),
            new TwigFunction('userAvatar', [$this, 'getUserAvatar']),
            new TwigFunction('usersConneted', [$this, 'getConnectedUsers']),
            new TwigFunction('getYear', [$this, 'getYear']),
        ];
    }

    public function users(): array
    {
        return $this->userRepository->findBy([
            'isVerified'    =>  1
        ], ['id'    =>  'DESC'], 16);
    }

    public function getUserAffiliers($user): array
    {
        return $this->userRepository->findByParrain($user, 5);
    }

    public function getUserAvatar($user)
    {
        return $this->avatarRepository->findOneBy(['user' => $user]);
    }

    public function getUserMissions($user): array
    {
        return $this->missionRepository->findByUser($user);
    }

    public function getUserPaiements($user): array
    {
        return $this->paiementRepository->findByUser($user);
    }

    public function getMissions(): array
    {
        return $this->missionRepository->findByLimit();
    }

    public function getUsers($limit): array
    {
        return $this->userRepository->findByLimit($limit);
    }

    public function getPaiements($limit): array
    {
        return $this->paiementRepository->findByLimit($limit);
    }

    public function getConnectedUsers(): array
    {
        return $this->userRepository->findBy(['connected' => 1]);
    }

    public function getYear()
    {   
        $year = date('Y');
        return $year;
    }
}
