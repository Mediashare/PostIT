<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/profile/{username}", name="profile")
     */
    public function profile(?string $username = "") {
        if (!$username): 
            $user = $this->getUser(); 
        else:
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        endif;
        if (!$user):
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('index');
        endif;
        
        return $this->render('user/profile.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/profile/edit/{username}/", name="profile_edit")
     */
    public function edit(Request $request, ?string $username = "", UserPasswordEncoderInterface $passwordEncoder) {
        $em = $this->getDoctrine()->getManager();
        if ($username && $this->getUser() && $this->getUser()->isAdmin()):
            $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
            if (!$user): 
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('admin');
            endif; 
        endif;
        if (empty($user)): $user = $this->getUser(); endif;
        if ($request->isMethod('POST')):
            // Username
            if ($user->getUsername() != $request->get('username')):
                if ($em->getRepository(User::class)->findOneBy(['username' => $request->get('username')])):
                    $this->addFlash('error', 'Username already used.');
                else: 
                    $user->setUsername($request->get('username')); 
                    $this->addFlash('success', 'New username recorded.');
                endif;
            endif;
            // Email
            if ($user->getEmail() != $request->get('email')):
                if ($em->getRepository(User::class)->findOneBy(['email' => $request->get('email')])):
                    $this->addFlash('error', 'Email already used.');
                else: 
                    $user->setEmail($request->get('email'));
                    $this->addFlash('success', 'New email recorded.');
                endif;
            endif;
            // Password
            if ($request->get('password') && $request->get('passwordRepeat')):
                if ($request->get('password') === $request->get('passwordRepeat')):
                    $user->setPassword(
                        $passwordEncoder->encodePassword($user, $request->get('password'))
                    );
                    $this->addFlash('success', 'New password recorded.');
                else: $this->addFlash('error', 'Password not confirmed.'); endif;
            endif;
            $em->persist($user);
            $em->flush();
        endif;
        return $this->render('user/edit.html.twig');
    }

    /**
     * @Route("/profile/apikey/reset", name="profile_apikey_reset")
     */
    public function apikey() {
        $this->getUser()->setApikey(\sha1(\microtime().$this->getUser()->getId()));
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->flush();
        $this->addFlash('success', 'Apikey has been reseted.');

        return $this->redirectToRoute('profile');
    }
}
