@extends('layouts.app')

@section('content')
<script type="text/javascript">
  $(document).ready(function() {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
  });
</script>
<div class="container">
		<div class="snoorder"></div>
		<h3>Confirm Payment</h3>
		<form action="" method="" enctype="multipart/form-data" id="form-confirm" >
			{{ csrf_field() }}
			<div class="form-group row">
				<label  class="col-xs-3 col-form-label">No Order</label>
				<div class="col-xs-7">
					<input class="form-control" name="no_order" id="no_order" type="text"  placeholder="No Order" value="" >
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
				<div class="col-xs-7">
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
			<button type="button" class="btn btn-primary" id='conpay'>Submit</button>
		
		</form>
</div>
@endsection
