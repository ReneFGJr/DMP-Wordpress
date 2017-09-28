<?php
global $data;
?>
<form method="post">
	<input type="hidden" name="id_tp" value="<?php echo $data['id_tp']; ?>">	
	<div class="container-fluid" id="templat_form" style="<?php echo $data['style']; ?>">
		<div class="row" style="border-left: 5px #8080FF solid;">
			<div class="col-md-2">
				<label for="templat_name"><?php echo msg('templat_id'); ?></label>
				<input type="text" id="ola" class="form-control" name="tp_id" value="<?php echo $data['tp_id']; ?>"  aria-describedby="emailHelp" placeholder="$templat_id">
				<small id="emailHelp" class="form-text text-muted">Templat ID</small>
			</div>
			
			<div class="col-md-10">
				<label for="templat_name"><?php echo msg('templat_name'); ?></label>
				<input type="text" class="form-control" name="tp_name" value="<?php echo $data['tp_name']; ?>" aria-describedby="emailHelp" placeholder="$templat_name">
				<small id="emailHelp" class="form-text text-muted">Templat name</small>
			</div>

			<div class="col-md-12">
				<label for="templat_description"><?php echo msg('templat_description'); ?></label>
				<textarea class="form-control" name="tp_descr" aria-describedby="emailHelp" placeholder="$templat_description"><?php echo $data['tp_descr']; ?></textarea>
				<small id="emailHelp" class="form-text text-muted">Templat description</small>
			</div>
			
			<div class="col-md-11">
				<input type="submit" value="<?php echo $data['save']; ?>" name="action" class="btn btn-default">
			</div>
		</div>
	</div>
</form>

<script>
    jQuery("#templat_new").click(function() {
            jQuery("#templat_form").toggle("slow");
        });

</script>

