<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $contactFormData = $form->getData();
                $message         = (new \Swift_Message('You Got Letter From Site'.getenv('APP_NAME')))
                    ->setFrom($contactFormData['email'])
                    ->setTo(getenv('APP_MANAGER_EMAIL'))
                    ->setBody(
                        $contactFormData['message'],
                        'text/plain'
                    );
                $mailer->send($message);
                $this->addFlash('success', 'Mail successfully sent');
                return $this->redirectToRoute('contact');
            } else {
                $this->addFlash('danger', 'Error Form Data');
            }
        }
        return $this->render(
            'contact/contact.html.twig', [
            'form' => $form->createView(),
        ]
        );
    }
}
