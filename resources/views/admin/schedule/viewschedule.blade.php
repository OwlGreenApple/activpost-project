@extends('layouts.app')

@section('content')
<div class="container">

           @if(Session::has('message'))
        <span class="labe label-succes">{{Session::get('message')}}</span>
    @endif
<script type="text/javascript">
  $(document).ready(function(){
    $('.service-info').hide();
      $('.read-more').click(function(){
          $(this).siblings('.service-info').show();
          $(this).hide();
          $(this).parent().find('.read-less').show();
        })
      $('.read-less').click(function(){
        $(this).siblings('.service-info').hide();
        $(this).hide();
        $(this).parent().find('.read-more').show();
      })
  });
</script>
<style type="text/css">
  .read-less{
  display: none;
}
</style>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">

                    
                    <div class="col-sm-5 col-md-3 pull-right">
                    <div class="row">&nbsp</div>
                    <form action="{{ url('search-schedule') }}" method="GET" class="navbar-form" role="search">
                    <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" name="q" id="srch-term">
                    <div class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                    </div>
                    </div>
                    </form>
                    </div>
                    
                
                  <table class="table">
                    <thead>
                      <th width="50">No</th>
                      <th width="80">Image</th>
                      <th width="300">Description</th>
                      <th width="75">Status</th>
                      <th width="75">Publish Schedule</th>
                      <th width="75">Deleted Schedule</th>
                      <th width="75">Created</th>
                      <th>Action</th>
                    </thead>
                    <tbody>
                    <?php $no = $listschedule->firstItem()  ; ?>
                    @foreach($listschedule as $list)
                      <tr>
                        <td>{{$no++ }} </td>
                        @if ($list->image == '')
                        <td> - </td>
                        @else
                        <td><img src="{{$list->image}}" width="75" height="75"> </td>
                        @endif
                        
                        <td><?php $desc = strlen($list->description) ?>
                        @if ($desc >= 200)

                        <?= substr($list->description, 0,200) ?>
                        
                        <a href="#" class="read-more">Read More </a>
                        <div class="service-info">
                          <?= substr($list->description, 200,$desc) ?>

                        </div>
                        <a href="#" class="read-less">Read Less</a>

                        @else
                        {{$list->description}} 
                        @endif
                        </td>
                        <td>
                            @if ($list->status == 1)
                                Pending
                            @elseif ($list->status == 2)
                                Published
                            @elseif ($list->status == 3)
                                Deleted
                            @endif
                        </td>
                        <td>{{$list->publish_at}}</td>
                        <td>{{$list->deleted_at}}</td>
                        <td>{{$list->created_at}}</td>
                        <td>
                           <a href="{{action('Admin\ScheduleController@schedulaccount',['id'=>$list->id])}}" id="button-add" class="btn btn-success ls-modal3" >Account Schedule</a> 
                        </td>
                      </tr>
                    @endforeach
                    </tbody>
                  </table>

                  {!! $listschedule->render() !!}
        
                </div>
                <div class="panel-body">
                    <div id="totalpost"></div>
                </div>
            </div>
        </div>
    </div>
</div>

  <script language="javascript">
    //JS script
$('.ls-modal').on('click', function(e){
  e.preventDefault();
  $('#myModal').modal('show').find('.modal-body').load($(this).attr('href'));
});

$('.ls-modal2').on('click', function(e){
  e.preventDefault();
  $('#myModal2').modal('show').find('.modal-body').load($(this).attr('href'));
});

$('.ls-modal3').on('click', function(e){
  e.preventDefault();
  $('#myModal3').modal('show').find('.modal-body').load($(this).attr('href'));
});
    </script>


{{-- Add Modal --}}
<div class="modal fade" id="myModal" >
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modalMdTitle"> User Log</h4>
                  </div>
                  <div class="modal-body">
                      
                  </div>
              </div>
          </div>
        </div>
    <!--
       <div class="modal fade" id="modalMd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modalMdTitle"></h4>
                  </div>
                  <div class="modal-body">
                      <div class="modalError"></div>
                      <div id="modalMdContent"></div>
                  </div>
              </div>
          </div>
        </div>
    -->
    
        <!-- Edit modal -->
        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modalMdTitle"></h4>
                  </div>
                  <div class="modal-body">
                      <div class="modalError"></div>
                      <div id="modalMdContent"></div>
                  </div>
              </div>
          </div>
        </div>


        <!-- Edit modal max account -->
        <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modalMdTitle"></h4>
                  </div>
                  <div class="modal-body">
                      <div class="modalError"></div>
                      <div id="modalMdContent"></div>
                  </div>
              </div>
          </div>
        </div>         
      

        <div class="modal fade" id="add">
            <div class="modal-dialog">
                    <div class="modal-content">
                            <form id="addaccount" role="form" action="">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="uri" value="{{ url('account/chklogin') }}">
                                    <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Add Account</h4>
                                    </div>
                                    <div class="modal-body">
                                            {{-- Alert --}}
                                            <div id="successmsg" class="alert alert-success" style="display: none;">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                    <strong>Login Success!</strong>
                                            </div>
                                            <div class="form-group">
                                                    <label for="insta_username">Username</label>
                                                    <input type="text" class="form-control" name="insta_username" required="required" id="insta_username">
                                            </div>
                                            <div class="form-group">
                                                    <label for="insta_password">Password</label>
                                                    <input type="password" class="form-control" name="insta_password" required="required" id="insta_password">
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-home" id="button-process">Add</button>
                                    </div>
                            </form>
                    </div>
            </div>
        </div>
@endsection



