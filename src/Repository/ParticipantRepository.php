<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function ajouterViaCsv($resultats, $em, $encoder, $validator)//:void
    {
        // création d'un tableau d'erreurs à retourner
        $errors = array();
        // création d'un booléen de test sur champ role
        $ok = true;

        // pour chaque ligne du fichier résultats on teste si le champ est bien présent
        // et on analyse via validator si le user est conforme
        foreach ($resultats as $resultat) {

            $user = new Participant();
            if (isset($resultat['nom'])) {
            $user->setNom($resultat['nom']);}
            if (isset($resultat['prenom'])) {
            $user->setPrenom($resultat['prenom']);}
            if (isset($resultat['username'])) {
            $user->setUsername($resultat['username']);}
            if (isset($resultat['email'])) {
            $user->setEmail($resultat['email']);}
            if (isset($resultat['role'])) {
                $user->setRoles([$resultat['role']]);
            } else {
                $user->setRoles(['ROLE_USER']);
            }
            if (isset($resultat['telephone'])) {
            $user->setTelephone($resultat['telephone']);}
            $user->setPassword($resultat['password']);
            // pour le site, on vérifie que l'id passé existe bien
            // sinon, on passe le booléen à false
            if (isset($resultat['idsite'])) {
                $siteRepo = $em->getRepository(Site::class);
                $site = $siteRepo->find($resultat['idsite']);
                if ($site) {
                    $user->setSite($site);
                } else {
                    $ok = false;
                }
            }
            $user->setUpdatedAt(new \DateTime());
            $user->setActif(true);

            // test du user par validator
            $userViolation = $validator->validate($user);


            // si le validator ne retourne pas d'erreurs et que le booléen est à true on encode le mdp
            // et on le met dans la base
            if (count($userViolation)<1 && $ok === true){
                $hashed = $encoder->encodePassword($user, $resultat['password']);
                $user->setPassword($hashed);
                // injection du user en cours
                $this->_em->persist($user);

                // sinon on remplit le tableau d'erreurs
            } else {
                $errors [] = $userViolation;
            }

        }
            // on flushe toutes les données non erronnées dans la base
            $this->_em->flush();
            // on retourne les erreurs s'il y en a
            return $errors;

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
            ->setMaxResults(25)
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
