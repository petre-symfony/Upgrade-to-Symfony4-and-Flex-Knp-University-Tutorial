<?php

namespace App\DataFixtures;

use App\Entity\Genus;
use App\Entity\GenusNote;
use App\Entity\GenusScientist;
use App\Entity\SubFamily;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Faker\Factory;
use Faker\Generator;

class AllFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $this->addSubFamily($manager);
        $this->addGenus($manager);
        $this->addGenusNote($manager);
        $this->addUser($manager);
        $this->addGenusScientist($manager);

        $manager->flush();
    }

    private function addGenus(EntityManager $em)
    {
        for ($i = 1; $i <= 10; $i++) {
            $genus = new Genus();
            $genus->setName($this->genus());
            $genus->setSubFamily($this->getReference('subfamily_'.random_int(1, 10)));
            $genus->setSpeciesCount($this->faker->numberBetween(100, 100000));
            $genus->setFunFact($this->faker->sentence());
            $genus->setIsPublished($this->faker->boolean(75));
            $genus->setFirstDiscoveredAt($this->faker->dateTimeBetween('-200 years', 'now'));

            $this->setReference('genus_'.$i, $genus);
            $em->persist($genus);
        }
    }

    private function addGenusNote(EntityManager $em)
    {
        for ($i = 1; $i <= 100; $i++) {
            $genusNote = new GenusNote();
            $genusNote->setUsername($this->faker->userName);
            $genusNote->setUserAvatarFilename($this->faker->boolean() ? 'leanna.jpeg' : 'ryan.jpeg');
            $genusNote->setNote($this->faker->paragraph());
            $genusNote->setCreatedAt($this->faker->dateTimeBetween('-6 months', 'now'));
            $genusNote->setGenus($this->getReference('genus_'.random_int(1, 10)));

            $this->setReference('genus.note_'.$i, $genusNote);
            $em->persist($genusNote);
        }
    }

    private function addSubFamily(EntityManager $em)
    {
        for ($i = 1; $i <= 10; $i++) {
            $subFamily = new SubFamily();
            $subFamily->setName($this->faker->lastName);

            $this->setReference('subfamily_' . $i, $subFamily);
            $em->persist($subFamily);
        }
    }

    private function addUser(EntityManager $em)
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("weaverryan+$i@gmail.com");
            $user->setPlainPassword('iliketurtles');
            $user->setRoles(['ROLE_ADMIN']);
            $user->setAvatarUri($this->faker->imageUrl(100, 100, 'abstract'));

            $this->setReference('user_' . $i, $user);
            $em->persist($user);
        }

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("aquanaut$i@example.org");
            $user->setPlainPassword('aquanote');
            $user->setIsScientist(true);
            $user->setFirstName($this->faker->firstName);
            $user->setLastName($this->faker->lastName);
            $user->setUniversityName($this->faker->company.' University');
            $user->setAvatarUri($this->faker->imageUrl(100, 100, 'abstract'));

            $this->setReference('user.aquanaut_' . $i, $user);
            $em->persist($user);
        }
    }

    private function addGenusScientist(EntityManager $em)
    {
        for ($i = 1; $i <= 10; $i++) {
            $genusScientist = new GenusScientist();
            $genusScientist->setUser($this->getReference('user.aquanaut_'.random_int(1, 10)));
            $genusScientist->setGenus($this->getReference('genus_'.random_int(1, 10)));
            $genusScientist->setYearsStudied($this->faker->numberBetween(1, 30));

            $this->setReference('genus.scientist_' . $i, $genusScientist);
            $em->persist($genusScientist);
        }
    }

    private function genus()
    {
        $genera = [
            'Octopus',
            'Balaena',
            'Orcinus',
            'Hippocampus',
            'Asterias',
            'Amphiprion',
            'Carcharodon',
            'Aurelia',
            'Cucumaria',
            'Balistoides',
            'Paralithodes',
            'Chelonia',
            'Trichechus',
            'Eumetopias'
        ];

        $key = array_rand($genera);

        return $genera[$key];
    }
}
