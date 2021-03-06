<?php

namespace TravelbuddyBundle\Controller;

use AppBundle\Entity\TravelGroup;
use AppBundle\Repository ;
use AppBundle\Entity\TravelBuddy ;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TravelbuddyBundle\Form\AddMemberType;
use TravelbuddyBundle\Form\TravelBuddyType;
use Symfony\Component\HttpFoundation\Request ;

class BuddysController extends Controller
{


    public function ajoutAction(Request $request)
    {     //to get CurrentUserID
        $user = $this->container->get('security.token_storage')->getToken()->getUser() ;

        //etape1.a créer un objet vide
        $buddy = new TravelBuddy();
        //mettre le id dans l'obj
        //1.b.preparation du form
        $form = $this->createForm(TravelBuddyType::class,$buddy);
        //2.1.Recup form
        $form = $form->handleRequest($request);
        //2.b.form validation
        if ($form->isValid()) {
            //putting the current user cuz in doctrine u put whole object in the forgien key//
           $buddy->setIdUser($user);
            /** @var UploadedFile $file */
            $file = $buddy->getImageFile();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            $buddy->setImage($fileName);

            //3.a.prepration d'entity manager
            $em = $this->getDoctrine()->getManager();
            //3.b.persist l'objet dans l'orm
            $em->persist($buddy);
            //3.c.sauv ds BD
            $em->flush();
            //3.d.redit
            return $this->redirectToRoute('travelbuddy_BuddysList');

        }
        //1.c envoi d form
        return $this->render('@Travelbuddy/Buddys/ajout.html.twig',
            array('form' => $form->createView()));

    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    public function listAction(){
        $ok = false ;
        if($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();


            $Buddy = $this->getDoctrine()
                ->getRepository(TravelBuddy::class)
                ->getBuddyByIdCurrentUser($user);

            if ($Buddy != null) {
                $ok = true ;

            }
        }
        dump($ok) ;
        $travelbuddies=$this->getDoctrine()
            ->getRepository(TravelBuddy::class)->findAll();
        dump($travelbuddies);

        return $this->render('@Travelbuddy/Buddys/list.html.twig',
            array('travelbuddies'=>$travelbuddies)) ;



    }


        public function mylistAction()
    {

        if($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
        $user = $this->container->get('security.token_storage')->getToken()->getUser() ;
        $id = $user->getID();


        $travelbuddies = $this->getDoctrine()
            ->getRepository(TravelBuddy::class)->findByIdUser($id); //need do findById //
        return $this->render('@Travelbuddy/Buddys/mylist.html.twig',
            array('travelbuddies' => $travelbuddies));


    }

          else
              return $this->redirectToRoute('fos_user_security_login') ;
    }


    public function deleteAction($id) {
        //entity manager
        $em=$this->getDoctrine()->getManager() ;
        //recu de l'objet avec $id
        $buddy=$this->getDoctrine()
            ->getRepository(TravelBuddy::class)
            ->find($id);
        //delete object from ORM
        $em->remove($buddy);
        //delete from the data base
        $em->flush();

        return $this->redirectToRoute('travelbuddy_BuddysMyList') ;
    }


    public function updateAction(Request $request,$id){
        //etape1.a recupriation de l'objet avec $id
        $em=$this->getDoctrine()->getManager() ;
        //recu de l'objet avec $id
        $buddy=$this->getDoctrine()
            ->getRepository(TravelBuddy::class)
            ->find($id);
        //1.b.preparation du form
        $form=$this->createForm(TravelBuddyType::class,$buddy) ;
        //2.1.Recup form
        $form=$form->handleRequest($request) ;
        //2.b.form validation
        if($form->isValid()){
            //3.a.prepration d'entity manager
            $em=$this->getDoctrine()->getManager();
            //3.c.sauv ds BD
            $em->flush();
            //3.d.redit
            return $this->redirectToRoute('travelbuddy_BuddysMyList') ;

        }
        //1.c envoi d form
        return $this->render('@Travelbuddy/Buddys/ajout.html.twig' ,
            array('form'=>$form->createView())) ;
    }


    public function detaillAction($id){

        $travelbuddies=$this->getDoctrine()
            ->getRepository(TravelBuddy::class)->findByidTravelBuddy($id);
        return $this->render('@Travelbuddy/Buddys/detail.html.twig',
            array('travelbuddies'=>$travelbuddies)) ;

    }

public function adminAction()
{


        $travelbuddies=$this->getDoctrine()
            ->getRepository(TravelBuddy::class)->findAll();



        return $this->render('@Travelbuddy/Buddys/admin.html.twig',
            array('travelbuddies'=>$travelbuddies)) ;
        dump($travelbuddies) ;



}


    public function adminDeleteAction($id) {
        //entity manager
        $em=$this->getDoctrine()->getManager() ;
        //recu de l'objet avec $id
        $buddy=$this->getDoctrine()
            ->getRepository(TravelBuddy::class)
            ->find($id);
        //delete object from ORM
        $em->remove($buddy);
        //delete from the data base
        $em->flush();

        return $this->redirectToRoute('travelbuddy_AdminBuddy') ;
    }

}
