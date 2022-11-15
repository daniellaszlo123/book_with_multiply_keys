<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Copy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopyController extends Controller
{
    //
    public function index(){
        $copies =  Copy::all();
        return $copies;
    }
    
    public function show($id)
    {
        $copies = Copy::find($id);
        return $copies;
    }
    public function destroy($id)
    {
        Copy::find($id)->delete();
    }
    public function store(Request $request)
    {
        $copy = new Copy();
        $copy->book_id = $request->book_id;
        $copy->hardcovered = $request->hardcovered;
        $copy->publication = $request->validate([
            'publication'=>'required|min:1000|max:9999'
        ]);
        $copy->status = 0;
        $copy->save(); 
    }

    public function update(Request $request, $id)
    {
        //a book_id ne változzon! mert akkor már másik példányról van szó
        $copy = Copy::find($id);
        $copy->hardcovered = $request->hardcovered;
        $copy->publication = $request->publication;
        $copy->status = $request->status;
        $copy->save();        
    }

    public function copies_pieces($title)
    {	
        $copies = Book::with('copy_c')->where('title','=', $title)->count();
        return $copies;
    }

    //view-k:

    public function newView()
    {
        //új rekord(ok) rögzítése
        $books = Book::all();
        return view('copy.new', ['books' => $books]);
    }

    public function editView($id)
    {
        $books = Book::all();
        $copy = Copy::find($id);
        return view('copy.edit', ['books' => $books, 'copy' => $copy]);
    }

    public function listView()
    {
        $copies = Copy::all();
        //copy mappában list blade
        return view('copy.list', ['copies' => $copies]);
    }

    public function yearCopies($year, $author, $title)
    {
        $copies = DB::table('copies as c')
        ->join('books as b', 'c.book_id', '=', 'b.book_id')
        ->where('c.publication', '=', $year)
        ->where('b.author', '=', $author)
        ->where('b.title', '=', $title)
        ->get();

        return $copies;
    }

    public function hardCopies()
    {
        $copies = DB::table('copies as c')->select('b.author', 'b.title')
        ->join('books as b', 'c.book_id', '=', 'b.book_id')
        ->where('c.hardcovered','=', '1')
        ->get();

        return $copies;
    }

    public function yearCopiesStorage($year, $author, $title)
    {
        $copies = DB::table('copies as c')
        ->join('books as b', 'c.book_id', '=', 'b.book_id')
        ->where('c.publication', '=', $year)
        ->where('b.author', '=', $author)
        ->where('b.title', '=', $title)
        ->where('c.status','=', '0')
        ->count();

        return $copies;
    }

    public function lendingDataDB($book_id)
    {
        $lending = DB::table('lendings as l')
        ->where('l.copy_id','=', $book_id)
        ->get();

        return $lending;
    }

    public function lendingDataWITH($book_id)
    {
        $lending = Copy::with('copy_c')
        ->where('book_id','=', $book_id)
        ->get();

        return $lending;
    }
}
