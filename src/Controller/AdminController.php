<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;


class AdminController extends AbstractController
{
 /**
+      * Require ROLE_ADMIN for only this controller method.
+      *
+      * @IsGranted("ROLE_ADMIN")
+      */
    public function adminDashboard()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // or add an optional message - seen by developers
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

    }
    public function index()
    {
            // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
        return new Response('Well hi there '.$user->getFirstName());
       
    }
    /**
     * @Route("/admin",name="admin")
     */
    public function admin(UserRepository $listUsers,Security $security)
    {  
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                  return $this->redirectToRoute('app_login');
                }
        else{
        
            if($security->getUser()->getRoles()[0]=='ROLE_ADMIN')
            {
                return $this->render('admin/index.html.twig', [
                    'users' => $listUsers->findAll(),
                ]);
            }
            else{
                echo "<script type='text/javascript'>alert('vous etez pas administrateur');</script>";
                return $this->redirectToRoute('index');
            
            }
        }
        
    }
}


