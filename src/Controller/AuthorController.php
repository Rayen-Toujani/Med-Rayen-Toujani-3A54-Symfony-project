<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\MinmaxType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{

    
        
    #[Route('/author', name: 'app_author')]
    public function index(AuthorRepository $repo): Response
    {
        $tab = $repo->findAll();
        return $this->render('author/index.html.twig', [
            'tab' => $tab,
        ]);
    }

    #[Route(name:'byemail')]
     function Nom(AuthorRepository $repo){
    
         $tab=$repo->OrderAutByEmail();
         return $this->render('author/index.html.twig', [
             'tab' => $tab,
         ]);
        }

        

    #[Route('/addauthor', name: 'addauthor')]
    public function addauthor(ManagerRegistry $managerRegistry): Response
    {
        $x = $managerRegistry->getManager();
        $author = new Author ();
        $author->setUsername("3a54new");
        $author->setEmail("3a54new@esprit.tn");
        $x->persist($author);
        $x->flush();
        return new Response(" great add");
    }

    #[Route('/addformauthor', name: 'addformauthor')]
    public function addformauthor(ManagerRegistry $managerRegistry, Request $req): Response
    {
        $x = $managerRegistry->getManager();
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $x->persist($author);
            $x->flush();

            return $this->redirectToRoute('app_author');
        }
        return $this->renderForm('author/add.html.twig', [
            'f' => $form
        ]);
    }

    #[Route('/editauthor/{id}', name: 'editauthor')]
    public function editauthor($id, AuthorRepository $authorRepository, ManagerRegistry $managerRegistry, Request $req): Response
    {
        //var_dump($id) . die();
        $x = $managerRegistry->getManager();
        $dataid = $authorRepository->find($id);
        // var_dump($dataid) . die();
        $form = $this->createForm(AuthorType::class, $dataid);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $x->persist($dataid);
            $x->flush();
            return $this->redirectToRoute('app_author');
        }
        return $this->renderForm('author/add.html.twig', [
            'f' => $form
        ]);
    }

    #[Route('/deleteauthor/{id}', name: 'deleteauthor')]
    public function deleteauthor($id, ManagerRegistry $managerRegistry, AuthorRepository $authorRepository): Response
    {
        $em = $managerRegistry->getManager();
        $dataid = $authorRepository->find($id);
        $em->remove($dataid);
        $em->flush();
        return $this->redirectToRoute('app_author');
    }


    

    



        #[Route('/listminmax', name: 'listminmax')]
    #[Route('/minmax', name: 'minmax')]
    public function listBooksByAuthorBookCountRange(Request $request, AuthorRepository $authorRepository): Response
    {
        $form = $this->createForm(MinmaxType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $min = $data['Min'];
            $max = $data['Max'];

            $authors = $authorRepository->SearchAuthorminmax($min, $max);
            return $this->render('author/listminmax.html.twig', [
                'tab' => $authors,
            ]);
        }

        return $this->renderForm('author/minmax.html.twig', [
            'f' => $form,
        ]);
    }


    #[Route('/DeleteDQL', name:'DD')]
        function DeleteDQL(AuthorRepository $repo){
            $repo->DeleteAuthorwith0books();
            return $this->redirectToRoute('app_author');
        }
    

}
