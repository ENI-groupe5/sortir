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
            ->join('s.sortie_etat','e')
            ->andWhere('e.id = 2 OR s.organisateur = :u')
            ->setParameter('u',$user);
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
        if((($search->getDateDebut())!=null)&&(($search->getDateFin())!=null))
        {
            $query = $query
                ->andWhere('s.datHeureDebut BETWEEN :dd AND :df' )
                ->setParameter('dd',$search->getDateDebut())
                ->setParameter('df',$search->getDateFin());
        }
        if((($search->getDateDebut())!=null)&&(($search->getDateFin())==null))
        {
            $query = $query
                ->andWhere('s.datHeureDebut > :dd' )
                ->setParameter('dd',$search->getDateDebut());
        }
        if((($search->getDateDebut())==null)&&(($search->getDateFin())!=null))
        {
            $query = $query
                ->andWhere('s.datHeureDebut < :df' )
                ->setParameter('df',$search->getDateFin());
        }
        if ((($search->getOrganisateur())!=null)&&(($search->getInscrit())==null))
        {
            $query = $query
                ->andWhere('s.organisateur = :u')
                ->setParameter('u',$user);
        }
        if (((($search->getOrganisateur())!=null)&&(($search->getInscrit())!=null))&&(($search->getNoinscrit())==null))
        {
            $query = $query
                ->andWhere(':u MEMBER OF s.participants OR s.organisateur = :u')
                ->setParameter('u',$user);

        }
        if (((($search->getInscrit())!=null)&&(($search->getNoinscrit())==null))&&(($search->getOrganisateur())==null))
        {
            $query = $query
                ->andWhere(':u MEMBER OF s.participants')
                ->setParameter('u',$user->getId());
        }
        if ((($search->getNoinscrit())!=null)&&(($search->getInscrit())==null))
        {
            $query = $query
                ->andWhere(':u NOT MEMBER OF s.participants')
                ->setParameter('u',$user);
        }
        if (($search->getPast())!=null)
        {
            $query = $query
                ->andWhere('s.datHeureDebut BETWEEN :d AND :da')
                ->setParameter('d',new \DateTime())
                ->setParameter('da',new \DateTime('-1 month'));
        } else {
            $query = $query
                ->andWhere('s.datHeureDebut >= :da')
                ->setParameter('da',new \DateTime('-1 month'));
        }
        return $query->getQuery();
    }


    public function sortieMobileQuery($user) :Query
    {
        $query = $this
            ->createQueryBuilder('s')
            ->andWhere('s.site = :i')
            ->setParameter('i',$user->getSite())
            ->andWhere('s.datHeureDebut >= :da')
            ->setParameter('da',new \DateTime('-1 month'));
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
