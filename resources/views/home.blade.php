@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">

        <div class="card-header">Dashboard</div>
        <div class="card-body">
          @if(Auth::user()->type)
          <button type="button" onClick="window.location='papers/create';" class="btn btn-primary">Add new
            Paper</button>
          <hr>
          @if(session('msg'))



          <div class="alert alert-success" style="margin-top:10px;">
            <strong>Success!</strong> {{session('msg')}}

          </div>
          @endif

          <div style="padding:20px 0;">
            <h4>
              Your Papers:
            </h4>
          </div>


          <table class="table">
            <thead>
              <tr>
                <th scope="col">Sr No.</th>
                <th scope="col">Name</th>
                <th scope="col">Created at</th>
                <th scope="col">Number of Questions</th>
                <th scope="col">Total Marks</th>
                <th scope="col">Status</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <?php $i = 1 ?>
            <tbody>
              @foreach ($papersgiven as $paper)

              <tr>
                <th scope="row"><?php echo $i ?></th>
                <td><a href="papers/{{$paper->code}}">{{$paper->name}}</a></td>
                <td>{{$paper->created_at->format('d/m/y')}}</td>
                <td>{{$paper->numQ}}</td>
                <td>{{$paper->total}}</td>
                <td>{{$paper->status}}</td>
                <td><a href="papers/{{$paper->id}}/result">View Result</a></td>

              </tr>
              <?php $i++ ?>
              @endforeach




            </tbody>
          </table>




          @else
          <div class="">
            <h3>Submit Answers:</h3>
          </div>
          <hr>
          <div class="form-row" style="margin:0px 0px;">
            <div class="form-group">
              <label for="code">Enter Paper Code</label>
              <input type="text" name="code" id="code">
              <button class="btn btn-primary"
                onclick="document.location.href = `/papers/${document.getElementById('code').value}`">Go</button>
            </div>
          </div>
          <br>
          <div>
            <h3>Your Results:</h3>
          </div>
          <hr>







          <table class="table">
            <thead>
              <tr>
                <th scope="col">Sr No.</th>
                <th scope="col">Paper Code</th>
                <th scope="col">Final Marks </th>
                <th scope="col">Question Wise Marks</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <?php $i = 1 ?>
            <tbody>
              @foreach ($results as $result)

              <tr>
                <th scope="row"><?php echo $i ?></th>
                <td> {{ $result-> paper_code }}</td>
                <td> {{ $result-> finalmarks }}</td>
                <td> {{ substr($result->marks,1) }}</td>
                <td><a href="/result/{{$result->id}}">View Submission</a></td>
              </tr>
              <?php $i++ ?>
              @endforeach



              @endif
        </div>

      </div>
    </div>
  </div>
</div>
@endsection