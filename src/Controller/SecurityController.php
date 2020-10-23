<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/account", name="account")
     */
    public function account(AuthenticationUtils $authenticationUtils, $registrationForm = null): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('profile_edit');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/index.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'registrationForm' => $registrationForm]);
    }

    /**
     * @Route("/account/password/forget", name="account_password_forget")
     */
    public function passwordForget(Request $request) {
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
                $this->emailVerifier->sendEmailConfirmation('account_password_recovery', $user, $template);
                $this->addFlash('success', 'Email for recovery password is send.');
            else:
                $this->addFlash('error', 'User not found with this email.');
            endif;
        endif;
        return $this->render('security/password_forget.html.twig');
    }
    
    /**
     * @Route("/account/password/recovery", name="account_password_recovery")
     */
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

        return $this->render('security/password_recovery.html.twig');
    }
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
}
