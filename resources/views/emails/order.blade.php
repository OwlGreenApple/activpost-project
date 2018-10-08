Terima kasih, anda telah melakukan pemesanan Amelia service.<br>
Info Order anda adalah sebagai berikut <br>
<br>
<strong>No Order :</strong> {{$no_order}} <br>
<strong>Nama :</strong> {{$user->name}} <br>
<strong>Status Order :</strong> {{$status}} <br>
Anda telah memesan paket 
<?php 
	if ($order->type=="daily-activity") {
?>

<?php if(env('APP_PROJECT')=='Celebgramme') { ?>
  {{$package->package_name}} = <strong>Rp. {{number_format($order->total - $order->discount,0,'','.')}} </strong><br>
<?php } else { 
  if($package->paket>=30){
    $package->paket = $package->paket/30;
    $package->paket = (string) $package->paket.' bulan';
  } else {
    $package->paket = (string) $package->paket.' hari';
  }
?>
  {{$package->akun}} akun {{$package->paket}} = <strong>Rp. {{number_format($order->total - $order->discount,0,'','.')}} </strong><br>
<?php } ?>

<?php 		
	}
	else if ($order->type=="max-account") {
?>

{{$order->added_account}} akun = <strong>Rp. {{number_format($order->total - $order->discount,0,'','.')}} </strong><br>

<?php 		
	}
?>
<br>
<?php if ( $status == "Belum lunas" ) { ?>
	Harap SEGERA melakukan pembayaran,<br> 
	<strong>TRANSFER Melalui :</strong><br>
	<br>
	<strong>Bank BCA</strong><br>
	6700382506<br>
  Steven Anthony<br>
	<br>
	<!--
	<strong>Bank Mandiri</strong><br>
	121-00-3592712-2<br>
	Sugiarto Lasjim<br>
	-->
	<br>
	
	
	dan setelah selesai membayar<br>
	silahkan KLIK <a href="{{url('confirm-payment')}}"> --> KONFIRMASI PEMBAYARAN <-- </a> disini . <br>
<?php } ?>

<br> Salam hangat, 
<br>
Amelia