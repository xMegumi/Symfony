<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/article/liste', name: 'app_article_liste')]
    public function showList(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('article/liste.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    #[Route('/article/creer', name: 'app_article_create')]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $article = new Article();
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {
                // Obtenez le nom du fichier (vous pouvez aussi générer un nom unique si nécessaire)
                $brochureFilename = uniqid().'.'.$brochureFile->guessExtension();

                // Déplacez le fichier dans le répertoire défini dans le paramètre
                $brochureFile->move(
                    $this->getParameter('brochure_directory'),
                    $brochureFilename
                );

                // Assigner le nom du fichier à l'entité (par exemple, à l'attribut `brochureFilename`)
                $article->setBrochureFilename($brochureFilename);
            }
                
            $this->addFlash(
                'success',
                "Votre article vient d'être enregistré !"
            );

            $entityManager->persist($article);
            $entityManager->flush();
    
            
    
            return $this->redirectToRoute('app_article_liste');
        }
    
        return $this->render('article/creer.html.twig', [
            'controller_name' => 'ArticleController',
            'titre' => 'Article',
            'form' => $form
        ]);
    }
    

    #[Route('/article/modifier/{id}', name: 'app_article_modifier')]
public function modifier(int $id, EntityManagerInterface $entityManager, Request $request): Response
{
    $article = $entityManager->getRepository(Article::class)->find($id);

    if(!$article) {
        throw new NotFoundHttpException("Aucun article n'a été trouvé");
    }

    // Créer le formulaire avec l'article à modifier
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $brochureFile = $form->get('brochure')->getData();
        if ($brochureFile) {
            // Traitez le fichier, par exemple, déplacez-le dans un dossier
            $newFilename = uniqid() . '.' . $brochureFile->guessExtension();
            $brochureFile->move(
                $this->getParameter('brochure_directory'), // Dossier de stockage
                $newFilename
            );
            $article->setBrochureFilename($newFilename); // Enregistrez le nom du fichier dans l'article
            $entityManager->flush();  // Sauvegarde les modifications
            $this->addFlash('success', 'L\'article a bien été modifié.');
        }
        
        return $this->redirectToRoute('app_article_liste');
    }
    

    return $this->render('article/modifier.html.twig', [
        'form' => $form->createView(),
        'article' => $article,
    ]);
}


    #[Route('/article/supprimer/{id}', name: 'app_article_supprimer')]
    public function supprimer(int $id, EntityManagerInterface $entityManager): Response 
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if(!$article) {
            throw new NotFoundHttpException("Aucun article n'a été trouvé");
        }

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_article_liste');
    }
}
