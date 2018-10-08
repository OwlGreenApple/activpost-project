@extends('layouts.app')

@section('content')
<script type="text/javascript">
	urlImage = "<?php 
		if ($url<>"") {
			echo $url; 
		} else if ($url=="") {
			echo "";
		}
	?>";
	// console.log(urlImage);
	function fillElement($el, text) {
		$el.val(text).trigger('input');
	}
	$(document).ready(function() {
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'GET',
          url: "<?php echo url('image-editor-pixie'); ?>",
          // data: $("#form-setting").serialize(),
          data: {
					},
          dataType: 'text',
          beforeSend: function()
          {
            $("#div-loading").show();
          },
          success: function(result) {
						// console.log(result);
						$("#container-pixie").html(result);
						
						$(".main-content").css("position","relative");
						$(".main-content").css("margin-top","-20px");
          }
      });
	});
</script>

<div class="container" id="container-pixie" >

</div>
@endsection
