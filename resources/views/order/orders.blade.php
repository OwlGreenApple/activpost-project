@extends('layouts.app')


@section('content')

<script type="text/javascript">
  $.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
</script>
<div class="container">
  @if(Session::has('message'))
    <span class="label label-succes">{{Session::get('message')}}</span>
  @endif
	
  <div class="ip"> 
  <form action="" method="" class="col-md-9 col-md-offset-2" >
  {{ csrf_field() }}
	<h3>Buy More&nbsp <span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Update Paket Langganan</div><div class='panel-content'>Setiap Paket menjadi paket kredit waktu <br> untuk penambahan akun tidak perlu membeli paket dibawah <br>Paket yang sudah dibeli akan tetap berlaku sesuai pembelian <br>Paket efektif September 2018 <br></div>"></span></h3>
		<div class="form-group form-group-sm row">
			<label class="col-xs-8 col-sm-4 col-md-3 control-label" for="formGroupInputSmall">Order Tipe</label>
			<div class="col-sm-8 col-md-5">
			<input type="radio" value="daily-activity" name="type" id="daily-activity" data-env="<?php echo env('APP_PROJECT') ?>" checked> <label for="daily-activity">Waktu harian</label> &nbsp
			<input type="radio" value="max-account" name="type" id="max-account"> <label for="max-account">Maksimum Akun</label> 
			</div>
		</div>  
      <input type="hidden" name="id" value="{{$user->id}}">
      <div  class="form-group" id="div-auto-manage">
			 <?php if(env('APP_PROJECT')=='Celebgramme') { ?>
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
			<?php } else { ?>
        <div class="form-group row">
          <label class="col-xs-8 col-sm-4 col-md-3 control-label" for="formGroupInputSmall">Paket</label>
          <div class="col-sm-8 col-md-5">
            <select class="form-control" name="paket" id="select-paket">
              @foreach($packages as $package)
                <option value="{{$package->id}}" data-real="{{$package->harga}}" data-show="<?php echo number_format($package->harga) ?>">
                  <?php  
                      if($package->paket>=30){
                        $package->paket = $package->paket/30;
                        $package->paket = (string) $package->paket.' bulan';
                      } else {
                        $package->paket = (string) $package->paket.' hari';
                      }

                      echo 'Paket '.$package->akun.' akun '.$package->paket.' - Rp.'.number_format($package->harga);
                  ?>
                </option>
              @endforeach
            </select>
          </div>
        </div>
      <?php } ?>
      </div>

			<div class="form-group row" id="div-maximum-account" style="display:none;">
				<label class="col-xs-8 col-sm-4 col-md-3 control-label" for="formGroupInputSmall">Tambah Max Akun</label>
				<div class="col-sm-8 col-md-5">
					<select class="form-control" name="maximum-account" id="select-maximum-account">
							<option value="3" data-real="100000" data-show="100.000">Tambah 3 Akun (Rp. 100.000)</option>
							<option value="6" data-real="200000" data-show="200.000">Tambah 6 Akun (Rp. 200.000)</option>
							<option value="9" data-real="300000" data-show="300.000">Tambah 9 Akun (Rp. 300.000)</option>
					</select>
				</div>
			</div>
			
      <div id="jmlig" style="display: none;"></div>
			<input type="hidden" id="days" value="0">

											<div class="form-group col-md-12 col-sm-12 col-xs-12 pull-right">
												<div class="col-xs-3 col-sm-3 col-md-3 text-center">
													<!--<div  style="margin-top: 10px;">Paket</div>-->
												</div>
												<div class="col-md-1 col-xs-1 col-sm-1 text-center" style=""></div>
												<div class="col-xs-3 col-sm-3 col-md-3 text-center">
													<!--<div  style="margin-top: 10px;">Bulan</div>-->
												</div>
												<div class="col-xs-2 col-sm-2 col-md-2 text-center"></div>
												<div class="col-xs-3 col-sm-3 col-md-3" style="margin-top: 10px;">
													Total
												</div>
												<div class="row"></div>
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
													<!--<div style="margin-top: 10px;">=</div>-->
												</div>
												<div class="col-md-3 col-xs-12 col-sm-3 text-left">
													<div id="total" class="total">-</div>
												</div>
											</div>
											<div class="row"></div>
											<div class="form-group col-md-12 col-sm-12 col-xs-12 pull-right">
												<div class="col-xs-4 col-sm-4 col-md-4 col-md-offset-1 col-sm-offset-1 col-xs-offset-1">
													<input type="text" name="coupon_code" id="coupon_code" class="form-control" placeholder="kode kupon" value="" >
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
					
					
          <div class="row"></div>
          <button type="button" class="btn btn-primary col-md-offset-8" id='add'>Bayar Sekarang</button>
         
       </form>
       
  </div>
	
	
	
	
	
	
	<div class="col-md-12 text-center" id="msgorder" style="display: none;">
	<div style="font-size: 30px;padding-bottom: 10px;" >Order Success</div>
	<div class="row"></div>
	<div class="row"></div>
       
        <div class="col-md-6">
         <div class="col-md-5 text-left" style="
    padding-bottom: 5px;font-size: 20px;
