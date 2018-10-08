Terima kasih, anda telah melakukan pemesanan activpost.net service.<br>
Info Order anda adalah sebagai berikut <br>
<br>
<strong>No Order :</strong> {{$no_order}} <br>
<strong>Nama :</strong> {{$user->fullname}} <br>
<strong>Status Order :</strong> {{$status}} <br>
Anda telah memesan paket {{$package->package_name}} = <strong>Rp. {{number_format($order->total - $order->discount,0,'','.')}} </strong><br>
<br>
<?php if ( $status == "Belum lunas" ) { ?>
	Harap SEGERA melakukan pembayaran,<br> 
	<strong>TRANSFER Melalui :</strong><br>
	<br>
	<strong>Bank BCA</strong><br>
	4800-227-122<br>
	Sugiarto Lasjim<br>
	<br>
	<strong>Bank Mandiri</strong><br>
	121-00-3592712-2<br>
	Sugiarto Lasjim<br>
	<br>
	
	
	dan setelah selesai membayar<br>
	silahkan KLIK <a href="{{url('confirm-payment')}}"> --> KONFIRMASI PEMBAYARAN <-- </a> disini . <br>
<?php } ?>

<br> Salam hangat, 
<br>
activpost.net
