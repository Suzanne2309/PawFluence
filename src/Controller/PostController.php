<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PostController extends AbstractController
{
    //Section de POST

    //Fonction pour lister les posts et les commentaires
    #[Route('/post', name: 'app_post')] //On crée la route vers la fonction
    public function index(PostRepository $postRepository): Response
    {
        //Le tableau $posts va contenir les résultats de recherche des posts par titre de post
        $posts = $postRepository->findBy([], ['postTitle' => 'ASC']);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    //Fonction pour ajouter un post et editer un poste spécifique
    #[Route('/post/add', name: 'add_post')] //On crée la route vers la fonction
    public function addPost(Request $request, PostRepository $postRepository, EntityManagerInterface $em): Response
    {

        $post = new Post(); //On crée un nouveau object Post
        
        $form = $this->createForm(PostType::class, $post); //On crée un nouveau formulaire post avec le fichier PostType  

        $form->handleRequest($request); //On va relier la requête HTTP avec notre formulaire (Permettant à symfony de faire les vérifications nécessaire et hydrater l'objet Post qui a été crée avec les nouvelles données)
        
        if ($form->isSubmitted() && $form->isValid()) { //SI les données du formulaire sont soumis et qu'elles sont validé
            $post = $form->getData(); //ALORS post va récupérer les données du formulaire
            $post->setUserOfPost($this->getUser()); //On va injecter à post l'utilisateur actuel comme l'utilisateur relié au post. On automatise donc l'ajout d'utilisateur pour le formulaire d'ajout de Post.
            $em->persist($post); //Puis on prépare la requête d'ajout
            $em->flush(); //Et on enregistre le tout dans la base de données

            return $this->redirectToRoute('app_post'); //Une fois que l'ajout est terminé, on renvoie à la fonction des lists des posts
        }

        return $this->render('post/addPost.html.twig', [ //On envoie sur la page addPost qui contient le formulaire pour ajouter un nouveau post
            'formAddPost' => $form->createView(), //Twig ne popuvant pas traiter directement de la logique métier, la méthode createView va permettre de créer un objet formulaire pour que Twig puisse accéder au champs et envoyé les données
        ]);
    }

    #[Route('/post/{id}/edit', name: 'edit_post')] //On crée la route vers la fonction
    #[IsGranted('edit', 'post')] //Si l'URL contient l'action edit et l'objet post alors on va vérifier si l'utilisateur et le même que celui du post
    public function editPost(Post $post = null, Request $request, PostRepository $postRepository, EntityManagerInterface $em): Response
    {   
        if ($post->getUserOfPost() !== $this->getUser()) { //Si On fait appel à un post existant, on va comparer l'utilisateur du post et l'utilisateur de la sesion. Si les deux ne coincide pas,
            // throw $this->createAccessDeniedException(); Alors on donne accées pour éxecuter le reste de l'action
            /* var_dump($post->getUserOfPost(), $this->getUser()); die(); */
            $this->addFlash(
                'notice',
                'Vous n`avez pas le droit d`editer ce post !'
            );
            return $this->redirectToRoute('app_post');
        }

        $form = $this->createForm(PostType::class, $post); //On crée un nouveau formulaire post avec le fichier PostType  

        $form->handleRequest($request); //On va relier la requête HTTP avec notre formulaire (Permettant à symfony de faire les vérifications nécessaire et hydrater l'objet Post qui a été crée avec les nouvelles données)
        
        if ($form->isSubmitted() && $form->isValid()) { //SI les données du formulaire sont soumis et qu'elles sont validé
            $post = $form->getData(); //ALORS post va récupérer les données du formulaire
            $post->setUserOfPost($this->getUser()); //On va injecter à post l'utilisateur actuel comme l'utilisateur relié au post. On automatise donc l'ajout d'utilisateur pour le formulaire d'ajout de Post.
            $em->persist($post); //Puis on prépare la requête d'ajout
            $em->flush(); //Et on enregistre le tout dans la base de données

            return $this->redirectToRoute('app_post'); //Une fois que l'ajout est terminé, on renvoie à la fonction des lists des posts
        }

        return $this->render('post/addPost.html.twig', [ //On envoie sur la page addPost qui contient le formulaire pour ajouter un nouveau post
            'formAddPost' => $form->createView(), //Twig ne popuvant pas traiter directement de la logique métier, la méthode createView va permettre de créer un objet formulaire pour que Twig puisse accéder au champs et envoyé les données
            'edit' => $post->getId(),
        ]);
    }

    //Fonction pour supprimer un post
    #[Route('/post/{id}/delete', name: 'delete_post')]
    #[IsGranted('edit', 'post')] //Si l'URL contient l'action delete et l'objet post alors on va vérifier si l'utilisateur et le même que celui du post
    public function deletePost(Post $post, EntityManagerInterface $em)
    { 
        if ($post->getUserOfPost() !== $this->getUser()) { //Si On clique sur le bouton de suppression, on va comparer l'utilisateur du post et l'utilisateur de la sesion. Si les deux ne coincide pas,
            $this->addFlash(
                'notice',
                'Vous n`avez pas le droit d`editer ce post !'
            );
            return $this->redirectToRoute('app_post');
        }
        $user = $this->getUser();
        if (!$user) { //S'il n'y a pas d'utilisateur connecté 
            return $this->redirectToRoute('app_login'); //Alors on renvoit l'utilisateur vers la page de log-in
        }

        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('app_post');
    }

    //Fonction pour afficher le détail d'un post
    #[Route('/post/{id}/show', name: 'show_post')]
    public function showPost(Post $post, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([], ['commentTitle' => 'ASC']);

        return $this->render('post/showPost.html.twig', [
            'post' => $post,
            'comments' => $comments,
        ]);
    }

    //Fonction pour ajouter un comentaire et editer un commentaire spécifique
    #[Route('/comment/{id}/add', name: 'add_comment')] //On crée la route vers la fonction
    public function addComment(Post $post, Request $request, CommentRepository $commentRepository, EntityManagerInterface $em): Response
    {

        $comment = new Comment(); //On crée un nouveau object Post
        
        $form = $this->createForm(CommentType::class, $comment); //On crée un nouveau formulaire post avec le fichier PostType  

        $form->handleRequest($request); //On va relier la requête HTTP avec notre formulaire (Permettant à symfony de faire les vérifications nécessaire et hydrater l'objet Post qui a été crée avec les nouvelles données)
        
        if ($form->isSubmitted() && $form->isValid()) { //SI les données du formulaire sont soumis et qu'elles sont validé
            $comment = $form->getData(); //ALORS post va récupérer les données du formulaire
            $comment->setCommentUser($this->getUser()); //On va injecter à post l'utilisateur actuel comme l'utilisateur relié au post. On automatise donc l'ajout d'utilisateur pour le formulaire d'ajout de Post.
            $comment->setRealtedPost($post);
            $em->persist($comment); //Puis on prépare la requête d'ajout
            $em->flush(); //Et on enregistre le tout dans la base de données

            return $this->redirectToRoute('app_post'); //Une fois que l'ajout est terminé, on renvoie à la fonction des lists des posts
        }

        return $this->render('post/addComment.html.twig', [ //On envoie sur la page addPost qui contient le formulaire pour ajouter un nouveau post
            'formAddComment' => $form->createView(), //Twig ne popuvant pas traiter directement de la logique métier, la méthode createView va permettre de créer un objet formulaire pour que Twig puisse accéder au champs et envoyé les données
        ]);
    }

    //Fonction pour editer un poste spécifique
    #[Route('/comment/{id}/edit', name: 'edit_comment')] //On crée la route vers la fonction
    #[IsGranted('edit', 'comment')] //Si l'URL contient l'action edit et l'objet post alors on va vérifier si l'utilisateur et le même que celui du post
    public function editComment(Comment $comment = null, Request $request, CommentRepository $commentRepository, EntityManagerInterface $em): Response
    {   
        if ($comment->getCommentUser() !== $this->getUser()) { //Si On fait appel à un post existant, on va comparer l'utilisateur du post et l'utilisateur de la sesion. Si les deux ne coincide pas,
            // throw $this->createAccessDeniedException(); Alors on donne accées pour éxecuter le reste de l'action
            /* var_dump($post->getUserOfPost(), $this->getUser()); die(); */
            $this->addFlash(
                'notice',
                'Vous n`avez pas le droit d`editer ce commentaire !'
            );
            return $this->redirectToRoute('app_post');
        }

        $form = $this->createForm(CommentType::class, $comment); //On crée un nouveau formulaire post avec le fichier PostType  

        $form->handleRequest($request); //On va relier la requête HTTP avec notre formulaire (Permettant à symfony de faire les vérifications nécessaire et hydrater l'objet Post qui a été crée avec les nouvelles données)
        
        if ($form->isSubmitted() && $form->isValid()) { //SI les données du formulaire sont soumis et qu'elles sont validé
            $comment = $form->getData(); //ALORS post va récupérer les données du formulaire
            $em->persist($comment); //Puis on prépare la requête d'ajout
            $em->flush(); //Et on enregistre le tout dans la base de données

            return $this->redirectToRoute('app_post'); //Une fois que l'ajout est terminé, on renvoie à la fonction des lists des posts
        }

        return $this->render('post/addComment.html.twig', [ //On envoie sur la page addPost qui contient le formulaire pour ajouter un nouveau post
            'formAddComment' => $form->createView(), //Twig ne popuvant pas traiter directement de la logique métier, la méthode createView va permettre de créer un objet formulaire pour que Twig puisse accéder au champs et envoyé les données
            'edit' => $comment->getId(),
        ]);
    }

    //Fonction pour supprimer un commentaire
    #[Route('/comment/{id}/delete', name: 'delete_comment')]
    #[IsGranted('delete', 'comment')] //Si l'URL contient l'action delete et l'objet post alors on va vérifier si l'utilisateur et le même que celui du post
    public function deleteComment(Comment $comment, EntityManagerInterface $em)
    { 
        if ($comment->getCommentUser() !== $this->getUser()) { //Si On clique sur le bouton de suppression, on va comparer l'utilisateur du post et l'utilisateur de la sesion. Si les deux ne coincide pas,
            $this->addFlash(
                'notice',
                'Vous n`avez pas le droit de supprimer ce commentaire !'
            );
            return $this->redirectToRoute('app_post');
        }
        $user = $this->getUser();
        if (!$user) { //S'il n'y a pas d'utilisateur connecté 
            return $this->redirectToRoute('app_login'); //Alors on renvoit l'utilisateur vers la page de log-in
        }

        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('app_post');
    }
}
