<?php 
  if ( ($data->count()==0) ) {
    echo "<tr><td colspan='7' align='center'>Data tidak ada</td></tr>";
  } else {

  $i=($page-1)*15 + 1;
  foreach ($data as $arr) {
		$file = "";
		// $pieces = explode(".", $arr->file);
		// $ext = $pieces[1];
    $ext = substr(strrchr($arr->file,'.'),1);
?>
    <tr id="tr-{{ $arr->id }}">
      <td>
        {{$i}}
      </td>
      <td align="center">
				@if (!empty($arr->file))
					<?php 
						//$file = url('/images/users/'.$user->username.'-'.$user->id.'/'.$arr->file); 
            // $file = url('/images/users/'.$user->username.'-'.$user->id.'/'.$arr->file.'?v='.uniqid()); 
            if ($arr->is_s3) {
              $file = Storage::disk('s3')->url($arr->file);
            }
            else {
              $file = url('/../vp/users/'.$user->username.'-'.$user->id.'/'.$arr->file.'?v='.uniqid()); 
						}
					?>
						<img src="{{$file}}" class="img-responsive zoom" data-zoom-image="{{$file}}" width="65" height="65" >
				@endif
			
      </td>
      
      <td align="center">
			{{$arr->created_at}}
      </td>
      <td align="center"  data-url="{{ $file}}" data-owner="{{$arr->owner_post}}" data-caption="{{$arr->caption}}" data-id="{{$arr->id}}">
				<button class="btn btn-info link-edit"><span class="glyphicon glyphicon-pencil"></span> Edit</button>
				<a href="{{$file}}" class="btn btn-info link-download" download="{{'download.'.$ext}}"><span class="glyphicon glyphicon-download-alt"></span> Download</a>
				<button class="btn btn-home link-schedule"><span class="glyphicon glyphicon-time"></span> Schedule</button>
				<button class="btn btn-danger link-delete" data-toggle="modal" data-target="#confirm-delete" data-id="{{$arr->id}}"><span class="glyphicon glyphicon-remove"></span> Delete</button>
				

      </td>
      
    </tr>    
<?php 
    $i+=1;
  } 
  }
?>

<script>
    $('.zoom').elevateZoom({scrollZoom:true});

</script>