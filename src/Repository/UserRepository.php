<?php


namespace App\Repository;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $username
     * @return int|mixed|string
     */
    public function getPasswordAndEmailByUsername($username)
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->select('u')
            ->where('u.username = :username')
            ->setParameter('username', $username);

        $query = $qb->getQuery();

        return $query->getResult(Query::HYDRATE_ARRAY);
    }

}