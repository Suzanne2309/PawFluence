<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\request;

final class PostController extends AbstractController
{
    //Fonction pour lister les posts
    #[Route('/post', name: 'app_post')] //On crée la route vers la fonction
    public function index(PostRepository $postRepository): Response
    {
        //Le tableau $posts va contenir les résultats de recherche des posts par titre de post
        $posts = $postRepository->findBy([], ['postTitle' => 'ASC']);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    //Fonction pour ajouter un post
    #[Route('/post/add', name: 'add_post')] //On crée la route vers la fonction
    public function add_editPost(Post $post = null, request $request, PostRepository $postRepository, entityManagerInterface $em): Response
    {

        if(!$post) { //Si la variable de post n'est pas existante
            $post = new Post(); //Alors on crée un nouveau object Post
        }

        $form = $this->createForm(PostType::class, $post); //On crée un nouveau formulaire post avec le fichier PostType  

        $form->handleRequest($request); //On va relier la requête HTTP avec notre formulaire (Permettant à symfony de faire les vérifications nécessaire et hydrater l'objet Post qui a été crée avec les nouvelles données)
        
        if ($form->isSubmitted() && $form->isValid()) { //SI les données du formulaire sont soumis et qu'elles sont validé
            $post = $form->getData(); //ALORS post va récupérer les données du formulaire
            $em = persist($post); //Puis on prépare la requête d'ajout
            $em = flush(); //Et on enregistre le tout dans la base de données

            return $this->redirectToRoute('app_post'); //Une fois que l'ajout est terminé, on renvoie à la fonction des lists des posts
        }

        return $this->render('post/addPost.html.twig', [ //On envoie sur la page addPost qui contient le formulaire pour ajouter un nouveau post
            'formAddPost' => $form->createView(), //Twig ne popuvant pas traiter directement de la logique métier, la méthode createView va permettre de créer un objet formulaire pour que Twig puisse accéder au champs et envoyé les données
        ]);
    }

    //Fonction pour afficher le détail d'un post
    #[Route('/post/{id}/show', name: 'show_post')]
    public function showPost(Post $post): Response
    {
        return $this->render('post/showPost.html.twig', [
            'post' => $post,
        ]);
    }
}
