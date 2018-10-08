@extends('layouts.app')

@section('content')
<div class="container">

           @if(Session::has('message'))
        <span class="labe label-succes">{{Session::get('message')}}</span>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="col-sm-5 col-md-3 pull-right">
                    <div class="row">&nbsp</div>

                    <form action="{{ url('search-refund') }}" method="GET" class="navbar-form" role="search">
                    <div class="input-group">
                      <input type="text" class="form-control" placeholder="Search" name="q" id="srch-term">
                      <div class="input-group-btn">
                        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                      </div>
                    </div>
                    </form>
                    </div>
        <div class="col-md-15">
        <div class="row">&nbsp</div>
        
            <table class="table">
            <thead>
            <tr>
              <th>No</th>
              <th>Name</th>
              <th>Email</th>
              <th>Total</th>
              <th>Tanggal</th>
            
            </tr>
           
            
            </thead>
            <tbody>


    <?php $no = $usern->firstItem()  ; ?>
    <?php $dt = count($usern) ?>
    @if ($dt > 0)
    @foreach ($usern as $usersn)
    <tr>
        <td>{{$no ++}}</td>
        <td>{{$usersn->name}}</td>
        <td>{{$usersn->email}}</td>
        <td>Rp. <?php echo number_format($usersn->total) ?></td>
        <td>{{$usersn->created_at}}</td>
    </tr>
    @endforeach

    @else
      <div style="font-weight: bold;"> Data Tidak Ditemukan</div>
    @endif
        </tbody>
        </table>
     {!! $usern->render() !!}

        </div>


                </div>

                <div class="panel-body">
                    <div id="totalpost"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection