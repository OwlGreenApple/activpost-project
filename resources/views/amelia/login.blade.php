<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Activpost</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/images/fav-ico.png') }}">

    <!-- Scripts -->
    <!--<script src="{{ asset('/js/app.js') }}"></script> -->
    <script type="text/javascript" src="{{ asset('/js/jquery-1.11.3.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/matchheigth.js') }}"></script>
    <script src="{{ asset('/js/moment.min.js') }}"></script>
    <!--<script src="{{ asset('/js/moment-timezone.js') }}"></script>-->
    <script src="{{ asset('/js/moment-timezone-with-data-2010-2020.js') }}"></script>
    <!--<script src="{{ asset('/js/moment-timezone-with-data.js') }}"></script>-->
    <script src="{{ asset('/js/jquery.elevateZoom.min.js') }}"></script>
    <script src="{{ asset('/js/account.js') }}"></script>
    @if (Request::is('schedule-video/add') || Request::is('schedule-video/edit*'))
        <script src="{{ asset('/js/schedule-video.js') }}"></script>
    @endif
    {{-- Init --}}
        <script src="{{ asset('/pixie-new/pixie-integrate.js') }}" data-preload="true" data-path="{{ asset('/pixie-new') }}" ></script>
        <script src="{{ asset('/build/jquery.datetimepicker.full.js') }}"></script>
        <script src="{{ asset('/build/jquery.datetimepicker.full.min.js') }}"></script>
        <script src="{{ asset('/build/jquery.datetimepicker.min.js') }}"></script>
    <!--<script src="{{ asset('/js/bootstrap-datetimepicker.min.js') }}"></script>-->
    @if (Request::is('order') || Request::is('confir-payment') || Request::is('list-order') || Request::is('search-orders') || Request::is('orders') || Request::is('confirm-payment') || Request::is('prices'))
        <script src="{{ asset('/js/orderbuymore.js') }}"></script>
    @endif
    @if (Request::is('schedule') || Request::is('schedule/add') || Request::is('schedule/edit*') ||  Request::is('schedule/repost*') )
        <script src="{{ asset('/js/realtime.js') }}"></script>
    @endif
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('/js/jquery.tooltipster.min.js') }}"></script>
    <!-- emoji -->
    <script type="text/javascript">
        mainPathFolder = "<?php echo asset(''); ?>";
    </script>
    <script type="text/javascript" src="{{ asset('/emoji/js/prettify.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/emoji/js/emojionearea.js') }}"></script>
    <script src="{{ asset('DataTables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/datetime-moment.js') }}"></script>
    <?php 
      if(Auth::check()){
        if(Auth::user()->is_member_rico || Auth::user()->is_admin) { 
    ?>
      <script src="https://wchat.freshchat.com/js/widget.js"></script>
      <script>
        // Make sure fcWidget.init is included before setting these values
        // To set unique user id in your system when it is available
        window.fcWidget.setExternalId('<?php echo Auth::user()->id; ?>');
        // To set user name
        window.fcWidget.user.setFirstName('<?php echo Auth::user()->name; ?>');
        // To set user email
        window.fcWidget.user.setEmail('<?php echo Auth::user()->email; ?>');
      </script>
    <?php 
        } 
      }
    ?>

    <!-- Styles -->
    <!--<link href="{{ asset('/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">-->
    <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/tooltipster/tooltipster.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/tooltipster/tooltipster-noir.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <?php if(env('APP_PROJECT')=='Celebgramme') { ?>
      <link href="{{ asset('/css/main.css') }}" rel="stylesheet">
    <?php } else { ?>
      <link href="{{ asset('/css/amelia/main.css') }}" rel="stylesheet">
    <?php } ?>
    <link href="{{ asset('/css/amelia/sign-in.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('/build/jquery.datetimepicker.min.css') }}" rel="stylesheet">
    @if (Request::is('order') || Request::is('orders') || Request::is('prices'))
    <link href="{{ asset('/css/buyorder.css')}}" rel="stylesheet">
    @endif
    <!-- emoji -->
    <link href="{{ asset('/emoji/css/emojionearea.min.css') }}" rel="stylesheet"> 
    <link href="{{ asset('DataTables/DataTables/css/jquery.dataTables.min.css') }}" rel="stylesheet"></link>
    
    
    <style type="text/css">
        .navbar-brand {
          padding: 0px;
        }
        .navbar-brand>img {
          height: 100%;
          padding: 10px;
          width: auto;
        }
        @if(Request::is('schedule/add') || Request::is('schedule/edit*'))
        img {
          max-width: 100%;
        }
        @endif
    </style>
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <script type="text/javascript">
        $(window).on('load', function() { 
          $("#div-loading").hide();
        });   
        $(document).click(function(e) {
            var target = e.target;

            if (!$(target).is('.glyphicon-question-sign') && !$(target).parents().is('.glyphicon-question-sign')) {
                $('.glyphicon-question-sign').find(".hint").hide();
            }
            if (!$(target).is('.glyphicon-menu-down') && !$(target).parents().is('.glyphicon-menu-down')) {
                $('.glyphicon-menu-down').find(".hint").hide();
            }
        });
        $(document).ready(function() {
          
          
          $("body").tooltip({ selector: '[data-toggle=tooltip]' });
          $("#checkAll").click(function () {
              $(".check").prop('checked', $(this).prop('checked'));
          });
          $(".main-content").css("min-height",$(window).height()-121);
          $( window ).resize(function() {
            $(".main-content").css("min-height",$(window).height()-121);
          });

          /*Hint*/
          $('.tooltipPlugin').tooltipster({
              theme: 'tooltipster-noir',
              contentAsHTML: true,
              interactive:true,
          });

          $( "body" ).on( "click", ".glyphicon-menu-down", function(e) {
            $(this).find('.hint').slideToggle();
          });

          $( "body" ).on( "click", ".glyphicon-question-sign", function(e) {
            $(this).find('.hint').slideToggle();
          });
    
        });
        
