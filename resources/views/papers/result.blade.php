@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Paper Information</div>
                <div class="card-body">
                    <h4>{{$result->paper->name}}</h4>
                    Paper Description: <b>{{$result->paper->des}}</b><br>
                    Number of Questions Questions:<b> {{ $result->paper->numQ }}</b><br>
                    Total Marks: <b>{{$result->paper->total}}</b>
                </div>
                <div class="card-body">
                    <div>
                        <h5>Following are the questions:</h5>
                        <hr>
                        {{-- View/Edit them here: <a href="{{$paper->id}}/edit/"> Questions</a> --}}
                        <ul>
                            @foreach ($result->paper->questions as $question)
                            <li>{{$question->name}}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    Your Parsed Answer
                </div>
                <div class="card-body">
                    <p>{{$result->scanned_answer}}</p>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">Images Submitted</div>
                @foreach ($result->images as $image)
                <div class="card-body">
                    <img src="{{"data:image/png;base64,".$image->scanned}}" alt="Paper Image" style="max-width:100%">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection