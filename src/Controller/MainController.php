<?php

namespace App\Controller;

use DateTime;
use App\Entity\Like;
use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Category;
use App\Form\ContactType;
use App\Entity\NewsLetter;
use App\Form\NewsLetterType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function home(Request $request, PaginatorInterface $paginator): Response
    {
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
		$articles = $this->getDoctrine()->getRepository(Article::class)->findBy([
            'status' => 'P'
        ],['createAt' => 'DESC']);

        // Méthode findBy qui permet de récupérer les données
		$categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
		
        for ($i=0; $i < count($articles); $i++) { 
            $isLiked  = $this->getDoctrine()->getRepository(Like::class)->findBy([
                'user' => $this->getUser(),
                'article' => $articles[$i]->getId()
            ]);

            if ($isLiked) {
                $articles[$i]->alreadyLike = true;
            } else {
                $articles[$i]->alreadyLike = false;
            }
        }

        $articles = $paginator->paginate(
            $articles, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('main/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/categories/{id}", name="show_category")
     */
    public function showArticles(Request $request, ? Category $category, PaginatorInterface $paginator): Response
    {
        if ($category) {

            $articles = $category->getArticles()->getValues();

            for ($i=0; $i < count($articles); $i++) { 
                $isLiked  = $this->getDoctrine()->getRepository(Like::class)->findBy([
                    'user' => $this->getUser(),
                    'article' => $articles[$i]->getId()
                    ],['createAt' => 'DESC']);
    
                if ($isLiked) {
                    $articles[$i]->alreadyLike = true;
                } else {
                    $articles[$i]->alreadyLike = false;
                }
            }

            $articles = $paginator->paginate(
                $articles, // Requête contenant les données à paginer
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                6 // Nombre de résultats par page
            );

        } else {

            return $this->redirectToRoute('app_home');
        }
        
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('main/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/", name="app_newsletter", methods={"POST"})
     */
    public function newsletter(Request $request)
    {
        $newsletter = new NewsLetter();

		// Créer le formulaire NewsLetter
        $form = $this->createForm(NewsLetterType::class, $newsletter);
        // Nous récupérons les données
		$form->handleRequest($request);

        // Nous vérifions si le formulaire a été soumis et si les données sont valides
		if (!empty($form->get('email')->getData()) && is_string($form->get('email')->getData())) {
            $newsletter->setEmail($form->get('email')->getData());
            $em = $this->getDoctrine()->getManager();
            // On hydrate notre instance $newsletter
			$em->persist($newsletter);
            //On écrit en base de données
			$em->flush();

            $this->addFlash('success', 'Inscription à la newsletter réussis !');
            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('error', 'Erreur lors de l\'inscription à la newsletter.');
        return $this->redirectToRoute('app_home');
    }
    
    /**
     * @Route("/projet", name="app_projet")
     */
    public function projet(): Response
    {
        return $this->render('main/projet.html.twig');
    }
   
    /**
     * @Route("/equipe", name="app_equipe")
     */
    public function equipe(): Response
    {
        return $this->render('main/equipe.html.twig');
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact_post(Request $request, MailerInterface $mailer)
    {
		// Nous créons l'instance de "Contact"
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        // Nous récupérons les données
		$form->handleRequest($request);
		// Nous vérifions si le formulaire a été soumis et si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // On hydrate notre instance $contact
			$em->persist($contact);
            // On écrit en base de données
			$em->flush();

            $email = (new TemplatedEmail())
                // On attribue l'expéditeur
				->from("florent.f21@orange.fr")
                // On attribue le destinataire
				->to("florent.f21@orange.fr")
                // On attribue le sujet
				->subject('Nouveau message - Page Contact')
                // On crée le texte avec la vue
				->htmlTemplate('emails/contact_email.html.twig')
                ->context([
                    'contact' => $contact,
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé avec succès, la réponse se fera dans nos meilleurs délais.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('main/contact.html.twig', [
            'contact' => $contact,
            'contactForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/conditions", name="app_mentions")
     */
    public function conditions(): Response
    {
        return $this->render('main/mentions.html.twig');
    }

    /**
     * @Route("/likes/{id}", name="add_likes")
     */
    public function addLikes($id, Article $article)
    {
		// EntityManager
        $em = $this->getDoctrine()->getManager();
        
        // Si l'article n'existe pas
		if (!$article) {
            throw new NotFoundHttpException('Article non trouvé');
        }

		// Obtenir l'utilisateur
        $user = $this->getUser();

        // Chercher si l'article fait partie des favoris de l'utilisateur
		$alreadyLike = $em->getRepository(Like::class)->findOneBy([
            'user' => $user,
            'article' => $article
        ]);

        // Mettre l'article dans les favoris ou afficher un message d'erreur
		if ($alreadyLike) {
            $this->addFlash('error', 'Vous avez déjà cet article en favoris !');
            return $this->redirectToRoute('app_home');
        } else {
            // Nous créons l'instance de "Like"
			$like = new Like();
            // Hydrate notre "like" avec l'article courant
			$like->setArticle($article);
            // Hydrate notre "like" avec l'utilisateur courant
			$like->setUser($user);
            // Hydrate notre "like" avec la date et l'heure courants
			$like->setCreateAt(new DateTime());
            // On hydrate notre instance $like
			$em->persist($like);
            // On écrit en base de données
			$em->flush();

            $this->addFlash('success', 'Article ajouté aux favoris !');
            return $this->redirectToRoute('app_home');
        }
    }

    /**
     * @Route("/likes/remove/{id}", name="del_likes")
     */
    public function removeLikes($id, Article $article)
    {
        // EntityManager
		$em = $this->getDoctrine()->getManager();
        
        // Si l'article n'existe pas
		if (!$article) {
            throw new NotFoundHttpException('Article non trouvé');
        }

		// Obtenir l'utilisateur
        $user = $this->getUser();

        // Chercher si l'article fait partie des favoris de l'utilisateur
		$alreadyLike = $em->getRepository(Like::class)->findOneBy([
            'user' => $user,
            'article' => $article
        ]);

		// supprimer l'article des favoris ou afficher un message d'erreur
        if ($alreadyLike) {
            $em->remove($alreadyLike);
            $em->flush();

            $this->addFlash('success', 'Article retiré de vos favoris !');
            return $this->redirectToRoute('app_home');
        } else {
            $this->addFlash('error', 'Cet article n\'est pas dans vos favoris !');
            return $this->redirectToRoute('app_home');
        }
    }

}