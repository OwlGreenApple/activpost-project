<?php 
use Celebpost\Models\ScheduleAccount;
$user = Auth::user();
?>

		@foreach ($schedules as $schedule)
		<div class="col-md-3 schedule-div col-xs-6 col-sm-6">
			@if (!empty($schedule->image))
				<!--<img src="{{$schedule->image}}" class="img-responsive schedule-image" data-zoom-image="{{$schedule->image}}" >-->
        <?php 
				//check jika diawali 
				if(strpos($arr->slug, 'PublishFile')===0){ 
					$file = $file.'.jpg';
				}				
				
				if($schedule->media_type=='photo') {
        ?>
				  <img src="{{'../vp/uploads/'.$user->username.'-'.$user->id.'/'.$file}}" class="img-responsive schedule-image" data-zoom-image="{{'../vp/uploads/'.$user->username.'-'.$user->id.'/'.$file}}" style="max-height:240px;">
        <?php 
				} else { ?>
          <video src="{{'../vp/uploads/'.$user->username.'-'.$user->id.'/'.$file}}" width="260" height="240" controls></video>
        <?php } ?>
			@endif
		</div>
		<div class="col-md-3 schedule-div col-xs-6 col-sm-6">
			<?php if ( $schedule->description <> "" ) { ?>
			<p>
				{{ str_limit($schedule->description, 30) }} 
			</p>
			<?php } ?>
			<p class="footer-schedule">
				<strong>Created : </strong>{{ date('M d, Y H:i',strtotime($schedule->created_at)) }} <br>
				<strong>Scheduled At : </strong>{{ date('M d, Y H:i',strtotime($schedule->publish_at)) }} <br>
				<strong>Deleted At : </strong><?php 
				if ($schedule->is_deleted) {
					echo date('M d, Y H:i',strtotime($schedule->delete_at)); 
				} else {
					echo "-";
				}
				?> <br>
				<?php 
					$scheduleAccounts = ScheduleAccount::select("accounts.*","schedule_account.status")
															->join("accounts","accounts.id","=","schedule_account.account_id")
															->where("schedule_id","=",$schedule->id)
															->whereDate("publish_at",Carbon::createFromFormat('Y-m-d H:i:s', $schedule->publish_at)->format('Y-m-d'))
															->get();
				?>
				<strong>User : </strong><br><?php 
					foreach ($scheduleAccounts as $scheduleAccount) {
						if ($scheduleAccount->status==5) {
							echo $scheduleAccount->username." <span style='color:#a94442;'>(Need Reschedule)</span><br>";
						} else {
							echo $scheduleAccount->username."<br>";
						}
					}
				?>
			</p>

			<?php if ($schedule->status<2) { 
              if(strpos($schedule->slug, 'StoryFile')===0){
      ?>
                <a class="btn btn-sm btn-info" href='{{url("schedule/edit-story/".$schedule->id)}}'>Edit Schedule</a>
      <?php
              } else if($schedule->media_type=='photo'){
      ?>
			          <a class="btn btn-sm btn-info" href='{{url("schedule/edit/".$schedule->id)}}'>Edit Schedule</a>
			<?php   } else { ?>
                <a class="btn btn-sm btn-info" href='{{url("schedule/edit-video/".$schedule->id)}}'>Edit Schedule</a>
      <?php   } 
            }
      ?>
			
			<?php if ( ($schedule->status<2) || ( ($schedule->status<3) && ($schedule->is_deleted) ) ) { ?>
			<a class="btn btn-sm btn-danger" data-toggle="modal" href='#del-{{$schedule->id}}'>Delete</a>
			<?php } ?>
			
			<div class="modal fade" id="del-{{$schedule->id}}">
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
											<a href="{{ url('schedule/delete/'.$schedule->id) }}" class="btn btn-primary">Yes</a>
									</div>
							</div>
					</div>
			</div>
			
		</div>
		@endforeach

