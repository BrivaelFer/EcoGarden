<?php

namespace App\DataFixtures;

use App\Entity\Conseil;
use App\Entity\Mois;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
          // Création d'un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setVille('Paris');
        $manager->persist($admin);

        // Création d'un utilisateur classique
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $user->setVille('Morlaix');
        $manager->persist($user);

        $mois = [
            [
                'id' => 1,
                'nom' => 'Janvier'
            ],
            [
                'id' => 2,
                'nom' => 'Février'
            ],
            [
                'id' => 3,
                'nom' => 'Mars'
            ],
            [
                'id' => 4,
                'nom' => 'Avril'
            ],
            [
                'id' => 5,
                'nom' => 'Mai'
            ],
            [
                'id' => 6,
                'nom' => 'Juin'
            ],
            [
                'id' => 7,
                'nom' => 'Juillet'
            ],
            [
                'id' => 8,
                'nom' => 'Août'
            ],
            [
                'id' => 9,
                'nom' => 'Septembre'
            ],
            [
                'id' => 10,
                'nom' => 'Octobre'
            ],
            [
                'id' => 11,
                'nom' => 'Novembre'
            ],
            [
                'id' => 12,
                'nom' => 'Décembre'
            ]
        ];

        $listMois = [];
        foreach($mois as $m) {
            $om = new Mois();
            $om->setId($m['id']);
            $om->setNom($m['nom']);
            $manager->persist($om);
            $listMois[] = $om;
        }


        $conseilsData = [
            [
                'title' => 'Conseils pour le potager en janvier',
                'content' => 'En janvier, protégez vos plantes du gel et préparez votre sol pour les semis à venir. C\'est aussi le moment de planifier vos cultures pour l\'année.',
                'listMois' => [$listMois[0]],
            ],
            [
                'title' => 'Entretien du jardin en printemps',
                'content' => 'Au printemps, désherbez régulièrement, semez les légumes de saison et arrosez modérément. Pensez à pailler pour conserver l\'humidité du sol.',
                'listMois' => [$listMois[2], $listMois[3], $listMois[4]],
            ],
            [
                'title' => 'Préparer l\'automne au jardin',
                'content' => 'En automne, récoltez les derniers légumes, plantez les bulbes pour le printemps suivant et protégez les plantes fragiles avant l\'hiver.',
                'listMois' => [$listMois[8], $listMois[9], $listMois[10]],
            ],
            [
                'title' => 'Jardinage en été : astuces pour économiser l\'eau',
                'content' => 'En été, arrosez tôt le matin ou tard le soir pour limiter l\'évaporation. Utilisez un système de goutte-à-goutte et paillez généreusement.',
                'listMois' => [$listMois[5], $listMois[6], $listMois[7]],
            ],
            [
                'title' => 'Hivernage des plantes en pot',
                'content' => 'Rentrez les plantes en pot avant les premières gelées. Réduisez les arrosages et surveillez l\'apparition de maladies liées à l\'humidité.',
                'listMois' => [$listMois[11], $listMois[0], $listMois[1]],
            ],
        ];

        foreach ($conseilsData as $data) {
            $conseil = new Conseil();
            $conseil->setTitle($data['title']);
            $conseil->setContent($data['content']);
            foreach($data['listMois'] as $m) {
                $conseil->addMoisList($m);
            }

            $manager->persist($conseil);
        }

        // Sauvegarde en base de données
        $manager->flush();
    }
}
