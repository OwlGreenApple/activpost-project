@extends('layouts.app')

@section('content')
<script type="text/javascript">
  $.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
</script>
  <div class="row">
  <div class="ip">
  <div class="col-md-10 text-center col-md-offset-1">
    <div class="col-md-6">
         <div class="col-md-6 text-left" style="
    padding-bottom: 5px;font-size: 20px;
"><strong> Information Order :</strong></div>
        <div class="row"></div>
       <div class="col-md-3 text-left" id="noorder"><strong>No Order        :</strong></div><div class="col-md-2 text-left">
          <strong>
            <?=str_replace('OCPS', '', $orderdis->no_order) ?>    
          </strong>
        </div>
       <div class="row"></div>
        <div class="col-md-3 text-left"><strong>Nama        :</strong></div><div class="col-md-2 text-left"><strong>{{ $orderdis->name }}</strong></div>
        <div class="row"></div>
        <div class="col-md-3 text-left" id="pakets"><strong> Paket       :</strong></div><div class="col-md-3 text-left"><strong>Rp {{number_format($orderdis->base_price, 0,'.','.')}}</strong></div>
        <div class="row"></div>
        <div class="col-md-3 text-left" id="bulans"><strong>Discount       :</strong> </div><div class="col-md-6 text-left"><strong>Rp {{number_format($orderdis->discount, 0,'.','.') }}</strong></div>
        <div class="row"></div>
         <div class="col-md-3 text-left" id="subtotals"><strong> Total Hari    : </strong></div><div class="col-md-6 text-left"><strong>{{$orderdis->meta_value}} hari   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   </strong></div>
        <div class="row"></div>
        <div class="col-md-3 text-left" id="totalprices"><strong>Total Price : </strong> </div><div class="col-md-3 text-left"><strong>Rp {{number_format($orderdis->total, 0,'.','.')}}</strong></div>
        </div>
        
        <div class="col-md-5">
        <div class="col-md-9 text-left" style="
    padding-bottom: 5px;font-size: 20px;
"><strong>SILAHKAN TRANSFER Ke :</strong></div>
            
          <div class="row"></div>
          <div class="col-xs-6 col-md-5 text-left">
          <p style="font-weight: bold;font-size: 20px;margin: 0; line-height: 12px;">Bank BCA</p>
          <div class="row"></div>
          <div style="font-size: 20px">
          8290-336-261
          </div>
          <div class="row"></div>
           <div style="font-size: 20px">
          Sugiarto Lasjim
          </div>
          <div class="row"></div>
          <!--
          <a href="{{url('confir-payment')}}" class="btn btn-info" role="button">Confirm Payment</a> 
          -->
           
        </div>
        </div>
</div>
  <div class="row"></div>

  <div class="col-md-6 col-md-offset-3">
		<h3>Confirm Payment</h3>
		<form action="" method="" enctype="multipart/form-data" id="form-confirm" >
			{{ csrf_field() }}
			<div class="form-group row">
				
				<div class="col-xs-5">
					<input class="form-control" name="no_order" id="no_order" type="hidden"  placeholder="No Order" value="<?=str_replace('OCPS', '', $orderdis->no_order) ?>" >
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
<div class="col-md-12 text-center" id="pesanlain" style="display: none;font-size: 30px;">
<div class="col-md-12 text-center" style="font-weight: bold;padding-bottom: 10px;color: #15b49e">Confirm Payment Success</div>
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
Silahkan hubungi kami di line @Celebpost atau email activpost@gmail.com
<br>
Customer service kami akan membantu anda
</div>
<!--
<dir class="row"></dir>
SILAHKAN TRANSFER Melalui :
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




@endsection