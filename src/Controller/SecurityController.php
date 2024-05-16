<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserProfileFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    
    #[Route('/profile/{id}', name: 'app_profile')]
    public function editProfile(Request $request, ManagerRegistry $managerRegistry, int $id, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user = $this->getUser(); // Get the logged-in user

        if (!$user instanceof User) {
          throw $this->createNotFoundException('User not found.');
        }
    $form = $this->createForm(UserProfileFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle password updating
        $plainPassword = $form->get('plainPassword')->getData();
        if ($plainPassword) {
        $encodedPassword = $passwordEncoder->hashPassword($user, $plainPassword);
        $user->setPassword($encodedPassword);
        }

        // Handle image upload
        /** @var UploadedFile|null $img */
        $img = $form->get('img')->getData();
        if ($img) {
        $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^a-zA-Z0-9-_.]/', '', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $img->guessExtension();

        try {
            $img->move($this->getParameter('kernel.project_dir') . '/public/img', $newFilename);
            $user->setImg($newFilename);
        } catch (FileException $e) {
            $this->addFlash('error', 'An error occurred while uploading the image.');
            return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
        }
        }

        $user->setUpdatedAt(new \DateTime('now', new \DateTimeZone(date_default_timezone_get())));

        $entityManager = $managerRegistry->getManager();
        $entityManager->flush();

        // Redirect to the display profile page while staying logged in
        return $this->redirectToRoute('app_my_profile', ['id' => $user->getId()]);
    }

    return $this->render('security/edit_profile.html.twig', [
        'form' => $form->createView(),
        'user' => $user
    ]);
    }

    

    #[Route('/my-profile/{id}', name: 'app_my_profile')]
    public function myProfile(int $id): Response
    {
        $user = $this->getUser(); // Get the logged-in user
        if (!$user instanceof User || $user->getId() !== $id) {
            throw $this->createNotFoundException('User not found.');
        }

        return $this->render('security/view_profile.html.twig', [
            'user' => $user,
        ]);
    }

}
