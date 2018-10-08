@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">&nbsp</div>
<div class="row">
<div class="col-md-12">
<?php if(env('APP_PROJECT')=='Celebgramme') { ?>
<table class="table">
	<thead>
		<tr>
			<th>No</th>
			<th>No Order</th>
			<!--<th>Paket Bulanan</th>
			<th>Bulan</th>
			-->
			<th>Sub Total</th>
			<th>Kupon</th>
			<th>Total</th>
			<th>Created</th>
			<th>Status</th>
			<th>Confirm Payment</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			$no = $orderlistuser->firstItem()  ; 
		?>
		@foreach ($orderlistuser as $orderlist)
		<tr>
			<td>{{$no++}}</td>
			<td><?=str_replace('OCPS', '', $orderlist->no_order) ?></td>
			<?php $nor= str_replace('OCPS', '', $orderlist->no_order) ?>
			<!--<td>{{number_format($orderlist->base_price, 0,'.','.')}} </td>
			<td>{{$orderlist->month}} </td>-->
			<td>{{number_format($orderlist->sub_price, 0,'.','.')}} </td>
			<td>{{$orderlist->discount}} </td>
			<td>{{number_format($orderlist->total, 0,'.','.')}} </td>
			<td>{{date('M d, Y',strtotime($orderlist->created_at))}} <strong>{{date('H:i',strtotime($orderlist->created_at))}}</strong></td>
		 
			<td 
				<?php if ( ($orderlist->order_status == "Confirmed") || ($orderlist->order_status=="cron dari affiliate") ) : ?>
					style="font-weight: bold;color:#2b9984;"
				<?php elseif($orderlist->order_status == "Not Confirmed") : ?>
					style="font-weight: bold;color:#ada4a4;"
				<?php elseif($orderlist->order_status == "Pending") : ?>
					style="font-weight: bold;color:#f90625;"
				<?php endif ?>

		 
			>
				<?php 
					if ($orderlist->order_status=="cron dari affiliate") {
						echo "Confirmed";
					} else {
						echo $orderlist->order_status;
					}
				?> 
			</td>
			<td>
				<?php if ($orderlist->order_status == "Pending"): ?>
				<a href="{{action('User\OrderController@confirpay',['id'=>$nor,'tipe'=>'clbp'])}}" class="btn btn-info" role="button">Confirm</a> 
				<?php endif?>
			</td>
		</tr>   
 	  @endforeach


	</tbody>
</table>
    
{!! $orderlistuser->render() !!}
<?php } ?>


<?php if(env('APP_PROJECT')=='Amelia') { ?>
<table class="table">
  <thead>
    <tr>
      <th>No</th>
      <th>No Order</th>
      <th>Total</th>
      <th>Created</th>
      <th>Status</th>
      <th>Confirm Payment</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $no = $orderclb->firstItem()  ; 
    ?>
    @foreach ($orderclb as $orderlist)
    <tr>
      <td>{{$no++}}</td>
      <td><?=str_replace('OCLB', '', $orderlist->no_order) ?></td>
      <?php $nor= str_replace('OCLB', '', $orderlist->no_order) ?>
      <!--<td>{{number_format($orderlist->base_price, 0,'.','.')}} </td>
      <td>{{$orderlist->month}} </td>-->
      <td>{{number_format($orderlist->total, 0,'.','.')}} </td>
      <td>{{date('M d, Y',strtotime($orderlist->created_at))}} <strong>{{date('H:i',strtotime($orderlist->created_at))}}</strong></td>
     
      <td 
        <?php if ( ($orderlist->order_status == "success")) : ?>
          style="font-weight: bold;color:#2b9984;"
        <?php elseif($orderlist->order_status == "pending") : ?>
          style="font-weight: bold;color:#f90625;"
        <?php endif ?>
      >
        <?php 
          echo $orderlist->order_status;
        ?> 
      </td>
      <td>
        <?php if ($orderlist->order_status == "pending"): ?>
        <a href="{{action('User\OrderController@confirpay',['id'=>$nor,'tipe'=>'clb'])}}" class="btn btn-info" role="button">Confirm</a> 
        <?php endif?>
      </td>
    </tr>   
    @endforeach


  </tbody>
</table>
    
{!! $orderclb->render() !!}

<?php } ?>

</div>     
</div>
</div>
@endsection
   

        