"><strong> Information Order :</strong></div>
        <div class="row"></div>
       <div class="col-md-3 text-left" id="noorder"><strong>No Order        :</strong></div>
       <div class="row"></div>
        <div class="col-md-3 text-left"><strong>Nama        :</strong></div><div class="col-md-2 text-left"><strong>{{ Auth::user()->name }}</strong></div>
        <div class="row"></div>
        <div class="col-md-3 text-left" id="tipe_order"><strong> Tipe Order       :</strong></div>
        <div class="row"></div>
        <div class="col-md-3 text-left" id="pakets"><strong> Paket       :</strong></div>
        <div class="row"></div>
        <div class="col-md-3 text-left" id="coupon">
          <strong>Discount       :</strong>
        </div>
        <div class="row"></div>
        <div class="col-md-3 text-left" id="hari"><strong>Total Hari       :</strong></div>
        <div class="row"></div>
        <div class="col-md-3 text-left" id="totalprices"><strong>Total Price : </strong> </div>
        </div>
        
        <div class="col-md-5">
        <div class="col-md-8 text-left" style="
    padding-bottom: 5px;font-size: 20px;
"><strong>SILAHKAN TRANSFER Ke :</strong></div>
            
          <div class="row"></div>
          <div class="col-xs-6 col-md-5 text-left">
          <p style="font-weight: bold;font-size: 20px;margin: 0; line-height: 12px;">Bank BCA</p>
          <div class="row"></div>
          <?php if(env('APP_PROJECT')=='Celebgramme') { ?>
            <div style="font-size: 20px">
            8290-336-261
            </div>
            <div class="row"></div>
             <div style="font-size: 20px">
            Sugiarto Lasjim
            </div>
          <?php } else { ?>
            <div style="font-size: 20px">
            6700382506
            </div>
            <div class="row"></div>
            <div style="font-size: 20px">
            Steven Anthony
            </div>
          <?php } ?>
          <div class="row"></div>
          <!--
          <a href="{{url('confir-payment')}}" class="btn btn-info" role="button">Confirm Payment</a> 
          -->
           <button class="btn btn-info" role="button" id="confir"> Confirm Payment</button>
        </div>
        </div>

        <div class="row"></div>
        <div class="col-md-7 col-md-offset-3 text-left" id="showform" style="display: none;">

<form action="" method="" enctype="multipart/form-data" id="form-confirm" >
   {{ csrf_field() }}
	<div class="form-group row">
  <div class="col-xs-5">
    <div id="no_order"></div>
  </div>
</div>
<div class="form-group row">
  <label  class="col-xs-3 col-form-label">Bank Pengirim</label>
  <div class="col-xs-7">
    <input class="form-control" name="nama_bank" id="nama_bank" type="text" placeholder="Nama Bank" value="" >
  </div>
