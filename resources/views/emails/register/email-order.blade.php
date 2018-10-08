Terima kasih, anda telah melakukan pemesanan activpost.net service.<br>
Info Order anda adalah sebagai berikut <br>
<br>
<strong>No Order :</strong><?= str_replace("OCPS","",$no_order)?> <br>
<strong>Nama :</strong> {{$username}} <br>
<strong>Status Order :</strong> {{$order_status}} <br>
Anda telah melakukan order sebesar = <strong>Rp. {{number_format($total,0,'','.')}} </strong><br>
<br>
  Harap SEGERA melakukan pembayaran,<br> 
  <strong>TRANSFER Melalui :</strong><br>
  <br>
  <strong>Bank BCA</strong><br>
  8290-336-261<br>
  Sugiarto Lasjim<br>
  <br>
  
  
  
  dan setelah selesai membayar<br>
  silahkan KLIK <a href="{{url('confirm-order')}}"> --> KONFIRMASI PEMBAYARAN <-- </a> disini . <br>

<br> Salam hangat, 
<br>
activpost.net

