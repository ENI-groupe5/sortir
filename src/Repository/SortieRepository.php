<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\User;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function listSortieQuery($search,$user) : Query
    {
        $query = $this
            ->createQueryBuilder('s')
            ->join('s.sortie_etat','e');

            //Recherche par libelle
        if(($search->getLibelle())!=null)
        {
            $query = $query
                ->andWhere('s.nom LIKE :l')
                ->setParameter('l',"%{$search->getLibelle()}%");
        }
        if(($search->getSites())!=null)
        {
            $query = $query
                ->join('s.site','si','WITH','si =:i')
                ->setParameter('i',$search->getSites());
        }
        if(($search->getDateDebut())!=null)
        {
            $query = $query
                ->andWhere('s.datHeureDebut <= :dd')
                ->setParameter('dd',$search->getDateDebut());
        }
        if(($search->getDateFin())!=null)
        {
            $query = $query
                ->andWhere('s.datHeureDebut >= :df')
                ->setParameter('df',$search->getDateFin());
        }


        {
        if (($search->getOrganisateur())!=null)
        {
            $query = $query
                ->join('s.organisateur','o','WITH','o.id=:u')
                ->setParameter('u',$user);
        }

        if (($search->getInscrit())!=null)
        {
            $query = $query
                ->join('s.participants','p','WITH','p.id=:u')
                ->setParameter('u',$user->getId());
        }
        if (($search->getNoinscrit())!=null)
        {
            $query = $query
                ->andWhere('s.participants_id != :u')
                ->setParameter('u',$user->getId());
        }
        if (($search->getPast())!=null)
        {
            $query = $query
                ->andWhere('s.datHeureDebut <= CURRENT_DATE');
        }
        }




        return $query->getQuery();

    }

    /*
    public function findOneBySomeField($value): ?SortieFixtures
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
