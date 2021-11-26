<?php

namespace App\Repository;

use App\Entity\Horario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Horario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Horario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Horario[]    findAll()
 * @method Horario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HorarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horario::class);
    }

    /**
     * @return Horario[]
     */
    /*public function dameHorario($user): array
    {
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
    'SELECT h
    FROM App\Entity\Horario h
    WHERE h.user = :user
    ORDER BY h.fecha ASC'
    )->setParameter('user', $user);

    // returns an array of Product objects
    return $query->getResult();
    }*/

    public function dameSaldo($user, $mes, $anyo): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT
            SUBTIME(SEC_TO_TIME(sum(TIME_TO_SEC(hora_salida))),SEC_TO_TIME(sum(TIME_TO_SEC(hora_entrada)))) AS saldo_Entrada,
            SEC_TO_TIME(sum(TIME_TO_SEC(hora_Saldo))) AS saldo_salida
            FROM horario h
            WHERE (h.user_id = :user) & (MONTH (h.fecha) = :mes) & (YEAR (h.fecha) = :anyo) & (h.hora_salida != "NULL")
            ';
        $result = $conn->prepare($sql);
        $result->execute(['user' => $user,
                        'mes' => $mes,
                        'anyo' => $anyo]);

        return $result->fetchAllAssociative();
    }

    public function dameHorario($user, $mes, $anyo): array
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT
            id,
            fecha,
            hora_entrada,
            hora_salida,
            hora_saldo,
            DATE_FORMAT(fecha,\'%W\') AS dia,
            SUBTIME(hora_salida,hora_entrada) AS saldo
            FROM horario h
            WHERE (h.user_id = :user) & (MONTH (h.fecha) = :mes) & (YEAR (h.fecha) = :anyo)
            ORDER BY h.fecha ASC
            ';
        $result = $conn->prepare($sql);

        $result->execute(['user' => $user,
            'mes' => $mes,
            'anyo' => $anyo]);

        return $result->fetchAllAssociative();
    }

    // /**
    //  * @return Horario[] Returns an array of Horario objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('h')
    ->andWhere('h.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('h.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?Horario
{
return $this->createQueryBuilder('h')
->andWhere('h.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
