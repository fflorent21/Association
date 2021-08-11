<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setAuthor('John Doe')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setPhone('+33 123456789')
            ->setEmail('user@collectif.fr')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setRoles(['ROLE_USER'])
            ->setIsVerified(1)
            ->setRgpd(1)
        ;
        
        $manager->persist($user);
        $this->addReference('John Doe', $user);

        $modo = new User();
        $modo
            ->setAuthor('Modérateur')
            ->setFirstName('Modo')
            ->setLastName('Modo')
            ->setPhone('+33 66666666')
            ->setEmail('modo@collectif.fr')
            ->setPassword($this->encoder->encodePassword($modo, 'password'))
            ->setRoles(['ROLE_MODO'])
            ->setIsVerified(1)
            ->setRgpd(1)
        ;
        
        $manager->persist($modo);
        $this->addReference('Modérateur', $modo);

        $manager->flush();
       
        $admin = new User();
        $admin
            ->setAuthor('Admin')
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setPhone('+33 66666666')
            ->setEmail('admin@collectif.fr')
            ->setPassword($this->encoder->encodePassword($admin, 'password'))
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(1)
            ->setRgpd(1)
        ;
        
        $manager->persist($admin);

        $manager->flush();
    }
}
