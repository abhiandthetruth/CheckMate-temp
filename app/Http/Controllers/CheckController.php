<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

global $smscr;
define("MINEX", 0.60);
define("MINLEN", 0.40);
define("MINMID", 0.80);

use Illuminate\Http\Request;

function spchk($str)
{
    $k = str_replace(' ', '+', $str);
    return $str;
}
function llemm($str)
{
    /*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/
    return $str;
}

function klem($key)
{
    /*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/
    return $key;
}

function listconv($str, $question)
{
    if ($question->type == 3 or $question->type == 4 or $question->type == 5) {
        $stra = preg_split("/[0-9]+[)\]]/", $str);

        $p = count($stra);
        for ($i = 0; $i < $p - 1; $i++) $stra[$i] = $stra[$i + 1];

        // dd($stra);
        return $stra;
    } else {
        $stra[0] = $str;
        return $stra;
    }
}

function analyz($refo, $refl, $studo, $studl, $key, $question)
{
    if ($question->type == 3) $studo = listsort($refo, $studo, $studl);
    $marks = listeval($refo, $refl, $studo, $studl, $key, $question);
    return $marks;
}

function listsort($refo, $studo, $studl)
{
    $i = 0;
    global $smscr;
    $stdr = [];
    foreach ($refo as $point) {
        $max = smchk($point, $studo[0]);
        $imax = 0;
        $j = 0;
        foreach ($studo as $p) {
            $s = smchk($point, $p);
            if ($s > $max) {
                $max = $s;
                $imax = $j;
            }
            $j++;
        }
        $stdr[$i] = $studo[$imax];
        $smscr[$i] = $max;
        $i++;
    }
    return $stdr;
}

function listeval($refo, $refl, $studo, $studl, $key, $question)
{
    if ($question->type != 3) simscr($refo, $studo);
    $n = ($question->numC);
    $m = $question->marks;
    $me = ($m) / ($n);
    $scr = [];
    for ($i = 0; $i < ($question->numP); $i++) {

        if ($question->evaltype == 0) {
            $scr[$i] = ex($refo[$i], $studo[$i], $me, $i);
        } else if ($question->evaltype == 1) {
            $scr[$i] = exnonsyn($refo[$i], $studo[$i], $me, $i);
        } else if ($question->evaltype == 2) {
            $scr[$i] = len($refo[$i], $refl[$i], $studo[$i], $studl[$i], $key, $me, $i);
        } else if ($question->evaltype == 3) {
            $scr[$i] = nonlen($refo[$i], $refl[$i], $studo[$i], $studl[$i], $key, $me, $i);
        }
    }
    $marks = 0;
    arsort($scr);
    for ($i = 0; $i < $n; $i++)  $marks = $marks + $scr[$i];
    return $marks;
}
function simscr($refo, $studo)
{
    $i = 0;
    global $smscr;
    foreach ($refo as $point) {
        $smscr[$i] = smchk($point, $studo[$i]);
        $i++;
    }
}

function smchk($str1, $str2)
{
    $m = str_replace(' ', '+', $str1);
    $k = str_replace(' ', '+', $str2);
    //  dd($str2);
    $l = "https://twinword-text-similarity-v1.p.rapidapi.com/similarity/?text1=" . $m . "&text2=" . $k;

    $response = \Unirest\Request::get(
        "$l",
        array(
            "X-RapidAPI-Key" => "0d4592612amsh2e69e9506883a6cp128a49jsn81286e4ac028"
        )
    );
    $sim = $response->body->similarity;
    // dd($response);
    return $sim;
}


function ex($re, $stu, $me, $i)
{
    global $smscr;
    if ($smscr[$i] == 1) return $me;
    else return 0;
}

function exnonsyn($ref, $stud, $me, $i)
{
    global $smscr;
    if ($smscr[$i] > MINEX) {
        return (1 - (1 - $smscr[$i]) / (1 - MINEX)) * $me;
    } else return 0;
}

function len($refo, $refl, $studo, $studl, $key, $me, $i)
{
    global $smscr;
    if ($smscr[$i] > MINLEN) {
        $me1 = $me;
        $e = exkey($studl, $key, $me, $i);
        $me1 = $me - $e;
        if ($me1 > 0.7 * $me) {
            $k = sentanlz($studl, $key, $me, $me1, $i);
            $me1 = $me1 - $k;
        }
        //if ($me1 > 0.3 * $me){ $p = reskey($studl,$key,$me,$me1,$i); $me1 = $me1 - $p;}
        return $me1;
    } else {
        return 0;
    }
}

function nonlen($refo, $refl, $studo, $studl, $key, $me, $i)
{
    global $smscr;
    if ($smscr[$i] >= MINMID) {
        $me1 = $me;
        $e = exkey($studl, $key, $me, $i);
        $me1 = $me - $e;
        if ($me1 > 0.7 * $me) {
            $k = sentanlz($studl, $key, $me, $me1, $i);
            $me1 = $me1 - $k;
        }
        //if ($me1 > 0.3 * $me){ $p = reskey($studl,$key,$me,$me1,$i); $me1 = $me1 - $p;}
        return $me1;
    } else if (MINLEN < $smscr[$i] and $smscr[$i] < MINMID) {
        $me1 = (1 - (MINMID - $smscr[$i]) / (MINMID - MINLEN)) * $me;
        $e = exkey($studl, $key, $me1, $i);
        $me1 = $me1 - $e;
        if ($me1 > 0.7 * $me) {
            $k = sentanlz($studl, $key, $me, $me1);
            $me1 = $me1 - $k;
        }
        //if ($me1 > 0.3 * $me){ $p = reskey($studl,$key,$me,$me1,$i); $me1 = $me1 - $p;}
        return $me1;
    } else return 0;
}

function exkey($studl, $key, $m, $i)
{
    $v = 0;
    $t = $m;
    foreach ($key as $x) {
        if ($x->type == 2) break;
        if ($x->type == 0) continue;
        if ($v == $i) {
            $str = $x->answer;
            $stra = preg_split("/;/", strtolower($str));
            $n = count($stra);
            $mar = $x->mark;
            $mara =  preg_split("/;/", strtolower($mar));
            $j = 0;
            foreach ($stra as $p) {
                $pos = strpos(strtolower($studl), $p);
                //  echo $p;
                if ($pos == FAlSE) {
                    //   echo $p;
                    if ($t < 0.5 * $m) $t = $t - $m / (($n) * ($n));
                    else
                        $t = $t - $m / ($n);
                    //         echo $m." ";
                    /*$t = $t - $mara[$j];
                if ($t<=0){$t=0; break;}*/
                }
                $j++;
            }
            //1   dd();
            break;
        }
        $v++;
    }
    if ($t < 0) $t = 0;
    return ($m - $t);
}

