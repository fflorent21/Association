<?php

namespace App\DataFixtures;

use App\Entity\NewsLetter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NewsLetterFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $newsLetter = new NewsLetter();

        $newsLetter
            ->setEmail('florent.f21@orange.fr')
        ;

        $manager->persist($newsLetter);
    
        $manager->flush();
    }
}
