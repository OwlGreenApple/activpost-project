@extends('layouts.app')


@section('content')

<script type="text/javascript">
  $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

<!-- Facebook Pixel Code celebpost initiate checkout pilih paket-->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '298054080641651');
  fbq('track', 'PageView');
  fbq('track', 'InitiateCheckout');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=298054080641651&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
</script>
	
<input type="hidden" value="daily-activity" name="type" id="daily-activity" checked>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h1>Pilih Paket Berlangganan</h1></div>
                <div class="panel-body">
	
									@if(Session::has('message'))
										<span class="labe label-succes">{{Session::get('message')}}</span>
									@endif
									<div class="ip"> 
								
								
										<form action=" {{url('orderslogin')}} " method="POST" class="col-md-12
										col-xs-12 col-sm-12" >
											{{ csrf_field() }}
											<div  class="form-group" >
												
				<div class="col-3 bg as" >
				Simple
				<div class="bgb" id="my_radio_box">
				<div class="txtprice">Rp 195.000,-</div>
				<div class="beln">3 IG Accounts <br>30 Hari</div>

				<!--<div class="beln">Per Bulan</div>-->
				 <input type="hidden" id="values" name="my_options"  value="195.000">
				 <div class="bels bl">BELI SEKARANG</div>
				</div>
				</div> 
				
				<div class="col-3 bg as2">
				Most Popular
				<div class="bgb" id="my_radio_box2">
				<div class="txtprice">Rp 395.000,-</div>
				<div class="beln">3 IG Accounts <br>90 Hari</div>
				 <input type="hidden" id="values2" name="my_options" value="395.000">
				 <div class="bels bl2">BELI SEKARANG</div>
				</div>
				</div>
				
				<div class="col-3 bg as3">
				Favorite
				<div class="bgb" id="my_radio_box3">
				<div class="txtprice">Rp 295.000,-</div>
				<div class="beln">3 IG Accounts <br>60 Hari </div>
				
				 <input type="hidden" id="values3" name="my_options" value="295.000">
				 <div class="bels bl3">BELI SEKARANG</div>
				</div>
				</div>
				
				<div class="col-3 bg as4">
				New Artist
				<div class="bgb" id="my_radio_box4">
				<div class="txtprice">Rp 695.000,-</div>
				<div class="beln">3 IG Accounts <br>180 Hari </div>
				
				 <input type="hidden" id="values4" name="my_options" value="695.000">
				 <div class="bels bl4">BELI SEKARANG</div>
				</div>
				</div>
				
				<div class="col-3 bg as5">
				Rising Star
				<div class="bgb" id="my_radio_box5">
				<div class="txtprice">Rp 995.000,-</div>
				<div class="beln">3 IG Accounts <br>270 Hari </div>
				
				 <input type="hidden" id="values5" name="my_options" value="995.000">
				 <div class="bels bl5">BELI SEKARANG</div>
				</div>
				</div>
				
				<div class="col-3 bg as6">
				New Artist
				<div class="bgb" id="my_radio_box6">
				<div class="txtprice">Rp 1.285.000,-</div>
				<div class="beln">3 IG Accounts <br>360 Hari </div>
				
				 <input type="hidden" id="values6" name="my_options" value="1.285.000">
				 <div class="bels bl6">BELI SEKARANG</div>
				</div>
				</div>
			
				
												
											</div>
											<div id="jmlig" style="display: none;"></div>
											<div class="form-group col-md-8 col-sm-12 col-xs-12 pull-right">
												<div class="col-xs-3 col-sm-3 col-md-3 text-center">
													<!--<div  style="margin-top: 10px;">Paket</div>-->
												</div>
												<div class="col-md-1 col-xs-1 col-sm-1 text-center" style=""></div>
												<div class="col-xs-3 col-sm-3 col-md-3 text-center">
													<!--<div  style="margin-top: 10px;">Bulan</div>-->
												</div>
												<div class="col-xs-2 col-sm-2 col-md-2 text-center"></div>
												<dir class="col-xs-3 col-sm-3 col-md-3" style="margin-top: 10px;">
													Total
												</dir>
												<dir class="row"></dir>
												<div class="col-xs-3 col-sm-3 col-md-3 text-center">
													<!--<div id="msg" style="margin-top: 10px;">-</div>-->
												</div>
												<div class="col-md-1 col-xs-1 col-sm-1 text-center" style="margin-top: 10px;">
													<!--X-->
												</div>
												<div class="col-xs-3 col-sm-3 col-md-3 text-center">
													<!--<input type="text" name="ordermonth" class="form-control" onkeyup="hitung();" placeholder="" id="ordermonth">-->
												</div>
												<div class="col-xs-2 col-sm-2 col-md-2 text-center">
													<!--<div id="msg" style="margin-top: 10px;">=</div>-->
												</div>
												<dir class="col-md-3 col-xs-12 col-sm-3 text-left">
													<div id="total" class="total">-</div>
												</dir>
											</div>
											<div class="row"></div>
											<div class="form-group col-md-8 col-sm-12 col-xs-12 pull-right">
												<div class="col-xs-4 col-sm-4 col-md-4 col-md-offset-1 col-sm-offset-1 col-xs-offset-1">
													<input type="text" name="coupon_code" id="coupon_code" class="form-control" placeholder="kode kupon" value="">
												</div>
												<div class="col-xs-2 col-sm-2 col-md-2">
												<!--
												 <button type="button" class="btn btn-primary" id='addk'><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
												 -->
													<button type="button" class="btn btn-primary" id='addk'>Enter Kupon</button>
												</div>
												<div class="col-xs-2 col-sm-2 col-md-2 text-center">
												 <div id="msg" style="margin-top: 10px;">=</div>
												</div>
												<div class="col-xs-12 col-sm-2 col-md-2">
												<div class="kupon text-left" style="margin-top: 10px;">0</div>
												<div class="kupon2" style="margin-top: 10px;display: none;"></div>
												</div>
												
												<div class="row">
												</div>
												<div class="kupond col-md-offset-9 col-sm-offset-9 col-xs-offset-9" id="msgorder2" style="margin-top: 10px;height: 19px;">
												</div>
												<!--
												<button type="button" class="btn btn-primary col-md-offset-9" id='addk'><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
												-->

												<div class="col-xs-8 col-sm-3 col-md-3 text-center col-md-offset-4 col-xs-offset-4 col-sm-offset-4 ">
													<div id="msg" style="margin-top: 10px;">GrandTotal Price</div>
												</div>
												<div class="col-xs-2 col-sm-2 col-md-2 text-center">
													<div id="msg" style="margin-top: 10px;">=</div>
												</div>
												<div class="col-xs-6 col-sm-3 col-md-3">
												<div class="jumlahs" style="margin-top: 10px;"></div>
												</div>
											</div>
											<div class="row">
											</div>
												<input type="hidden" name="base_price">
												<input type="hidden" name="discount">
												<input type="hidden" name="total">
												<input type="hidden" name="sub_price">
												<input type="hidden" name="max_account">
												<input type="hidden" id="days" name="days">

												<button type="Submit" class="btn btn-primary col-md-offset-10" id="dashboard-add">
												Order Now
												</button>
												
										</form>
										 
									</div>
					

					
				
										<div class="description">
											<div class="col-md-6 col-xs-12" style="margin-top:50px;">
												<!--
												<div class="image-description center-block img-responsive">
												</div>
											-->
												<img src="{{url('images/register/laptop-landingpage-celebpost.png')}}" width="525" height="275" class="img-responsive">
											</div>
											<div class="content-description col-md-6 col-xs-12"  style="margin-top:50px;">
												<h3 style="margin-top:20px;">Cara Pembayaran</h3>
												<p> 
													1. Silahkan cek harga paket yang telah tersedia di halaman ini <br>
													2. Pilih paket yang anda inginkan. <br>
													3. Masukkan kode kupon potongan harga jika ada, klik Order Now <br>
													4. Silahkan lakukan pembayaran <br>
													5. Untuk proses pemesanan selanjutnya, mohon konfirmasi pembayaran anda dengan mengisi form konfirmasi pembayaran. <br>
													6. Silahkan log in, paket anda telah aktif <br>
													<strong>Selamat menggunakan Celebpost!</strong>
												</p>
											</div>
										</div>
				
				
				


					</div>
				</div>
			</div>
		</div>
	</div>
</div>

 
@endsection