</div>
<div class="form-group row">
  <label  class="col-xs-3 col-form-label">No Rek Pengirim</label>
  <div class="col-xs-5">
    <input class="form-control" name="no_rekening" id="no_rekening" type="text" placeholder="No Rekening" value="" >
  </div>
</div>
<div class="form-group row">
  <label  class="col-xs-3 col-form-label">Nama Pengirim</label>
  <div class="col-xs-7">
    <input class="form-control" name="atas_nama" id="atas_nama" type="text" placeholder="Atas Nama" value="" >
  </div>
</div>
<div class="form-group row">
  <label  class="col-xs-3 col-form-label">Bukti Pembayaran</label>
  <div class="col-xs-7">
    <input type="file" id="gambar" multiple name="gambar" / >
    <div class="row"></div>
  <div id="ukuranimage"></div>
  <div class="imageukuran"></div>
  </div>

</div>
<div class="form-group row">
  <label  class="col-xs-3 col-form-label">Keterangan</label>
  <div class="col-xs-7">
    
    <textarea class="form-control" name="keterangan" type="text" placeholder="Keterangan" value=""></textarea>
  </div>
</div>
	<div class="snoorder"></div>

  <button type="button" class="btn btn-primary" id='conpay'>Submit</button>
  
</form>
        </div>
       


</div> 
<div class="row"></div>

<div class="col-md-12 text-center" id="msgsukses" style="display: none;font-size: 30px;">
<div class="col-md-12 text-center" style="font-weight: bold;padding: 25px 0px 25px 0;color: #15b49e;font-family: Arial, Helvetica, sans-serif">Confirm Payment Success</div>
<div class="row"></div>
<div class="col-md-12 text-center" style="font-weight: bold;font-size: 17px;padding-bottom: 15px;font-family: Arial, Helvetica, sans-serif;text-decoration: underline;"><div id="orderkode"></div></div>    
<div class="row"></div>
<div class="col-md-12 text-center" style="font-size: 15px;">
Terima kasih anda telah melakukan konfirmasi pembayaran
<br>
Sistem admin kami akan melakukan pengecekan pembayaran anda
<br>
Silahkan tunggu max 1x24 jam
<br>
Jika sudah di konfirmasi, paket yang anda beli akan otomatis ditambahan ke akun anda.
<br>
Apabila pembelian anda belum terupdate dalam waktu 1x24 jam
<br>

<?php if(env('APP_PROJECT')=='Celebgramme') { ?>
  Silahkan hubungi kami di <strong> line @Celebpost </strong> atau <strong>email activpost@gmail.com</strong>
<?php } else { ?>
  Silahkan hubungi kami di <strong>email support@amelia.id</strong>
<?php } ?>
<br>
Customer service kami akan membantu anda
</div>
<!--
<div class="row"></div>
TRANSFER Melalui :
<dir class="row"></dir>
<div class="col-xs-6 col-md-5 text-center">
          <p style="font-weight: bold;">Bank BCA</p>
          <div class="row"></div>
          8290-336-261
          <div class="row"></div>
          Sugiarto Lasjim
</div>
-->
</div>



        
        </div>
        
        <!--
        <div class="row"></div>
         <div class="row"></div>
        <div class="col-md-8 col-md-offset-2">
        TRANSFER Melalui :
        <div class="row"></div>
         <div class="row"></div>
        <div class="col-xs-6 col-md-5 text-center">
          <p style="font-weight: bold;">Bank BCA</p>
          <div class="row"></div>
          4800-227-122
          <div class="row"></div>
          Sugiarto Lasjim
        </div>
        <div class="col-xs-6 col-md-5 text-center">
          <p style="font-weight: bold;">Bank Mandiri</p>
          <div class="row"></div>
          121-00-3592712-2
          <div class="row"></div>
          Sugiarto Lasjim
        </div>
        </div>
        -->



</div>

 
@endsection

