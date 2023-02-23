<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Component\HttpFoundation\UrlHelper;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class ParrainageController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    private UrlHelper $urlHelper;

    public function __construct(EmailVerifier $emailVerifier, UrlHelper $urlHelper)
    {
        $this->emailVerifier = $emailVerifier;
        $this->urlHelper = $urlHelper;
    }

    #[Route('/parrainage/parrain_id={user_id}', name: 'app_parrainage')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager, UserRepository $userRepository, $user_id): Response
    {
    	$parrain = $userRepository->findOneBy(['id' => $user_id]);

    	// Recherche du parrain
    	if(!$parrain){
    		return $this->redirectToRoute('app_register');
    	}

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        	$user->setParrain($parrain);
            $user->setIsVerified(1);

            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            /* generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('cashrew@domaine.com', 'CASHREWARDS'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email */

            $this->addFlash('success', 'Votre compte à bien été avec succes!');

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        if($form->isSubmitted() && !$form->isValid()){
            $response = new Response;
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);

            return $this->renderForm('registration/register.html.twig', [
                'registrationForm' => $form,
            ], $response);

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_parrainage');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_parrainage');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_parrainage');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_parrainage');
    }

    #[Route('/lien-de-parrainage', name: 'app_parrainage_link')]
    public function parrainageLink(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        return $this->render('user/lienParrainage.html.twig', [
        	'parrainageLink' => $this->urlHelper->getAbsoluteUrl('/parrainage/parrain_id=' . $this->getUser()->getId()),
        ]);
    }
}
