<!DOCTYPE html>
<html lang="en">
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
    @if (Request::is('schedule') || Request::is('schedule/add') || Request::is('schedule/edit*') ||  Request::is('schedule/repost*') || Request::is('schedule/video') || Request::is('schedule/story'))
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
    <div id="div-loading">
      <div class="loadmain"></div>
      <div class="background-load"></div>
    </div>

    <nav class="navbar navbar-default navbar-static-top" style="z-index:1;">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Menu -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                  <?php if(env('APP_PROJECT')=='Celebgramme') { ?>
                    <img src="{{ asset('images/logo2.png') }}" alt="Activpost">
                  <?php } else { ?>
                    <img src="{{ asset('images/logo-amelia.png') }}" alt="Celebpost">
                  <?php } ?>
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <?php use Celebpost\Models\Account; ?>
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                  
                        <!--<li><a href="{{ url('/login') }}">Login</a></li>-->
                    @elseif (Auth::user()-> is_admin === 1)
                    
                      

                         <?php 
                        
                        $user = Auth::user();
                        //checking have accounts
                        $check_num_account = Account::where("user_id","=",$user->id)->count();

                      
                      ?>

                
                       
                        <!--<li @if(Request::is('maintenance')) class="active" @endif><a href="{{ url('/maintenance') }}">Maintenance</a></li>-->
                        <li @if(Request::is('coupon')) class="active" @endif><a href="{{ url('coupon') }}"><span class="glyphicon glyphicon-gift"></span> Coupon</a></li>

                        <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="glyphicon glyphicon-user"></span>  
                              List User   
                            <span class="caret"></span>
                          </a>

                          <ul class="dropdown-menu" role="menu">
                            <li @if(Request::is('list-user')) class="active" @endif>
                              <a href="{{ url('list-user') }}">
                                All
                              </a>
                            </li>

                            <?php 
                              $email = Auth::user()->email;
                              if($email=='admin@demo.com' || $email=='puspita.celebgramme@gmail.com' || $email=='it.axiapro@gmail.com') {
                            ?>
                              <li @if(Request::is('list-admin')) class="active" @endif>
                                <a href="{{ url('list-admin') }}">
                                  Admin
                                </a>
                              </li>
                            <?php } ?>
                            <li @if(Request::is('list-user-affiliate')) class="active" @endif>
                              <a href="{{ url('list-user-affiliate') }}">
                                User Affiliate
                              </a>
                            </li>
                            <li @if(Request::is('list-user-refund')) class="active" @endif>
                              <a href="{{ url('list-user-refund') }}">
                                User Refund
                              </a>
                            </li>
                          </ul>
                        </li>

                        <li @if(Request::is('list-order')) class="active" @endif><a href="{{ url('/list-order') }}"><span class="glyphicon glyphicon-euro"></span> List Order</a></li>
                        
                        <li @if(Request::is('list-account')) class="active" @endif><a href="{{ url('/list-account') }}"><span class="glyphicon glyphicon-plus"></span> List Account</a></li>

                        <li @if(Request::is('schedules')) class="active" @endif>
                        <a href="{{ url('/schedules')}}" id="link-schedules"><span class="glyphicon glyphicon-time"></span>
                          Schedules
                        </a>
                        </li>
                        
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Hi, {{ Auth::user()->name }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li @if(Request::is('order')) class="active" @endif><a href="{{ url('/order') }}">Buy More</a></li>
                                <li @if(Request::is('change-password')) class="active" @endif><a href="{{ url('/change-password') }}">Ubah Password</a></li>
                                
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                    @elseif (Auth::user()-> is_admin === 0)
                

                        <?php 
                        
                        $user = Auth::user();
                        //checking have accounts
                        $check_num_account = Account::where("user_id","=",$user->id)->count();


                      ?>
                
                        <li @if(Request::is('account')) class="active" @endif><a href="{{ url('/account') }}"><span class="glyphicon glyphicon-plus"></span> Accounts</a></li>

												
                        <li @if(Request::is('search-hashtags')) class="active" @endif>
												<a href="<?php if ( ($check_num_account>0) && ($user->is_confirmed) ) { echo url('/search-hashtags'); } else { echo '#'; }?> " class="<?php if ( ($check_num_account==0) || (!$user->is_confirmed) ) { echo 'disabled'; } ?>">Research #</a></li>
                        
                        
                        <li @if(Request::is('saved-images')) class="active" @endif>
												<a href="<?php if (!$user->is_confirmed) { echo '#'; } else { echo url('/saved-images'); }?>" class="<?php if (!$user->is_confirmed) { echo 'disabled'; } ?>">Images</a></li>
                        
                        
                        <li @if(Request::is('caption')) class="active" @endif>
												<a href="<?php if (!$user->is_confirmed) { echo '#'; } else { echo url('/caption'); }?>" class="<?php if (!$user->is_confirmed) { echo 'disabled'; } ?>">Captions</a></li>
                        <!--<li @if(Request::is('maintenance')) class="active" @endif><a href="{{ url('/maintenance') }}">Maintenance</a></li>-->
                        
                        
                        <li @if(Request::is('schedule')) class="active" @endif>
                        <a href="<?php if ( ($check_num_account>0) && ($user->is_confirmed) ) { echo url('/schedule'); } else { echo "#"; } ?>" id="link-schedule" class="<?php if ( ($check_num_account==0) || (!$user->is_confirmed) ) { echo 'disabled'; } ?>">
                          Schedules
                        </a>
                        </li>
                        
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Hi, {{ Auth::user()->name }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
															<li @if(Request::is('order')) class="active" @endif>
																<!--<a href="<?php if (!Auth::user()->is_member_rico) { echo url('/order'); } else {echo "https://amelia.id/order.php";} ?>">-->
                                <a href="<?php echo url('/order'); ?>">
																<!--<a disabled>-->
																	Buy More
																</a>
															</li>
															<!--
															<li @if(Request::is('confirm-payment')) class="active" @endif>
																<a href="{{ url('/confirm-payment') }}">
																	Confirm Payment
																</a>
															</li>
															-->
															<li @if(Request::is('confirm-order')) class="active" @endif>
																<a href="<?php echo url('/confirm-order'); ?>">
																	Confirm Order
																</a>
															</li>
															<li @if(Request::is('change-password')) class="active" @endif><a href="{{ url('/change-password') }}">Ubah Password</a></li>
															<li><a href="https://youtu.be/sys-y7F36bk" target="_blank"> <span class="glyphicon glyphicon-film"></span> Tutorial Video</a></li>
															<li><a href="https://docs.google.com/document/d/1CA7hxRL-3DTQiR8CoEX7yw58mx4LNRmfLKahaHtKFic/edit" target="_blank"><span class="glyphicon glyphicon-list-alt"></span> Tutorial PDF </a></a></li>
															<li>
																<a href="{{ url('/logout') }}"
																onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                  Logout
																</a>
																<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
																	{{ csrf_field() }}
																</form>
															</li>
                            </ul>
                    @endif


                   

                </ul>
            </div>
        </div>
    </nav>
    <?php 
      $images_home = asset('/images/home.jpg');
    ?>
    <div @if ( (Auth::guest()) && (!Request::is('test')) ) style="background : url('{{$images_home}}') no-repeat center center fixed;  -webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover,100%;" @endif class="main-content" >
      @yield('content')
    </div>
    
    
    <div class="div-footer">
      <p>© 2018 Activpost.net All rights reserved</p>
    </div>
    
    <?php 
      if(Auth::check()){
        if(Auth::user()->is_member_rico || Auth::user()->is_admin) { 
    ?>
          <script>
            window.fcWidget.init({
              token: "660fa27c-cfa6-4fa3-b6c9-cd41aad6ab87",
              host: "https://wchat.freshchat.com"
            });
          </script>  
    <?php 
        } 
      }
    ?>
</body>
</html>
