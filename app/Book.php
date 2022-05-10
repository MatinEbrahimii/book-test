<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\MicroServices\Filters\BookFilters;

class Book extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'isbn',
        'title',
        'description',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public static function store($request)
    {
        DB::beginTransaction();
        try {
            $book = self::create([
                'title' => $request->title,
                'description' => $request->description,
                'isbn' => $request->isbn
            ]);

            if (!$book->exists) {
                throw new \Exception('Book could not be created');
            }

            $book->authors()->attach($request->authors);
            $book->reviews()->create([
                'review' => 0,
                'comment' => ' ',
                'user_id' => Auth::user()->id
            ]);
            return $book;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function getCollectionQuery($request)
    {
        $query = self::query()->with('authors');

        if ($request->has('title')) {
            $query->filter('title', $request->input('title'));
        } else if ($request->has('authors')) {
            $query->filter('authors', $request->input('authors'));
        }
        return $query;
    }

    public static function sortCollection($query, $request)
    {
        if (self::existsTitle($request)) {
            $query = $query->orderBy('title');
        } elseif (self::existsTitleAndDesc($request)) {
            $query = $query->orderByDesc('title');
        } elseif (self::exsitsAvgReview($request)) {
            $query = $query->withCount(['reviews as reviews_avg' => function ($query) {
                $query->select(DB::raw('avg(review)'));
            }])
                ->orderBy('reviews_avg');
        } elseif (self::exsitsAvgReviewAndDesc($request)) {
            $query = $query->withCount(['reviews as reviews_avg' => function ($query) {
                $query->select(DB::raw('avg(review)'));
            }])
                ->orderByDesc('reviews_avg');
        }
        return $query;
    }

    public static function existsTitle($request)
    {
        return $request->has('sortColumn') &&
            $request->input('sortColumn') == 'title' &&
            !$request->has('sortDirection');
    }

    public static function exsitsAvgReview($request)
    {
        return $request->has('sortColumn') &&
            $request->has('sortColumn') == 'avg_review' &&
            !$request->has('sortDirection');
    }

    public static function existsTitleAndDesc($request)
    {
        return $request->has('sortColumn') &&
            $request->input('sortColumn') == 'title' &&
            $request->has('sortDirection') &&
            $request->input('sortDirection') == 'DESC';
    }

    public static function exsitsAvgReviewAndDesc($request)
    {
        return   $request->has('sortColumn') &&
            $request->has('sortColumn') == 'avg_review' &&
            $request->has('sortDirection') &&
            $request->input('sortDirection') == 'DESC';
    }

    public function scopeFilter($query, $filter, $filter_value)
    {
        $book_filter = new BookFilters();
        return $book_filter->apply($query, $filter, $filter_value);
    }
}
