<?php
namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use App\Security\CustomAuthenticator;
use App\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController {
    public function account(AuthenticationUtils $authenticationUtils, $registrationForm = null): Response {
        if ($this->getUser()): return $this->redirectToRoute('profile'); endif;
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('app/account.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'registrationForm' => $registrationForm]);
    }

    public function passwordForget(Request $request, EmailVerifier $emailVerifier) {
        if ($request->isMethod('POST')):
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['email' => \strtolower($request->get('email'))]);
            if ($user):
                $user->setToken(\sha1(\microtime().$user->getId()));
                $em->persist($user);
                $em->flush();

                $template = (new TemplatedEmail())
                    ->from(new Address($this->getParameter('mailer.from'), $this->getParameter('mailer.from_name')))
                    ->to($user->getEmail())
                    ->subject('Password recovery')
                    ->htmlTemplate('mail/password_recovery.html.twig');
                $emailVerifier->sendEmailConfirmation('account_password_recovery', $user, $template);
                $this->addFlash('success', 'Email for recovery password is send.');
            else:
                $this->addFlash('error', 'User not found with this email.');
            endif;
        endif;
        return $this->render('app/password_forget.html.twig');
    }
    
    public function passwordRecovery(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        if (!$request->get('token')):
            return $this->redirectToRoute('account_password_forget');
        endif;
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['token' => $request->get('token')]);
        if (!$user):
            $this->addFlash('error', 'User not found with this token.');
            return $this->redirectToRoute('account_password_forget');
        endif;
        
        if ($request->isMethod('POST')):
            if ($request->get('password') != $request->get('passwordRepeat')):
                $this->addFlash('error', 'Password is not confirmed.');    
                return $this->render('security/password_recovery.html.twig');
            endif;
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $request->get('password'))
            );
            $user->setToken(null);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('account');
        endif;

        return $this->render('app/password_recovery.html.twig');
    }

    public function verifyUserEmail(Request $request, EmailVerifier $emailVerifier): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('account');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('index');
    }
    
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, CustomAuthenticator $authenticator, EmailVerifier $emailVerifier): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()):
            if (!$form->isValid()):
                if ($request->get('registration_form')['plainPassword']['first'] !== $request->get('registration_form')['plainPassword']['second']):
                    $this->addFlash('verify_email_error', 'Password not confirmed.');    
                endif;
                return $this->redirectToRoute('account');
            endif;
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setEmail(\strtolower($user->getEmail()));
            $em = $this->getDoctrine()->getManager();
            if (!$em->getRepository(User::class)->findAll()):
                $user->setRoles(['ROLE_ADMIN']);
            endif;
            if ($em->getRepository(User::class)->findOneBy(['email' => \strtolower($user->getEmail())])):
                $this->addFlash('verify_email_error', 'This email already exist.');
                return $this->redirectToRoute('account');
            endif;
            if ($em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()])):
                $this->addFlash('verify_email_error', 'This username already exist.');
                return $this->redirectToRoute('account');
            endif;
            $em->persist($user);
            $em->flush();

            // generate a signed url and email it to the user
            $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($this->getParameter('mailer.from'), $this->getParameter('mailer.from_name')))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('mail/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        endif;
        return $this->render('partial/_registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    public function logout() {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
}
