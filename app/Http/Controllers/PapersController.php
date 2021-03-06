<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PapersController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }


  public function index()
  {
  }

  public function create()
  {
    return view('papers.add');
  }

  public function add()
  {
    if (Auth::user()->type < 1) abort(403, 'Unauthorized action.');
    request()->validate([
      'code' => ['required', 'unique:papers', 'max:6'],
      'name' => ['required'],
      'totalQ' => ['required'],
      'des' => ['required'],
      'total' => ['required']
    ]);
    $paper = new \App\paper;
    $paper->name = request('name');
    $paper->des = request('des');
    $paper->numQ = request('totalQ');
    $paper->total = request('total');
    $paper->status = 0;
    $paper->code = request('code');
    $paper->user_id = Auth::user()->id;
    $paper->save();

    session()->flash('msg', 'Paper is added successfully.');
    return redirect('/home');
  }

  public function addQuestions($code)
  {
    $paper = \App\Paper::where('code', $code)->firstOrFail();

    return view("papers/questions", compact('paper'));
  }


  public function viewresult($id)
  {
    $paper = \App\Paper::find($id);
    $results = $paper->results;
    return view("papers/results", compact('paper', 'results'));
  }

  public function showresult($id)
  {
    $result = \App\result::findOrFail($id);
    if (Auth::user()->type == 0 && $result->iska->Roll != Auth::user()->Roll) abort(403);
    return view("papers/result", compact('result'));
  }

  public function SubmitQuestions($code)
  {

    $paper = \App\Paper::where('code', $code)->firstOrFail();
    $validateRules = [];
    for ($i = 1; $i <= $paper->numQ; $i++) {
      $validateRules['name' . strval($i)] = 'required';
      $validateRules['nump' . strval($i)] = 'required';
      $validateRules['numc' . strval($i)] = 'required';
      $validateRules['type' . strval($i)] = 'required';
      $validateRules['typec' . strval($i)] = 'required';
      $validateRules['marks' . strval($i)] = 'required';
    }
    request()->validate($validateRules);
    for ($i = 1; $i <= $paper->numQ; $i++) {
      //Adding Questions
      $question = new \App\question;
      $question->name = request('name' . strval($i));
      $question->paper_id = $paper->id;
      $question->number = $i;
      $question->numP = request('nump' . strval($i));
      $question->numC = request('numc' . strval($i));
      $question->type = request('type' . strval($i));
      $question->evaltype = request('typec' . strval($i));
      $question->marks = request('marks' . strval($i));
      $question->sp = 0;
      $question->save();

      //Adding Answer
      $answer = new \App\keyword;
      $answer->type = 0;
      $answer->question_id = $question->id;
      $answer->mark = 0;
      $answer->answer = request('answer' . strval($i));
      $answer->save();

      //Adding Exact Keywords withmarks
      if (request()->has('exact') && request()->has('emarks')) {
        $exact = request('exact' . strval($i));
        $exactm = request('emarks' . strval($i));
        $exactarray = explode(",", $exact);
        $exactmarks = explode(",", $exactm);
        $j = 0;
        foreach ($exactarray as $e) {
          $exact = new \App\keyword;
          $exact->type = 1;
          $exact->question_id = $question->id;
          $exact->mark = $exactmarks[$j];
          $exact->answer = $e;
          $exact->save();
          $j++;
        }
      }

      // Adding similar words
      if (request()->has('syn') && request()->has('smarks')) {
        $sym = request('syn' . strval($i));
        $symm = request('smarks' . strval($i));
        $symarray = explode(",", $sym);
        $symmarks = explode(",", $symm);
        $j = 0;
        foreach ($symarray as $e) {
          $sym = new \App\keyword;
          $sym->type = 2;
          $sym->question_id = $question->id;
          $sym->mark = $symmarks[$j];
          $sym->answer = $e;
          $sym->save();
          $j++;
        }
      }


      //Adding res Keywords withmarks
      if (request()->has('Res') && request()->has('rmarks')) {
        $res = request('Res' . strval($i));
        $resm = request('rmarks' . strval($i));
        $resarray = explode(",", $res);
        $resmarks = explode(",", $resm);
        $j = 0;
        foreach ($resarray as $e) {

          $res = new \App\keyword;
          $res->type = 3;
          $res->question_id = $question->id;
          $res->mark = $resmarks[$j];
          $res->answer = $e;
          $res->save();
          $j++;
        }
      }
    }
    $paper->status = 1;
    $paper->save();
    return redirect('/home');
  }
}
