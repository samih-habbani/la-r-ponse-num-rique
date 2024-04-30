<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Newsletter;
use App\Entity\Category;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry; 

class ArticleController extends AbstractController
{
    #[Route('/article/{id}', name: 'article')]
    public function index(Request $request,
        EntityManagerInterface $entityManager,
        PersistenceManagerRegistry $doctrine,
        PaginatorInterface $paginator): Response
    {

        $idArticle = $request->get("id");
        $article = $doctrine->getRepository(Article::class)->find($idArticle);

        // récupérer les commentaires valides pour cet article
        $comments = $entityManager->getRepository(Comment::class)->findAllValidComments($idArticle);
        
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $comment->setArticle($article);
            $comment->setDate(new \DateTime);
            $comment->setUser($this->getUser());
            $comment->setIsValid(false);

            $entityManager->persist($comment);
            $entityManager->flush();

            // réinitialiser le formulaire
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);

            $this->addFlash('secondary', 'Votre commentaire sera validé prochainement pour publication !');
            
        }

        if (!empty($request->request->get('newsletter'))) {
            
            $email = strtolower($request->request->get('newsletter'));
            $frequence = $request->request->get('frequence');

            $entityManager = $doctrine->getManager();
            $newsletter = new Newsletter();
            $newsletter->setEmail($email);
            $newsletter->setFrequence($frequence);

            $entityManager->persist($newsletter);
            $entityManager->flush();

            $this->addFlash('secondary', 'Vous avez bien été inscrit à notre newsletter !');

        }


        $relatedArticles = $doctrine->getRepository(Article::class)->findRelatedArticles($article->getCategory(), $idArticle);

        $relatedArticles = $paginator->paginate(
            $relatedArticles, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('article/index.html.twig', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'categorySelected' => $article->getCategory(),
            'form_comment' => $form,
            'comments' => $comments
        ]);
    }
}
