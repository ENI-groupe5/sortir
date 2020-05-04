<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Participant) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * fonction pour ajouter un participant via un fichier csv
     */
    public function ajouterViaCsv($resultats, $em, $encoder):void {

        foreach ($resultats as $resultat){
            $user = new Participant();
            if ($resultat['nom']){
            $user->setNom($resultat['nom']);
            } else {
                throw new \Exception("Nom obligatoire! il manque au moins un nom dans votre fichier");
            }
            if ($resultat['prenom']){
                $user->setPrenom($resultat['prenom']);
            } else {
                throw new \Exception("Prénom obligatoire! il manque au moins un prénom dans votre fichier");
            }
            if ($resultat['username']){
                $user->setUsername($resultat['username']);
            } else {
                throw new \Exception("Username obligatoire! il manque au moins un username dans votre fichier");
            }
            if ($resultat['email']){
                $user->setEmail($resultat['email']);
            } else {
                throw new \Exception("email obligatoire! il manque au moins un email dans votre fichier");
            }
            if ($resultat['roles']){
                $user->setRoles($resultat['roles']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            if ($resultat['telephone']){
                $user->setRoles($resultat['telephone']);
            }
            if ($resultat['idsite']){
                $siteRepo = $em->getRepository(Site::class);
                $site = $siteRepo->find($resultat['idsite']);
                $user->setSite($site);
            } else {
                throw new \Exception("il manque au moins un site ou il n'est pas au bon format");
            }
            if ($resultat['password']){
                $hashed = $encoder->encodePassword($user,$resultat['password']);
                $user->setPassword($hashed);
            } else {
                throw new \Exception("il manque au moins un mot de passe");
            }
            $user->setUpdatedAt(new \DateTime());
            $user->setActif(true);
            try {
                $this->_em->persist($user);
                $this->_em->flush();
            } catch (UniqueConstraintViolationException $e){
                throw new \Exception("un des emails ou username existe déjà dans le fichier");
            }
        }


    }

    /**
     * @param $participantId
     * @return Participant[] Returns an array of Participant objects
     */
    public function rechercherParticipant($participantId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :participant')
            ->setParameter('participant', $participantId)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Participant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
