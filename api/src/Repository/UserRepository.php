<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User Returns an admin
     */
    public function findOneAdmin()
    {
        $userMapping = new ResultSetMapping;
        $userMapping->addEntityResult(User::class, 'u');
        $userMapping->addFieldResult('u', 'id', 'id');

        $query = "SELECT * FROM \"user\" WHERE '\"ROLE_ADMIN\"' = ANY (ARRAY(select * from json_array_elements(roles))::text[]);";
        return $this->getEntityManager()->createNativeQuery($query, $userMapping)->getResult()[0];
    }

    /**
     * @return User Returns a simple user (not admin)
     */
    public function findOneNotAdmin()
    {
        $userMapping = new ResultSetMapping;
        $userMapping->addEntityResult(User::class, 'u');
        $userMapping->addFieldResult('u', 'id', 'id');

        $query = "SELECT * FROM \"user\" WHERE NOT ('\"ROLE_ADMIN\"' = ANY (ARRAY(select * from json_array_elements(roles))::text[]));";
        return $this->getEntityManager()->createNativeQuery($query, $userMapping)->getResult()[0];
    }
}
