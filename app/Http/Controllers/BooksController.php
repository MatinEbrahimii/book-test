<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Book;
use App\BookReview;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;

class BooksController extends Controller
{
    public function getCollection(Request $request)
    {
        $books_query = Book::getCollectionQuery($request);

        $sorted_books = Book::sortCollection($books_query, $request);

        $paginated_books = $this->paginateBooksCollection($sorted_books, $request);

        return new BookCollection($paginated_books);
    }

    public function post(PostBookRequest $request)
    {
        $book = Book::store($request);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    public function postReview(Book $book, PostBookReviewRequest $request)
    {
        $book_review = BookReview::store($book, $request);

        return (new BookReviewResource($book_review))
            ->response()
            ->setStatusCode(201);
    }

    public function paginateBooksCollection($sorted_books, $request)
    {
        return $sorted_books->paginate(
            $perPage = null,
            $columns = ['*'],
            $pageName = 'page',
            $page = $request->input('page', 1)
        );
    }
}
