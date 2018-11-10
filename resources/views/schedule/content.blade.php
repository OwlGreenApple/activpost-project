<?php 
	use Celebpost\Models\ScheduleAccount;
	// use Carbon;
	$user = Auth::user();
  
  $i=($page-1)*10 + 1;
	
	// $arr_shadow_schedule_accounts = array();
	// $scheduleAccountShadows = ScheduleAccount::select("accounts.username","schedule_account.status","schedule_account.schedule_id")
												// ->join("accounts","accounts.id","=","schedule_account.account_id")
												// ->whereDate("publish_at",">=",Carbon::now()->format('Y-m-d'))
												// ->get();
	// foreach($scheduleAccountShadows as $scheduleAccountShadow){
		// $arr_shadow_schedule_accounts[] = array(
																			// "schedule_id"=>$scheduleAccountShadow->schedule_id,
																			// "username"=>$scheduleAccountShadow->username,
																			// "status"=>$scheduleAccountShadow->status,
																		// );
	// }
	
  foreach ($data as $arr) {
?>
    <tr id="tr-{{ $arr->id }}">
      <td>
        {{$i}}
      </td>
      <td align="center">
				<!--<img src="{{$arr->image}}" class="img-responsive" width="65" height="65">-->
				<!--<img src="{{'images/uploads/'.$user->username.'-'.$user->id.'/'.$arr->slug.'.jpg'}}" class="img-responsive" width="65" height="65">-->
        <?php if($arr->media_type=='photo') { 
          $img = $arr->slug;
          if(strpos($arr->slug, 'PublishFile')===0){ //check jika diawali 
            $img = $img.'.jpg';
          }
        ?>
          <img src="{{'../vp/uploads/'.$user->username.'-'.$user->id.'/'.$img}}" class="img-responsive" width="65" height="65" >
        <?php 
				} 
				else {
						if ($arr->status >= 2 ){
				?>
							<div class="video-remove"><i class="glyphicon glyphicon-play-circle"></i></div>
        <?php }
						else {
				?>
							<video src="{{'../vp/uploads/'.$user->username.'-'.$user->id.'/'.$arr->slug}}" width="65" height="65"></video>
        <?php }} ?>
      </td>
      <td align="center">
				<?php 
					$scheduleAccounts = ScheduleAccount::select("accounts.username","schedule_account.status")
															->join("accounts","accounts.id","=","schedule_account.account_id")
															->where("schedule_id","=",$arr->id)
															->whereDate("publish_at",Carbon::createFromFormat('Y-m-d H:i:s', $arr->publish_at)->format('Y-m-d'))
															->get();
					foreach ($scheduleAccounts as $scheduleAccount) {
						$str_description = "";
						if ($scheduleAccount->status==1) {
							$str_description = "Pending";
						} else if ($scheduleAccount->status==2) {
							$str_description = '<span style="color:#2b9984;">Published</span>';
						} else if ($scheduleAccount->status==3) {
							$str_description = '<span style="color:#c12e2a;">Deleted</span>';
						} else if ($scheduleAccount->status==5) {
							$str_description = '<span style="color:#a94442;">(Need Reschedule)</span>';
						}
						echo "<a href='http://instagram.com/".$scheduleAccount->username."' target='blank'>".$scheduleAccount->username."</a> ".$str_description."<br>";
					}
					// foreach ($arr_shadow_schedule_accounts as $arr_shadow_schedule_account) {
						// if ((string) $arr_shadow_schedule_account->schedule_id== (string)$arr->id) {
							// $str_description = "";
							// if ((string)$arr_shadow_schedule_account['status']=="1") {
								// $str_description = "Pending";
							// } else if ((string)$arr_shadow_schedule_account['status']=="2") {
								// $str_description = '<span style="color:#2b9984;">Published</span>';
							// } else if ((string)$arr_shadow_schedule_account['status']=="3") {
								// $str_description = '<span style="color:#c12e2a;">Deleted</span>';
							// } else if ((string)$arr_shadow_schedule_account['status']=="5") {
								// $str_description = '<span style="color:#a94442;">(Need Reschedule)</span>';
							// }
							// echo $arr_shadow_schedule_account['username']." ".$str_description."<br>";
						// }
					// }
				?>

      </td>
			<?php 
				$description = $arr->description;
				$description = str_replace(chr(13),"<br>",$description);
			?>
      <td align="left">
        <div id="description" data-full-description="{{$description}}">
          <?php 
            if (strlen($description)>100) {
              echo mb_substr($description, 0, 100)."... ";
              echo '<a href="#" class="link-read-more">read more</a>';
            } else {
              echo $description;
            }
          ?>  
        </div>
      </td>
      <td align="center">
				{{$arr->created_at}}
      </td>
      <td align="center">
				<?php 
					if ($arr->status==1) {
						echo $arr->publish_at; 
					} else if ($arr->status>=2) {
						echo '<span style="color:#2b9984;">'.$arr->publish_at.'</span>'; 
					}
				?>
      </td>
      
      <td align="center">
				<?php 
					if ($arr->is_deleted) {
						echo '<span style="color:#c12e2a;">'.$arr->delete_at.'</span>'; 
					} else  {
						echo "-"; 
					}
				?>
      </td>
      <td>
				<?php if ( ($arr->status<2) || ( ($arr->status<3) && ($arr->is_deleted) ) ) { ?>
				<a class="btn btn-sm btn-danger" data-toggle="modal" href='#del1-{{$arr->id}}'>Delete</a>
				<div class="modal fade" id="del1-{{$arr->id}}">
						<div class="modal-dialog">
								<div class="modal-content">
										<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
												<h4 class="modal-title">Warning!</h4>
										</div>
										<div class="modal-body">
												Are you sure want to cancel this schedule?
										</div>
										<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
												<a href="{{ url('schedule/delete/'.$arr->id) }}" class="btn btn-primary">Yes</a>
										</div>
								</div>
						</div>
				</div>
				<?php } ?>
      </td>
      
    </tr>    
<?php 
    $i+=1;
  } 
?>

<script>
    // $('.zoom').elevateZoom({scrollZoom: true});

</script>