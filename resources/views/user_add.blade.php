<div class="container">
    <div class="row">
        <div class="col-md-6" >

<form action="/calendar/<?php echo $scheduleData['calendar_id'] ?>/schedule-element/user-add" method="POST" id="schedule-cal-ctrl-form">

	<?php echo Form::token(); ?>

	<div class="form-group">
		<label for="scheduled_date">scheduled_date</label>
		<input class="form-control" name="scheduled_date" type="text" id="scheduled-date-field" class="date-picker" value="{{$scheduleData['json_data']['scheduled_date'] or ''}}">
	</div>


	<div class="form-group">
		<label for="worker_id">worker_id</label>
 		<?php echo Form::select('worker_id',$scheduleData['workerList'] , null, ['placeholder' => 'pick a worker...', 'class' => 'form-control']); ?>
	</div>


	<?php foreach (  $scheduleData['displayFields'] as $f ): ?>


	<?php if ( $f == 'schedule_id'  ||  $f == 'scheduled_date'  || $f == 'worker_id') continue; ?>
		<div class="form-group">
			<label for="{{$f}}">{{$f}}</label>
			<input class="form-control" name="{{$f}}" type="text" id="{{$f}}" value="{{$scheduleData['json_data'][$f] or ''}}">
		</div>
	<?php endforeach; ?>

</form>

    </div>
</div>
