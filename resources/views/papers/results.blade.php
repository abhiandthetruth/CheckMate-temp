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




          @endif

          <div style="padding:20px 0;">
            <h4>
              Results For the paper {{ $paper->name }}
            </h4>
          </div>


          <table class="table">
            <thead>
              <tr>
                <th scope="col">Sr No.</th>
                <th scope="col">Student Name</th>
                <th scope="col">Roll No.</th>
                <th scope="col">Marks per Question</th>
                <th scope="col">Total Marks</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <?php $i = 1 ?>
            <tbody>
              @foreach ($results as $result)

              <tr>
                <th scope="row"><?php echo $i ?></th>
                <td>{{$result->name}}</td>
                <td>{{$result->iska->Roll}}</td>
                <td>{{substr($result->marks,1)}}</td>
                <td>{{$result->finalmarks}}</td>
                <td><a href="/result/{{$result->id}}">View Submission</a></td>
              </tr>
              <?php $i++ ?>
              @endforeach




            </tbody>
          </table>




          @else



          @endif
        </div>

      </div>
    </div>
  </div>
</div>
@endsection