<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Book;
use App\Review;

class ReviewController extends Controller {
   public function index() {
     return view('review.home');
   }


   public function add() {
     return view('review.create');
   }

   public function confirm(Request $request) {
     $this->validate($request, Book::$rules);
     $this->validate($request, Review::$rules);
     // if (!null == ($request->input('image'))) {
     //   $this->validate($request, Book::$image_rules);
     // }
     $form = $request->all();
     unset($form['_token']);  //_tokeの削除は必須
     return view('review.confirm', ['form' => $form]);
   }


   public function create(Request $request) {
     $form = $request->all();
     $book = new Book;
     $review = new Review;
     // if(!null == ($form['image'])) {
     //    $path = $request->file('image')->store('public/image');
     //    $book->image_path = basename($path);
     //  } else {
     //    $book->image_path = null;
     //  }
     unset($form['image']);
     unset($form['_token']);
     $book->image_path = null;
     $book->title = $form['title'];
     $book->genre = $form['genre'];
     $book->author = $form['author'];
     $book->publisher = $form['publisher'];
     $book->save();

     $review->user_id = Auth::id();
     $review->book_id = $book->id;
     $review->review = $form['review'];
     $review->practice = $form['practice'];
     $review->save();
     return redirect('/');
   }
}