</script>    
</head>

<body>
  <div class="div-black"></div>

  <div class="container"> 
    <div class="container2">
      <div class="div-logo">
        <a href="{{url('/login')}}"><div class="logo"></div></a>
      </div>

      <div align="center" style="background-color:#2cb99d">
        <h4 style="color:white;margin:0;height:30px;padding-top:10px;padding-bottom:30px">
          Auto Posting
        </h4>
      </div>

      <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
          <label for="username" class="label-home control-label">
            Email Address
          </label>
          <input id="username" type="text" class="form-control input-text-home" name="username" value="{{ old('username') }}" autofocus placeholder="Email">
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
          <label for="password" class="label-home control-label">
            Password
          </label>
          <input id="password" type="password" class="form-control input-text-home" name="password" placeholder="password">
        </div>

        <div class="form-group">
          <div class="checkbox">
            <label>
              <input type="checkbox" name="remember"> Remember Me
            </label>
          </div>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-home form-control">
            Sign in
          </button>
          <a class="btn btn-link" href="{{ url('/password/reset') }}">
            Forgot Your Password?
          </a>
        </div>

      </form>

      <div class="notif-user">
        @if ($errors->has('username'))
          <div class="alert alert-danger">
            <p align="center">{{ $errors->first('username') }}</p>
          </div>
        @endif

        @if ($errors->has('password'))
          <div class="alert alert-danger">
            <p align="center">{{ $errors->first('password') }}</p>
          </div>
        @endif

        @if (session('error') )
          <div class="alert alert-danger">
            <p align="center">{{session('error')}}</p>
          </div>
        @endif
        
        @if (session('success') )
          <div class="alert alert-success">
            <p align="center">{{session('success')}}</p>
          </div>
        @endif
      </div>

    </div>      
  </div>
</body>

</html>
