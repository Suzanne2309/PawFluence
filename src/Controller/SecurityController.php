<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    //Fonction de connexion
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    //Fonction de déconnexion
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }

    //Fonction pour modifier un profile
    #[Route('/user/{id}/edit', name: 'edit_user')] //On crée la route vers la fonction
    #[IsGranted('edit', 'user')] //Si l'URL contient l'action edit et l'objet user alors on va vérifier si l'utilisateur et le même que celui du compte
    public function editUser(User $user = null, Request $request, UserRepository $userRepository, EntityManagerInterface $em): Response
    {   
        if ($user->getId() !== $this->getUser()->getId()) { //Si On fait appel à un post existant, on va comparer l'utilisateur du post et l'utilisateur de la sesion. Si les deux ne coincide pas,
            throw $this->createAccessDeniedException();
            /* var_dump($post->getUserOfPost(), $this->getUser()); die(); */
            $this->addFlash(
                'notice',
                'Vous n`avez pas le droit d`editer ce profil !'
            );
            return $this->redirectToRoute('show_user');
        }

        $form = $this->createForm(ProfileType::class, $user); //On crée un nouveau formulaire post avec le fichier PostType  

        $form->handleRequest($request); //On va relier la requête HTTP avec notre formulaire (Permettant à symfony de faire les vérifications nécessaire et hydrater l'objet User qui a été crée avec les nouvelles données)
        
        if ($form->isSubmitted() && $form->isValid()) { //SI les données du formulaire sont soumis et qu'elles sont validé
            $user = $form->getData(); //ALORS post va récupérer les données du formulaire
            $em->persist($user); //Puis on prépare la requête d'ajout
            $em->flush(); //Et on enregistre le tout dans la base de données

            return $this->redirectToRoute('show_user', [
            'id' => $user->getId(),
            ]); //Une fois que l'ajout est terminé, on renvoie à la fonction des lists des posts
        }

        return $this->render('security/editProfile.html.twig', [ //On envoie sur la page addPost qui contient le formulaire pour ajouter un nouveau post
            'formEditProfile' => $form->createView(), //Twig ne popuvant pas traiter directement de la logique métier, la méthode createView va permettre de créer un objet formulaire pour que Twig puisse accéder au champs et envoyé les données
            'edit' => $user->getId(),
        ]);
    }

    //Fonction pour afficher le profile d'un utilisateur
    #[Route('/user/{id}/show', name: 'show_user')]
    public function showUser(User $user, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy(['userOfPost' => $user], ['publicationDate' => 'DESC']);

        return $this->render('security/profile.html.twig', [
            'user' => $user,
            'post' => $post,
        ]);
    }

}
