<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class BookReview extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment',
        'review',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public static function store($book, $request)
    {
        $book_review = $book->reviews()->create([
            'review' => $request->review,
            'comment' => $request->comment,
            'user_id' => Auth::user()->id
        ]);

        if (!$book_review->exists) {
            throw new \Exception('Book review could not be created');
        }

        return $book_review;
    }
}
