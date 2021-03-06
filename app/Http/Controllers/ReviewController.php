<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\User;
use App\Book;
use App\Review;
use App\Helpful;

class ReviewController extends Controller {
  //投稿レビュー一覧表示(検索結果も含める)
   public function index(Request $request) {
     $cond_statement = $request->cond_statement;
     if ($cond_statement != '') {
       $reviews = Review::whereHas('book', function($q) use ($cond_statement) {
         $q->where('title', 'like', '%'.$cond_statement.'%')->orwhere('author', 'like',  '%'.$cond_statement.'%')->orderByDesc('updated_at');
       })->paginate(8);
     } else {
       $reviews = Review::with('book')->orderByDesc('updated_at')->paginate(8);
     }
     return view('review.home', ['reviews' => $reviews, 'cond_statement' => $cond_statement]);
   }

   //ジャンル別レビュー一覧表示
   public function business_economy() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', 'ビジネス・経済');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function society_politics() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', '社会・政治');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function investment_finance() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', '投資・金融');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function nature_environment() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', '自然・環境');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function history_geography() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', '歴史・地理');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function culture_thought() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', '文化・思想');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function education_selfdevelopment() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', '教育・自己啓発');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function science_technology() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', '科学・テクノロジー');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function travel() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', '旅行・紀行');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function sports_outdoor() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', 'スポーツ・アウトドア');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   public function other() {
     $reviews = Review::whereHas('book', function($q) {
       $q->where('genre', 'その他');
     })->orderByDesc('updated_at')->paginate(8);
     return view('review.home', ['reviews' => $reviews]);
   }

   //レビュー内容の表示
   public function content(Request $request) {
     $review = Review::find($request->id);
     if (empty($review)) {
          abort(404);
     }
    // 本番環境ではデバッグバーは削除、またはコメントアウトしておかないと
    // エラーが発生する
    // \Debugbar::info($review);
    return view('review.content',['review' => $review]);
   }


  //レビュー作成画面表示
   public function create() {
     return view('review.create');
   }

   //レビュー入力内容確認
   public function confirm(Request $request) {
     $this->validate($request, Book::$rules);
     $this->validate($request, Review::$rules);
     $form = $request->all();

     if (isset($form['image'])) {
       $this->validate($request, Book::$image_rules);

       $path = Storage::disk('s3')->putFile('/', $form['image'], 'public');
       $read_path = Storage::disk('s3')->url($path);
       return view('review.confirm', compact('form', 'path', 'read_path'));

       //ローカル環境での処理
       // $temp_path = $request->file('image')->store('public/temp');
       // $read_temp_path = str_replace('public/', 'storage/', $temp_path);
       //
       // $data = array(
       //   'temp_path' => $temp_path,
       //   'read_temp_path' => $read_temp_path,
       // );
       // $request->session()->put('data', $data);
       // // \Debugbar::info($form['image'], $data);
       // return view('review.confirm', compact('form', 'data'));
     }

     unset($form['_token']);  //_tokeの削除は必須
     unset($form['image']);
     //compact関数:Controllerからviewへ変数を渡す時に、$を省略できる
     return view('review.confirm', compact('form'));
   }

   //レビュー保存・投稿
   public function store(Request $request) {
     $form = $request->all();
     $book = new Book;
     $review = new Review;

     if (isset($form['read_path'])) {
       $book->image_path = $form['read_path'];

     // ローカル環境での処理
     // if ($request->session()->has('data')) {
        // $data = $request->session()->pull('data');
        // $temp_path = $data['temp_path'];
        // $read_temp_path = $data['read_temp_path'];
        // // unset($data['read_temp_path']);
        //
        // $filename = str_replace('public/temp/', '', $temp_path);
        // $storage_path = 'public/bookimage/'.$filename;
        //
        // Storage::move($temp_path, $storage_path);
        //
        // $read_path = str_replace('public/', 'storage/', $storage_path);
        // $book->image_path = $read_path;

      } else {
        $book->image_path = null;
      }
     unset($form['_token']);
     $book->title = $form['title'];
     $book->genre = $form['genre'];
     $book->author = $form['author'];
     $book->publisher = $form['publisher'];
     $book->save();

     $review->user_id = Auth::id(); //Auth::id():ログイン中のユーザーのID
     $review->book_id = $book->id;
     $review->review = $form['review'];
     $review->practice = $form['practice'];
     $review->save();
     // \Debugbar::info($book->image_path);
     return redirect('/');
   }

   //レビュー作成画面に戻る(+ s3の画像も削除)
   public function back(Request $request) {
    $form = $request->form;
    if (isset($request->path)) {
      $path = $request->path;
      // dd($path);
      Storage::disk('s3')->delete($path);
      unset($form['_token']);
      // dd($form);
    }
    return back()->withInput($form);
   }


   //マイレビュー表示画面の表示
   public function myreview_show(Request $request) {
    $reviews = Review::where('user_id', Auth::id())->paginate(8);
    // \Debugbar::info($reviews);
    return view('review.myreview', ['reviews' => $reviews]);
   }

   //マイレビュー編集画面の表示
   public function edit(Request $request) {
     // $review = Review::where('user_id', Auth::id())->where('id', $request->id)->first();
     $review = Review::find($request->id);
     // \Debugbar::info($review);
     return view('review.edit', ['review_form' => $review]);
   }

   //マイレビュー編集
   public function update(Request $request) {
     $this->validate($request, Book::$rules);
     $this->validate($request, Review::$rules);
     $review = Review::find($request->id);
     $book = Book::find($review->book_id);
     $form = $request->all();

     if (isset($form['image'])) {
       $this->validate($request, Book::$image_rules);

       $path = Storage::disk('s3')->putFile('/', $form['image'], 'public');
       $book->image_path = Storage::disk('s3')->url($path);

       // ローカル環境での処理
       // $storage_path = $request->file('image')->store('public/bookimage');
       // $read_path = str_replace('public/', 'storage/', $storage_path);
       // $book->image_path = $read_path;

     } elseif ($request->input('remove')) {
         $book->image_path = null;
     } else {
         $form['image_path'] = $book->image_path;
         $book->image_path = $form['image_path'];
     }

     unset($form['_token']);
     unset($form['image']);
     unset($form['remove']);

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
     return redirect('/myreview');
     }

     //マイレビュー削除
   public function delete(Request $request) {
     $review = Review::find($request->id);
     $review->delete();
     $review->book->delete();
     return redirect('/myreview');
   }

}
