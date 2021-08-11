<?php

namespace App\Controller;

use DateTime;
use App\Entity\NewsLetter;
use App\Entity\User;
use App\Service\Mail;
use App\Entity\Article;
use App\Entity\Like;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreateAt(new DateTime());
            $article->setUser($this->getUser());
            
            $image = $form->get('image')->getData();

            $fichier = md5(uniqid()).'.'.$image->guessExtension();

            $image->move(
                $this->getParameter('images_directory'),
                $fichier
            );

            $article->setImage($fichier);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            
            $newsletterBdd = $this->getDoctrine()->getRepository(NewsLetter::class)->findAll();

            foreach ($newsletterBdd as $newsLetter) {
                
                $mail = new Mail();
                
                $content_title ="Bonjour cher Abonné(e),<br><br>".$article->getTitle();
                $content_subTitle ="<br>".$article->getSubTitle();
                $content_category ="<br>".$article->getCategory();

                $mail->send($newsLetter->getEmail(), 'Chers Abonnés', 'Nouvel article du Collectif Taliesin !', $content_title, $content_subTitle, $content_category);  
            }
            
            $this->addFlash('success', 'Article créé avec succès');
            
            return $this->redirectToRoute('app_home');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET", "POST"})
     */
    public function show(Article $article, Request $request)
    {
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'article' => $article,
            'status' => "V"
        ],['createAt' => 'desc']);

        $latest_article = $this->getDoctrine()->getRepository(Article::class)->findBy([], [
            'createAt' => 'desc',
        ],5);

        $isLiked  = $this->getDoctrine()->getRepository(Like::class)->findBy([
            'user' => $this->getUser(),
            'article' => $article->getId()
        ]);
        
        if ($isLiked) {
            $article->alreadyLike = true;
        } else {
            $article->alreadyLike = false;
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setStatus('W');
            $comment->setUser($this->getUser());
            $comment->setCreateAt(new DateTime('now'));

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($comment);
            $doctrine->flush();

            $this->addFlash('success', 'Commentaire posté avec succès. Cependant le commentaire doit être approuvé par un modérateur pour être visible');
            return $this->render('article/show.html.twig', [
                'form' => $form->createView(),
                'article' => $article,
                'comments' => $comments,
                'latest_article' => $latest_article
            ]);
        }

        return $this->render('article/show.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'comments' => $comments,
            'latest_article' => $latest_article
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MODO")
     */
    public function edit(Request $request, Article $article, $id): Response
    {
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'article' => $article,
        ],['createAt' => 'desc']);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Article édité avec succès');
            return $this->redirectToRoute('article_show', [ 'id' => $id ]);
        }

        return $this->render('article/edit.html.twig', [
            'comments' => $comments,
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MODO")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
           
            $image = $article->getImage();
           
            unlink($this->getParameter('images_directory').'/'.$image);
            
            $entityManager->remove($article);
            $entityManager->flush();
        }
        
        $this->addFlash('success', 'Article supprimé avec succès');
        return $this->redirectToRoute('app_home');
    }
}
