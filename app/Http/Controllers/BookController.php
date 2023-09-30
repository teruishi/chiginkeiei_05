<?php


namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Validator;
use Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $books = Book::orderBy('created_at', 'asc')->paginate(3);
        $books = Book::where('user_id',Auth::user()->id)->orderBy('created_at', 'asc')->paginate(3);
        return view('books', [
            'books' => $books
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
           $validator = Validator::make($request->all(), [
        'item_name' => 'required|string|max:255',
        'item_number' => 'required|integer',
        'item_amount' => 'required|numeric',
        'published' => 'required|date',
    ]);

    // バリデーション:エラー
    if ($validator->fails()) {
        return redirect('/')
            ->withInput()
            ->withErrors($validator);
    }

    // ユーザー認証を確認し、ユーザーが認証されている場合にユーザーIDを取得
    if (Auth::check()) {
        $user_id = Auth::user()->id;
    } else {
        // ユーザーが認証されていない場合、エラーまたはリダイレクトを行うか、適切な処理を追加してください。
    }

    // Eloquentモデル
    $book = new Book;
    $book->item_name = $request->item_name;
    $book->item_number = $request->item_number;
    $book->item_amount = $request->item_amount;
    $book->published = $request->published;

    // ユーザーIDを設定
    $book->user_id = $user_id;

    $book->save();

    return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit($book_id){
        $books = Book::where('user_id',Auth::user()->id)->find($book_id);
        return view('booksedit', ['book' => $books]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        //バリデーション
         $validator = Validator::make($request->all(), [
             'id' => 'required',
             'item_name' => 'required|min:3|max:255',
             'item_number' => 'required|min:1|max:3',
             'item_amount' => 'required|max:6',
             'published' => 'required',
        ]);
        //バリデーション:エラー
         if ($validator->fails()) {
             return redirect('/booksedit/'.$request->id)
                 ->withInput()
                 ->withErrors($validator);
        }
        
        //データ更新
        // $books = Book::find($request->id);
        $books->item_name   = $request->item_name;
        $books->item_number = $request->item_number;
        $books->item_amount = $request->item_amount;
        $books->published   = $request->published;
        $books->save();
        $books = Book::where('user_id',Auth::user()->id)->find($request->id);
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();       //追加
        return redirect('/');  //追加
    }
    public function __construct()
    {
        $this->middleware('auth');
    }
}
