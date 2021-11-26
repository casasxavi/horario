<?php

namespace App\Repository;

use App\Entity\Evento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evento|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evento|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evento[]    findAll()
 * @method Evento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evento::class);
    }


    public function listarEventos($user, $mes, $anyo): array
    {

        if ($mes == null) {
            $conn = $this->getEntityManager()->getConnection();

            $sql = '
                SELECT
                id,
                fecha,
                hora,
                descripcion,
                DATE_FORMAT(fecha,\'%W\') AS dia
                FROM evento e
                WHERE (e.user_id = :user)
                ORDER BY e.fecha ASC
                ';
            $result = $conn->prepare($sql);
    
            $result->execute(['user' => $user]);
    
            return $result->fetchAllAssociative();
        } else {
            $conn = $this->getEntityManager()->getConnection();

            $sql = '
                SELECT
                id,
                fecha,
                hora,
                descripcion,
                DATE_FORMAT(fecha,\'%W\') AS dia
                FROM evento e
                WHERE (e.user_id = :user) & (MONTH (e.fecha) = :mes) & (YEAR (e.fecha) = :anyo)
                ORDER BY e.fecha ASC
                ';
            $result = $conn->prepare($sql);
    
            $result->execute(['user' => $user,
                'mes' => $mes,
                'anyo' => $anyo]);
    
            return $result->fetchAllAssociative();
        }

    }


/*   public function listarEventos($user)
    {
    
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :val')
            ->setParameter('val', $user)
            ->orderBy('e.fecha', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }*/

    // /**
    //  * @return Evento[] Returns an array of Evento objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evento
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