function reskey($studl, $key, $m, $m1, $i)
{
    $v = 0;
    $t = $m1;
    foreach ($key as $x) {
        if ($x->type == 0 or $x->type == 1 or $x->type == 2) continue;
        if ($v == $i) {
            $str = $x->answer;
            $stra = preg_split("/;/", strtolower($str));
            $n = count($stra);
            $mar = $x->mark;
            $mara =  preg_split("/;/", strtolower($mar));
            $j = 0;
            foreach ($stra as $p) {
                $pos = strpos(strtolower($studl), $p);
                if ($pos != FAlSE) {
                    /*if ($t<0.5*$m) $t = $t - $m/(($n)*($n));
                else $t = $t - $m/($n);*/
                    $t = $t - $mara[$j];
                    if ($t <= 0) {
                        $t = 0;
                        break;
                    }
                }
                $j++;
            }
            break;
        }
        $v++;
    }
    return ($m - $t);
}

function sentanlz($studl, $key, $me, $me1)
{
    /*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/
    return 0;
}

use GoogleCloudVision\GoogleCloudVision;
use GoogleCloudVision\Request\AnnotateImageRequest;



class CheckController extends Controller
{


    public function checkmate($id, request $request)
    {
        $rollnumber = '';
        if (Auth::user()->type) {
            $rollnumber = request('roll');
        } else $rollnumber = Auth::user()->Roll;
        $i = "";
        $paper = \App\Paper::find($id);
        $result = new \App\Result;
        $imageObjs = array();
        foreach ($request->file('filename') as $file) {
            $image = base64_encode(file_get_contents($file));
            //prepare request
            $request = new AnnotateImageRequest();
            $request->setImage($image);
            $request->setFeature("DOCUMENT_TEXT_DETECTION");
            $gcvRequest = new GoogleCloudVision([$request],  env('GOOGLE_CLOUD_KEY'));
            //send annotation request
            $response = $gcvRequest->annotate();
            $j = $response->responses[0]->textAnnotations[0]->description;
            $j = $j . " ";
            $i = $i . $j;
            //Save to database
            $imageObj = new \App\image;
            $imageObj->paper_id = $paper->id;
            $imageObj->scanned = $image;
            array_push($imageObjs, $imageObj);
        }
        $i = str_replace("|", "", $i);
        $i = str_replace("??", "Q", $i);
        $i = trim($i);
        $result->scanned_answer = $i;
        $answers = preg_split("/((q|Q)[0-9]+(:|;))|([0-9]+(:|;))/", $i);
        $answers = preg_replace('/\s+/', ' ', $answers);
        $questions = $paper->questions;
        $k = 0;
        $total = 0;
        $m = "";
        foreach ($questions as $question) {
            $keywords = $question->keywords;
            if ($answers[0] == NULL) $k = $k + 1;
            if ($question->sp == 0) {
                $ref = spchk($keywords[0]->answer);
                $stud = spchk($answers[$k]);
            }
            $refo = listconv($ref, $question);
            $studo = listconv($stud, $question);
            $refl = llemm($refo);
            $studl = llemm($studo);
            $key = klem($keywords);
            $marks = analyz($refo, $refl, $studo, $studl, $key, $question);
            $total += $marks;
            $m = $m . ',' . (string)$marks;
            $k++;
        }
        $student = \App\user::where('roll', '=', $rollnumber)->first();
        $result->marks = $m;
        $result->finalmarks = $total;
        $result->Sid = $student->id;
        $result->paper_id = $id;
        $result->paper_code = $paper->code;
        $result->name = $student->name;
        $result->save();
        foreach ($imageObjs as $imageObj) {
            $imageObj->result_id = $result->id;
            $imageObj->save();
        }
        session()->flash('msg', 'Paper is Checked successfully.');
        return back()->withInput();
    }



    /* Evaluations end */
}
