<?php
     
namespace App\Controller;
use DateTime;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use App\Entity\Messages;
use App\Form\MessagesType;
use App\Repository\MessagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
// $session = new Session();
// $session->start();
$session = new Session();

//var_dump(get("users"));
/**
 * @Route("/ticket")
 */

class TicketController extends AbstractController
{
    
    
   
    /**
     * @Route("/", name="ticket_index", methods={"GET"})
     */
    public function index(TicketRepository $ticketRepository): Response
    {  
      
                if(in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
                {
                    return $this->render('ticket/index.html.twig', [
                        'tickets' => $ticketRepository->findAll()
                        
                    ]);
                }
                else{
                    
                    return $this->render('ticket/index.html.twig', [
                        'tickets' => $ticketRepository->findBy(
                            ["auth"=>$this->getUser()->getId()],
                            ['start' => 'DESC'])
                        
                    ]);
                }
            

        
      
    
    }

    /**
     * @Route("/new", name="ticket_new", methods={"GET","POST"})
     */
    public function new(Request $request,Security $security,UserRepository $userRepository): Response
    {
      
        $ticket = new Ticket();
        
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        $ticket->setUser($security->getUser());
      
        $ticket->setStart(new DateTime());
        
        if ($form->isSubmitted() && $form->isValid()) {
            $request = Request::createFromGlobals();
            $request = new Request(
                $_GET,
                $_POST
            );
            if($request->request->get("getAssigne")!=null && $request->request->get("getAssigne")!=0){
                $ticket->setAuth($request->request->get("getAssigne"));
            }else{
                $ticket->setAuth($security->getUser()->getId());
            }
         
            // var_dump($request->request->get("getAssignes"));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'users'=>$userRepository->findAll(),
            'form' => $form->createView(),
        ]);
       
    }

    /**
     * @Route("/{id}", name="ticket_show", methods={"GET","POST"})
     */
    public function show(Ticket $ticket,Request $request,Security $security,MessagesRepository $messagesRepository): Response
    {  
        
            
            $message = new Messages();
            $form = $this->createForm(MessagesType::class, $message);
            $form->handleRequest($request);
            $message->setUserId($security->getUser());
            $message->setStartmessage(new DateTime());
            $message->setTicketmessage($ticket->getId());
        

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();

                return $this->redirectToRoute('ticket_show', ['id' => $ticket->getId()]);
            }
            return $this->render('ticket/show.html.twig', [
                'ticket' => $ticket,
                'message' => $message,
                'messages' => $messagesRepository->findBy(
                 ["ticketmessage"=>$ticket->getId()],
                 ['startmessage' => 'ASC']
                ),
                'form' => $form->createView(),
            ]);

            
        
    }

    /**
     * @Route("/{id}/edit", name="ticket_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ticket $ticket): Response
    {
        
            $form = $this->createForm(TicketType::class, $ticket);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
    
                return $this->redirectToRoute('ticket_index');
            }
    
            return $this->render('ticket/edit.html.twig', [
                'ticket' => $ticket,
                'form' => $form->createView(),
            ]);
         
    }

    /**
     * @Route("/{id}", name="ticket_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }
}
