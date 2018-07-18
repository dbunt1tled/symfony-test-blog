<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Services\Mailer\Emailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param Emailer $emailer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function contact(Request $request, Emailer $emailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $contactFormData = $form->getData();
                $emailer->create($contactFormData['email'],getenv('APP_MANAGER_EMAIL'),'You Got Letter From Site'.getenv('APP_NAME'));
                $emailer->sendMessage($contactFormData['message']);
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
