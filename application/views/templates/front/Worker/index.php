<section class="breadcrumbs">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center">
			<h2><?php echo $this->template->title; ?></h2>
			<ol>
				<li><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('header')['navbar']['home']; ?></a></li>
				<li><?php echo $this->template->title; ?></li>
			</ol>
		</div>
	</div>
</section>

<section id="worker">
	<div class="container">
		<?php echo form_open('', ['method' => 'get', 'id' => 'formFilter', 'autocomplete' => 'off', 'data-parsley-validate' => true]); ?>
			<div class="form-row">
				<div class="form-group col-md-3">
					<?php echo form_label('NIK', null); ?>
					<?php echo form_input(['type' => 'text', 'name' => 'nik', 'class' => 'form-control', 'value' => $this->input->get('nik') ? $this->input->get('nik') : '']); ?>
				</div>
				<div class="form-group col-md-3">
					<?php echo form_label('Gender', null); ?>
					<select name="gender" class="form-control select2">
						<option value="">Please Select</option>
						<option value="1">Male</option>
						<option value="2">Female</option>
					</select>
				</div>
				<div class="form-group col-md-3">
					<?php echo form_label('Status', null); ?>
					<select name="marital_status" class="form-control select2">
						<option value="">Please Select</option>
						<option value="1">Single</option>
						<option value="2">Married</option>
						<option value="3">Divorce</option>
					</select>
				</div>
				<div class="form-group col-md-3">
					<?php echo form_label('Placement', null); ?>
					<select name="placement" class="form-control select2">
						<option value="">Please Select</option>
						<?php foreach ($placements as $placement) {
							echo '<option value="' .$placement['id']. '">'. $placement['name']. '</option>';
						} ?>
					</select>
				</div>
				<div class="form-group col-md-6">
					<?php echo form_label('Experience', null); ?>
					<div class="d-flex flex-wrap">
						<?php foreach ($experiences as $experience) { ?>
							<div class="icheck-secondary mr-4">
								<?php echo form_checkbox(['name' => 'experience[]', 'id' => 'Experience' . $experience['id'], 'value' => $experience['id'], 'class' => 'asd']); ?>
								<?php echo form_label($experience['name'], 'Experience' . $experience['id']); ?>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="form-group col-md-6">
					<?php echo form_label('Ready For Placement', null); ?>
					<div class="d-flex flex-wrap">
						<?php foreach ($placements as $ready_placement) { ?>
							<div class="icheck-secondary mr-4">
								<?php echo form_checkbox(['name' => 'ready_placement[]', 'id' => 'ReadyPlacement' . $ready_placement['id'], 'value' => $ready_placement['id']]); ?>
								<?php echo form_label($ready_placement['name'], 'ReadyPlacement' . $ready_placement['id']); ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 text-right border-top mt-1 pt-2">
					<?php echo form_button(['type' => 'submit', 'class' => 'btn btn-secondary', 'content' => 'Search']); ?>
				</div>
			</div>
		<?php echo form_close(); ?>

		<div class="section-sub-title">
			<h5>Result</h5>
		</div>

		<div class="row">
			<?php foreach ($workers as $worker) { ?>
				<div class="col-md-4 mb-3">
					<div class="box">
						<div class="profile-photo">
							<img src="<?php echo @getimagesize(base_url('files/workers/'.$worker['id'].'/'.$worker['photo'])) ? base_url('files/workers/'.$worker['id'].'/'.$worker['photo']) : base_url('assets/img/default-avatar.jpg'); ?>" alt="Employees Photo" class="img-fluid">
						</div>
						<div class="profile-name">
							<h6><?php echo $worker['fullname']; ?></h6>
							<p>NIK: <?php echo $worker['nik']; ?></p>
						</div>
						<div class="profile-info match-height">
							<div class="flex-wrapper">
								<div>Gender</div>
								<div><?php echo !empty($worker['gender']) ? $worker['gender'] : '-'; ?></div>
							</div>
							<div class="flex-wrapper">
								<div>Status</div>
								<div><?php echo !empty($worker['marital_status']) ? $worker['marital_status'] : '-'; ?></div>
							</div>
							<div class="flex-wrapper">
								<div>Age</div>
								<div><?php echo !empty($worker['age']) ? $worker['age'] : '-'; ?></div>
							</div>
							<div class="flex-wrapper">
								<div>Placement</div>
								<div><?php echo !empty($worker['placement']) ? $worker['placement'] . ((!empty($worker['placement_status'])) ? ' (' . $worker['placement_status'] . ')' : '') : '-'; ?></div>
							</div>
							<div class="flex-wrapper">
								<div>Experience</div>
								<div><?php echo !empty($worker['experience']) ? implode(', ', (explode(',', $worker['experience']))) : '-'; ?></div>
							</div>
							<div class="flex-wrapper">
								<div>Ready For Placement</div>
								<div><?php echo !empty($worker['ready_placement']) ? implode(', ', (explode(',', $worker['ready_placement']))) : '-'; ?></div>
							</div>
						</div>
						<div class="profile-menu">
							<?php echo anchor('worker/detail/' . $worker['id'], 'View Detail', ['class' => 'btn btn-secondary']); ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<div class="page-center">
			<?php echo $pagination; ?>
		</div>
	</div>
</section>

<!-- load required builded stylesheet for this page -->
<?php $this->template->stylesheet->add('assets/vendor/select2/css/select2.min.css', ['type' => 'text/css']); ?>
<?php $this->template->stylesheet->add('assets/vendor/select2/css/select2-bootstrap4.min.css', ['type' => 'text/css']); ?>
<?php $this->template->stylesheet->add('assets/vendor/icheck-bootstrap/icheck-bootstrap.min.css', ['type' => 'text/css']); ?>

<!-- load required builded script for this page -->
<?php $this->template->javascript->add('assets/vendor/select2/js/select2.full.min.js'); ?>

<!-- script for this page -->
<script type="text/javascript">

	$(document).ready(function() {
		var paramExperience = '<?php echo$this->input->get('experience'); ?>',
			elemExperience = $('#formFilter [name="experience[]"');

		for (var x = 0; x < elemExperience.length; x++) {
			if($.inArray(elemExperience[x].value, paramExperience.split('-')) !== -1) {
				elemExperience.eq(x).attr('checked', true);
			}
		}

		var paramReadyPlacement = '<?php echo$this->input->get('ready_placement'); ?>',
			elemReadyPlacement = $('#formFilter [name="ready_placement[]"');

		for (var x = 0; x < elemReadyPlacement.length; x++) {
			if($.inArray(elemReadyPlacement[x].value, paramReadyPlacement.split('-')) !== -1) {
				elemReadyPlacement.eq(x).attr('checked', true);
			}
		}

		if ($('#formFilter [name="gender"]').find('option[value="<?php echo $this->input->get('gender'); ?>"]').length) {
			$('#formFilter [name="gender"]').val('<?php echo $this->input->get('gender'); ?>').trigger('change');
		}

		if ($('#formFilter [name="marital_status"]').find('option[value="<?php echo $this->input->get('marital_status'); ?>"]').length) {
			$('#formFilter [name="marital_status"]').val('<?php echo $this->input->get('marital_status'); ?>').trigger('change');
		}

		if ($('#formFilter [name="placement"]').find('option[value="<?php echo $this->input->get('placement'); ?>"]').length) {
			$('#formFilter [name="placement"]').val('<?php echo $this->input->get('placement'); ?>').trigger('change');
		}
	});

	$('#formFilter').on('submit', function(e) {
		e.preventDefault();

		var thisForm = $(this);
		var thisExperience = thisForm.find('[name="experience[]"');
		var valueExperience = [];
		var thisReadyPlacement = thisForm.find('[name="ready_placement[]"');
		var valueReadyPlacement = [];
		var xxx = 0;
		var yyy = 0;

		for (var xx = 0; xx < thisExperience.length; xx++) {
			if (thisExperience[xx].checked) {
				valueExperience[xxx] = thisExperience[xx].value;
				xxx++;
			}
		}

		thisForm.append('<input type="hidden" name="experience" value="' + valueExperience.join('-') +'">');
		thisExperience.attr({'disabled': true});

		for (var yy = 0; yy < thisReadyPlacement.length; yy++) {
			if (thisReadyPlacement[yy].checked) {
				valueReadyPlacement[yyy] = thisReadyPlacement[yy].value;
				yyy++;
			}
		}

		thisForm.append('<input type="hidden" name="ready_placement" value="' + valueReadyPlacement.join('-') +'">');
		thisReadyPlacement.attr({'disabled': true});

		thisForm.find('[type="submit"]').attr({'disabled': true});

		e.currentTarget.submit();
	});
</script>