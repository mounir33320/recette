<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
//       $user = new User();
//       $user->setEmail("toto@toto.fr")
//           ->setFirstname("Toto")
//           ->setLastname("TOTO")
//            ->setRoles(["ROLE_USER"])
//            ->setPassword($this->userPasswordEncoder->encodePassword($user, "toto"));
//
//       $manager->persist($user);
//       $manager->flush();

//        $user2 = new User();
//        $user2->setEmail("tata@tata.fr")
//            ->setFirstname("Tata")
//            ->setLastname("TATA")
//            ->setRoles(["ROLE_USER"])
//            ->setPassword($this->userPasswordEncoder->encodePassword($user2, "tata"));
//
//        $manager->persist($user2);
//        $manager->flush();
//
//        $user3 = new User();
//        $user3->setEmail("titi@titi.fr")
//            ->setFirstname("Titi")
//            ->setLastname("TITI")
//            ->setRoles(["ROLE_USER"])
//            ->setPassword($this->userPasswordEncoder->encodePassword($user3, "titi"));

        $userAdmin = new User();
        $userAdmin->setEmail("tutu@tutu.fr")
            ->setFirstname("Tutu")
            ->setLastname("TUTU")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->userPasswordEncoder->encodePassword($userAdmin, "tutu"));

        $manager->persist($userAdmin);
        $manager->flush();
    }
}
