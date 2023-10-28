<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    
    
    function findBookByRef($ref){
    
        return $this->createQueryBuilder('auth')
            ->Where('auth.ref=:ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();
    }

    public function ShowBookOrderByAuthor()
    {
        return $this->createQueryBuilder('auth')
        ->orderBy('auth.author', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findBooksb42023morethan35()
    {
        return $this->createQueryBuilder('book')
        ->join('book.author', 'author')
        ->Where('author.nb_books > 35')
        ->andWhere('book.publicationdate < :date')
        ->groupBy('author.nb_books')
        ->setParameter('date', new \DateTime('2023-01-01'))
        ->getQuery()
        ->getResult();
    }


    public function updateWilliamShakespeareBooks()
    {
        return $this->createQueryBuilder('book')
            ->innerJoin('book.author', 'author')
            ->where('author.username = :authorName')
            ->setParameter('authorName', 'William Shakespeare')
            ->getQuery()
            ->getResult();
    }


    function NbBookCategory(){
        $em=$this->getEntityManager();
        return $em
        ->createQuery('SELECT count(b) from App\Entity\Book b WHERE b.category=:category')
        ->setParameter('category','Science-Fiction')
        ->getSingleScalarResult();
    }


    function findBookByPublicationDate(){
        $em=$this->getEntityManager();
        return $em->createQuery('SELECT b from App\Entity\Book b WHERE 
        b.publicationdate BETWEEN ?1 AND ?2')
        ->setParameter(1,'2014-01-01')
        ->setParameter(2,'2018-12-31')->getResult();
    }


    


//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
