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

    /**
     * @param $search
     * @param $user
     * @return Query
     */
    public function listSortieQuery($search,$user) : Query
    {
        $query = $this
            //Création de la requete
            ->createQueryBuilder('s')
            ->join('s.sortie_etat','e') //Joindre la table état pour les détails
            ->andWhere('e.id = 2 OR s.organisateur = :u') //Ne prendre que les sorties avec un statut "ouvert" sauf si l'on est l'organisateur
            ->setParameter('u',$user);

            //Ajout de contraintes en fonction des filtres
            //Recherche par libelle
        if(($search->getLibelle())!=null)
        {
            $query = $query
                ->andWhere('s.nom LIKE :l')
                ->setParameter('l',"%{$search->getLibelle()}%");        // Chercher par libelle avec % avant et après
        }
            //Recherche par site
        if(($search->getSites())!=null)
        {
            $query = $query
                ->join('s.site','si','WITH','si =:i')
                ->setParameter('i',$search->getSites());
        }

        // Recherche entre dates si est précisé date de début ET date de fin
        if((($search->getDateDebut())!=null)&&(($search->getDateFin())!=null))
        {
            $query = $query
                ->andWhere('s.datHeureDebut BETWEEN :dd AND :df' )
                ->setParameter('dd',$search->getDateDebut())
                ->setParameter('df',$search->getDateFin());
        }
        //Recherche après une date si seulement une date de début est précisée
        if((($search->getDateDebut())!=null)&&(($search->getDateFin())==null))
        {
            $query = $query
                ->andWhere('s.datHeureDebut > :dd' )
                ->setParameter('dd',$search->getDateDebut());
        }
        //Recherche avant une date si seulement une date de fin est précisée
        if((($search->getDateDebut())==null)&&(($search->getDateFin())!=null))
        {
            $query = $query
                ->andWhere('s.datHeureDebut < :df' )
                ->setParameter('df',$search->getDateFin());
        }
        //Recherche par organisateur si seul la case organisateur est cochée
        if (((($search->getOrganisateur())!=null)&&(($search->getInscrit())==null))&&(($search->getNoinscrit())==null))
        {
            $query = $query
                ->andWhere('s.organisateur = :u')
                ->setParameter('u',$user);
        }
        //Recherche par organisateur ET sorties auxquelles je suis inscrit
        if (((($search->getOrganisateur())!=null)&&(($search->getInscrit())!=null))&&(($search->getNoinscrit())==null))
        {
            $query = $query
                ->andWhere(':u MEMBER OF s.participants OR s.organisateur = :u')
                ->setParameter('u',$user);

        }
        //Recherche par organisateur ET sorties auxquelles je ne suis pas inscrit
        if (((($search->getOrganisateur())!=null)&&(($search->getInscrit())==null))&&(($search->getNoinscrit())!=null))
        {
            $query = $query
                ->andWhere(':u NOT MEMBER OF s.participants OR s.organisateur = :u')
                ->setParameter('u',$user);

        }
        //Recherche par Sorties auxquelles je suis inscrit
        if (((($search->getInscrit())!=null)&&(($search->getNoinscrit())==null))&&(($search->getOrganisateur())==null))
        {
            $query = $query
                ->andWhere(':u MEMBER OF s.participants')
                ->setParameter('u',$user->getId());
        }
        //Recherche par sorties auxquelles je ne suis pas inscrit
        if ((($search->getNoinscrit())!=null)&&(($search->getInscrit())==null))
        {
            $query = $query
                ->andWhere(':u NOT MEMBER OF s.participants')
                ->setParameter('u',$user);
        }
        //Recherche par dates
        if (($search->getPast())!=null)
        {
            $query = $query
                ->andWhere('s.datHeureDebut BETWEEN :d AND :da')
                ->setParameter('d',new \DateTime())
                ->setParameter('da',new \DateTime('-1 month'));//Rechercher par dates passées (entre 1 mois et hier)
        } else {
            $query = $query
                ->andWhere('s.datHeureDebut >= :da')
                ->setParameter('da', new \DateTime('-1 month'));// Sinon rechercher toutes les dates après il y'a 1 mois
        }
            $query=$query->orderBy('e.id','ASC');//Classer par etat pour que les sorties dont je suis l'organisateur et qui ne sont pas encore publiées
                                                           //Arrivent les premières sur la liste
        return $query->getQuery(); // Retourne une Query et pas une liste, c'est ensuite l'objet paginator dans le controller qui est en charge d'executer la requete.
    }


    public function sortieMobileQuery($user) :Query
    {
        $query = $this
            ->createQueryBuilder('s')
            ->join('s.lieu','l')
            ->join('l.lieu_ville','v')
            ->andWhere('s.site = :i')
            ->setParameter('i',$user->getSite())
            ->join('s.sortie_etat','e')
            ->andWhere('e.id = 2 OR s.organisateur = :u')
            ->setParameter('u',$user)
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
