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

    public function ajouterViaCsv($resultats, $em, $encoder):void
    {
        // ouverture d'une transaction
        $this->_em->getConnection()->beginTransaction();
        $this->_em->getConnection()->setAutoCommit(false);

        // mise en place d'un message et d'un compteur pour récupérer les erreurs et leur numéro de ligne
        $message = "";
        $ligne = 0;
        try {
            // pour chaque ligne du fichier résultats
            foreach ($resultats as $resultat) {
                $ligne++;
                $user = new Participant();
                // vérification de chacun des champs du fichier si le champ est présent et si le contenu n'est pas vide
                // si ok, on hydrate le user
                // si non, on alimente un message en renseignant une exception et un numéro de ligne
                if (isset($resultat['nom']) && !empty($resultat['nom'])) {
                    $user->setNom($resultat['nom']);
                } else {
                    $message .= "Nom obligatoire à la ligne" . $ligne.". ";
                }
                if (isset($resultat['prenom']) && !empty($resultat['prenom'])) {
                    $user->setPrenom($resultat['prenom']);
                } else {
                    $message .= "Prenom obligatoire à la ligne" . $ligne.". ";
                }
                if (isset($resultat['username']) && !empty($resultat['username'])) {
                    $user->setUsername($resultat['username']);
                } else {
                    $message .= "Username obligatoire à la ligne" . $ligne.". ";
                }
                if (isset($resultat['email']) && !empty($resultat['email'])) {
                    $user->setEmail($resultat['email']);
                } else {
                    $message .= "Email obligatoire à la ligne" . $ligne.". " ;
                }
                if (isset($resultat['role']) && !empty($resultat['role'])) {
                    $user->setRoles([$resultat['role']]);
                } else {
                    $user->setRoles(['ROLE_USER']);
                }

                if (isset($resultat['telephone']) && !empty($resultat['telephone'])) {
                    $user->setRoles($resultat['telephone']);
                }
                if (isset($resultat['idsite']) && !empty($resultat['idsite'])) {
                    $siteRepo = $em->getRepository(Site::class);
                    $site = $siteRepo->find($resultat['idsite']);
                    $user->setSite($site);
                } else {
                    $message .= "Id site obligatoire ou pas au bon format à la ligne" . $ligne.". ";

                }
                if (isset($resultat['password']) && !empty($resultat['password'])) {
                    $hashed = $encoder->encodePassword($user, $resultat['password']);
                    $user->setPassword($hashed);
                } else {
                    $message .= "Mot de passe obligatoire à la ligne" . $ligne.". ";
                }
                $user->setUpdatedAt(new \DateTime());
                $user->setActif(true);


                // injection du user en cours
                $this->_em->persist($user);

            }
            // validation en BDD
            $this->_em->flush();
            $this->_em->getConnection()->commit();

            // récupère les violations de contrainte unique (un des champs username ou email)
            // annule la transaction et alimente le message si c'est le cas
        } catch (UniqueConstraintViolationException $e) {
            $this->_em->getConnection()->rollBack();
            $message .= "Un des emails ou username existe déjà dans le fichier. ";

            // récupère toutes les autres erreurs et effectue le rollback s'il y en a
        } catch (\Exception $e) {
            $this->_em->getConnection()->rollBack();

        } finally {
            // si le message n'est pas vide, on propage une exception au controller avec le message qu'il contient
            if (!empty($message)){
                $message .= "Opération annulée, veuillez vérifier votre fichier et recommencer. ";
                throw new \Exception($message);
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
