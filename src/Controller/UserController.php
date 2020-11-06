<?php
namespace App\Controller;

use App\Service\Text;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController {
    public function profile(?string $username = null) {
        if (!$username): 
            $user = $this->getUser(); 
        else:
            $user = $this->getUser(['slug' => $username]);
        endif;
        if (!$user):
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('index');
        endif;
        
        return $this->render('app/profile.html.twig', ['user' => $user]);
    }

    public function edit(Request $request, ?string $username = null, UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger) {
        if ($username):
            $user = $this->getUser(['slug' => $username]);
            if (!$user):
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('profile');
            endif;
            if ($user != $this->getUser() && !$this->getUser()->isAdmin()):
                $this->addFlash('error', 'You have not permission.');
                return $this->redirectToRoute('profile');
            endif;
        else: $user = $this->getUser(); endif;
        if ($request->isMethod('POST')):
            // Username
            $form = $request->request;
            if ($user->getUsername() != $form->get('username')):
                $text = new Text();
                $slug = $text->slugify($form->get('username'));
                if ($this->getUser(['slug' => $slug])):
                    $this->addFlash('error', 'Username already used.');
                else: 
                    $user->setUsername($form->get('username')); 
                    $this->addFlash('success', 'New username recorded.');
                endif;
            endif;
            // Email
            if ($user->getEmail() != $form->get('email')):
                if ($this->getUser(['email' => \strtolower($form->get('email'))])):
                    $this->addFlash('error', 'Email already used.');
                else: 
                    $user->setEmail($form->get('email'));
                    $this->addFlash('success', 'New email recorded.');
                endif;
            endif;
            // Password
            if ($form->get('password') && $form->get('passwordRepeat')):
                if ($form->get('password') === $form->get('passwordRepeat')):
                    $user->setPassword(
                        $passwordEncoder->encodePassword($user, $form->get('password'))
                    );
                    $this->addFlash('success', 'New password recorded.');
                else: $this->addFlash('error', 'Password not confirmed.'); endif;
            endif;
            // Avatar
            $avatar = $request->files->get('avatar');
            if ($avatar):
                $directory = $this->getParameter('avatars_directory');
                if ($user->getAvatar() != 'default.png' && 
                    \file_exists($old_avatar = $directory . '/' . $user->getAvatar())):
                    \unlink($old_avatar);
                endif;
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatar->guessExtension();
                $avatar->move($directory, $newFilename);
                $user->setAvatar($newFilename);
            endif;
            // Signature
            if ($user->getSignature() != $form->get('signature')):
                $user->setSignature($form->get('signature'));
            endif;

            $this->getEm()->persist($user);
            $this->getEm()->flush();
            return $this->redirectToRoute('profile_edit', ['username' => $user->getSlug()]);
        endif;
        return $this->render('app/profile_edit.html.twig', ['user' => $user]);
    }

    public function apikey() {
        $this->getUser()->setApikey(\sha1(\microtime().$this->getUser()->getId()));
        $this->getEm()->persist($this->getUser());
        $this->getEm()->flush();
        $this->addFlash('success', 'Apikey has been reseted.');

        return $this->redirectToRoute('profile');
    }


    public function delete(string $id) {
        $user = $this->getUser(['id' => $id]);
        if (!$user):
            $this->addFlash('error', 'No user found.');
            return $this->redirectToRoute('admin');
        endif;
        foreach ($user->getPosts() as $post):
            $this->getEm()->remove($post);
        endforeach;
        foreach ($user->getComments() as $comment):
            $this->getEm()->remove($comment);
        endforeach;
        
        $this->getEm()->remove($user);
        $this->getEm()->flush();
        
        return $this->redirectToRoute('admin');
    }
}
