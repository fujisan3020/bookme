@extends('layouts.index')

@section('title', 'マイレビュー')

@section('content')
           <p><br></p>
           <div class="text-sm-center">
             <h1>マイレビュー</h1>
           </div>

           @php
             $i = 1
           @endphp

           @foreach($reviews as $review)
             <div class="container">
               <br>
               <h1 class="bg-danger text-sm-center">Review Number : {{ $i }}</h1>
               <div class="container myreviews">
                <fieldse class="form-group">
                  <label for="title">本のタイトル</label>
                  <div>
                    <p class="review-value">{{ $review->book->title }}</p>
                  </div>
                </fieldset>
                <fieldse class="form-group">
                  <label for="genre">本のジャンル</label>
                  <div>
                    <p class="review-value">{{ $review->book->genre }}</p>
                  </div>
                </fieldset>
                <fieldse class="form-group">
                  <label for="author">本の著者</label>
                  <div>
                    <p class="review-value">{{ $review->book->author }}</p>
                  </div>
                </fieldset>
                <fieldse class="form-group">
                  <label for="publisher">本の出版社</label>
                  <div>
                    <p class="review-value">{{ $review->book->publisher }}</p>
                  </div>
                </fieldset>
                <fieldse class="form-group">
                  <label for="image">本の画像</label>
                  <div>
                    @if(isset($review->book->image_path))
                    <img class="content_image" src="{{ asset($review->book->image_path) }}" alt="本の画像" width="300" height="400">
                    @else
                    <p>なし</p>
                    @endif
                  </div>
                </fieldset>
                <fieldse class="form-group">
                  <label for="review">レビュー</label>
                  <div>
                    <p class="review-value">{{ $review->review }}</p>
                  </div>
                </fieldset>
                <fieldse class="form-group">
                  <label for="practice">実践できること</label>
                  <div>
                    <p class="review-value">{{ $review->practice }}</p>
                  </div>
                </fieldset>
                <fieldse class="form-group">
                  <label for="name">レビュワー</label>
                  <div>
                    <p class="review-value">{{ $review->user->name }}</p>
                  </div>
                </fieldset>
                <p>更新日時:
                @if ($review->updated_at > $review->book->updated_at)
                  {{ $review->updated_at }}
                @else
                  {{ $review->book->updated_at }}
                </p>
                @endif
                <div>
                  <a class="btn btn-success" href="{{ action('ReviewController@edit', ['id' => $review->id]) }}" role="button">レビュー編集</a>
                  <a class="btn btn-warning review-delete" href="{{ action('ReviewController@delete', ['id' => $review->id]) }}" role="button">レビュー削除</a>
                </div>
              </div>
              <hr>
           </div>

            @php
              $i++
            @endphp

          @endforeach
          <p class="pagination">{{ $reviews->links() }}</p>
@endsection
