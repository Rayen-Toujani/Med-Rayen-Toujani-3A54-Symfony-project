<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\RefbookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    
    


    #[Route('/book', name: 'app_book')]
    public function index(BookRepository $repo,Request $request): Response
    {   
        $book = $repo->findBy(['published' => true]);
        $bkk =$repo->ShowBookOrderByAuthor();
        $nbr=$repo->NbBookCategory();
        

        $form=$this->createForm(RefbookType::class,);     
   $form->handleRequest($request);
   if ($form->isSubmitted()){
   $ref = $form->get('ref')->getData();
   
   $book = $repo->findBookByRef($ref);
   } 
         if ($book === null) {
            throw $this->createNotFoundException('Aucun Livre.');
        }

        $publishedCount = $repo->count(['published' => true]);
        $unpublishedCount = $repo->count(['published' => false]);

        
        return $this->renderForm('book/index.html.twig', [
            
            'book' => $book,
            'f' =>$form,
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
            'bkk'=>$bkk,
            'nbr' => $nbr
        ]);
    }

    #[Route('/byauthor',name:'byauthor')]
     function Mail(BookRepository $repo,Request $request){

        $form=$this->createForm(RefbookType::class,);     
   $form->handleRequest($request);
   $nbr=$repo->NbBookCategory();
   if ($form->isSubmitted()){
   $ref = $form->get('ref')->getData();
   
   
   $book = $repo->findBookByRef($ref);
   }

   $publishedCount = $repo->count(['published' => true]);
        $unpublishedCount = $repo->count(['published' => false]);
    
         $book=$repo->ShowBookOrderByAuthor();
         return $this->renderForm('book/index.html.twig', [
             'book' => $book,
             'f' =>$form,
             'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
            'nbr' => $nbr


         ]);
        }

    #[Route('/addformbook', name: 'addformbook')]
    public function addformbook(Request $req,ManagerRegistry $managerRegistry): Response
    {
        $em=$managerRegistry->getManager();
        $book= new Book();
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid() ){
            $author = $book->getAuthor();
            if ($book->isPublished()) { 
                $author->setNbBooks($author->getNbBooks() + 1);
            } 
         $em->persist($author);
        $em->persist($book);
        $em->flush();
        return $this->redirectToRoute('app_book');
        }
        return $this->renderForm('book/add.html.twig', [
            'f'=>$form
        ]);
    }

    

    #[Route('/editbook/{id}', name: 'editbook')]
    public function editbook($id,BookRepository $bookRepository,ManagerRegistry $managerRegistry,Request $req): Response
    {
        //var_dump($id).die();
        $em=$managerRegistry->getManager();
        $dataid=$bookRepository->find($id);
        //var_dump($dataid).die();
        $form=$this->createForm(BookType::class,$dataid);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()){
            $em->persist($dataid);
            $em->flush();
            return $this->redirectToRoute('app_book');

        }
        return $this->renderForm('book/edit.html.twig', [
            'd' => $form
        ]);
    }





    #[Route('/deletebook/{id}', name: 'deletebook')]
    public function deletebook($id,ManagerRegistry $managerRegistry,BookRepository $authorRepository): Response
    {
        //var_dump($id).die();
        $em=$managerRegistry->getManager();
        $dataid=$authorRepository->find($id);
        $author = $dataid->getAuthor();

        // Decrement the author's nbBooks
        $author->setNbBooks($author->getNbBooks() - 1);
        //var_dump($dataid).die();
        $em->remove($dataid);
        $em->flush();
        return $this->redirectToRoute('app_book');
    }




    #[Route('/showbyidauthor/{ref}', name: 'showbyidauthor')]
    public function showidbyauthor($ref,BookRepository $BookRepository,ManagerRegistry $managerRegistry): Response
    {
        $em=$managerRegistry->getManager();
        $book = $BookRepository->find($ref);
        $em->persist($book);
        $em->flush();
        return $this->render('book/showbyidauthor.html.twig', [
            'book' => $book,
        ]);
    }





    #[Route('/book/show/{id}', name: 'show_book')]
    public function showBook($id,ManagerRegistry $managerRegistry): Response
    {
        // Récupérez le livre depuis la base de données en utilisant Doctrine
        $entityManager = $managerRegistry->getManager();
        $bookRepository = $entityManager->getRepository(Book::class);
        $book = $bookRepository->find($id);

        if ($book === null) {
            throw $this->createNotFoundException('Le livre n\'existe pas.');
        }

        // Utilisez la méthode render pour afficher un template avec les détails du livre
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    

    #[Route('/listbookb42023', name: 'listbookb42023')]
    public function findBooksb42023morethan35(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBooksb42023morethan35();

        return $this->render('book/listbookb42023.html.twig', [
            'books' => $books,
        ]);
    }


    #[Route('/updateWilliamShakespeareBooks', name: 'updateWilliamShakespeareBooks')]
    public function updateCategoryForWilliamShakespeareBooks(BookRepository $bookRepository, ManagerRegistry $managerRegistry)
    {
        $entityManager =$managerRegistry->getManager();

        $williamShakespeareBooks = $bookRepository->updateWilliamShakespeareBooks();

        foreach ($williamShakespeareBooks as $book) {
            $book->setCategory('Romance');
            $entityManager->persist($book);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_book');
    }

    #[Route('/showpubbetdates')]
    function showTitleBook(BookRepository $repo){
        $titles=$repo->findBookByPublicationDate();
        return $this->render('book/showpubbetdates.html.twig', [
            'book' => $titles,
        ]);
    }



    



   
}
