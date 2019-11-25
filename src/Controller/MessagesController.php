<?php

namespace App\Controller;
use DateTime;
use App\Entity\Messages;
use App\Form\MessagesType;
use App\Repository\MessagesRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/messages")
 */
class MessagesController extends AbstractController
{
    /**
     * @Route("/", name="messages_index", methods={"GET"})
     */
    public function index(MessagesRepository $messagesRepository): Response
    { 
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
          }
        else{
            return $this->render('messages/index.html.twig', [
                'messages' => $messagesRepository->findAll(),
            ]);
        }
    }

    /**
     * @Route("/new", name="messages_new", methods={"GET","POST"})
     */
    // public function new(Request $request,Security $security,TicketRepository $ticketRepository): Response
    // {   
    //     if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
    //         return $this->redirectToRoute('login');
    //       }
    //     else{
    //         $message = new Messages();
    //         $form = $this->createForm(MessagesType::class, $message);
    //         $form->handleRequest($request);
    //         $message->setUserId($security->getUser());
    //         $message->setTicketmessage($ticketRepository->getId());

    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $entityManager = $this->getDoctrine()->getManager();
    //             $entityManager->persist($message);
    //             $entityManager->flush();

    //             return $this->redirectToRoute('messages_index');
    //         }

    //         return $this->render('messages/new.html.twig', [
    //             'message' => $message,
    //             'form' => $form->createView(),
    //         ]);
    //      }
    // }

    /**
     * @Route("/{id}", name="messages_show", methods={"GET"})
     */
    public function show(Messages $message): Response
    { 
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
          }
        else{
            return $this->render('messages/show.html.twig', [
                'message' => $message,
            ]);
            }
    }

    /**
     * @Route("/{id}/edit", name="messages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Messages $message): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
          }
        else{
            $form = $this->createForm(MessagesType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('ticket_index');
            }

            return $this->render('messages/edit.html.twig', [
                'message' => $message,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="messages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Messages $message): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }
}
