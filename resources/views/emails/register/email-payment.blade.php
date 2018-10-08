<?php if(env('APP_PROJECT')=='Celebgramme') {
        $service = 'activpost.net';
      } else {
        $service = 'Amelia';
      }
?>
Terima kasih,<br> 
anda telah mensubmit KONFIRMASI PEMBAYARAN ORDER <?php echo $service ?> service <br>

Info konfirmasi pembayaran yang anda submit sebagai berikut :
<br>
<strong>No Order :</strong> {{$no_order}} <br>
<strong>Nama Bank :</strong> {{$nama_bank}} <br>
<strong>No Rekening :</strong> {{$no_rekening}} <br>
<strong>Nama :</strong> {{$atas_nama}} <br>
<strong>Total :</strong> {{$total}} <br>
<strong>Keterangan :</strong> {{$keterangan}} <br>
<br>
Silahkan tunggu PENERIMAAN KONFIRMASI dari admin (max 1x24 jam) <br>

<br> Salam hangat, 
<br>
<?php echo $service ?>