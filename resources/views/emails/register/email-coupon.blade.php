Hi <strong>{{$nama}}</strong> ,
<br>
<br>
Selamat yah,
<br>   
Anda Mendapatkan KUPON untuk Potongan Harga Berlangganan activpost.net
<br>
<br>
INFO KUPON dibawah ini :
<br>
<br>
Kode kupon : <strong>{{$coupon_code}}</strong> 
<br>
 @if ($coupon_value === '')
 Potongan : <strong>{{$coupon_percent}} %  ( berlaku untuk semua paket ) </strong>
 @else
 Potongan : <strong>Rp {{number_format($coupon_value, 0,'.','.')}} ( berlaku untuk semua paket ) </strong>
 @endif
<br>
Masa berlaku kupon sampai : <strong>{{date('d-M-Y', strtotime($valid_until))}}</strong> 
<br>
Sisah waktu pemakaian paket <strong><?= intval(intval($active_time)/(3600*24));?> hari</strong> 
<br>
<br>
ORDER SEKARANG disini ---> <STRONG><a href="{{url('order')}}"> https://activpost.net/order </a></STRONG> <--- <br>
<br>
<br>
Selamat yah, 
<br>
silahkan gunakan Kupon SEKARANG juga
<br>
Sebelum masa berlakunya habis
<br>
<br>
<strong> *PS: Kupon ini Exclusive HANYA untuk username anda saja </strong> 
<br>
<br>
Semoga bermanfaat,
<br>
<br>
Salam hangat,
<br>
<br>
<br>
Admin activpost.net