<?php

namespace AppBundle\Repository;

/**
 * ReservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

class ReservationRepository extends \Doctrine\ORM\EntityRepository
{

    public function findreservation($iduser){

        $qb=$this->getEntityManager()
            ->createQuery("SELECT u 
                            FROM AppBundle:Reservation u JOIN AppBundle:Event r WITH u.idEvent = r.idEvent
                            WHERE u.idUser='$iduser'");

        return $qb->getResult();

    }





}