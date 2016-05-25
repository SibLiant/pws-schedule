<div class="container">
    <div class="row">
        <div class="col-md-6" >


	<div class="form-group">
		<label for="tag_id">tag_id</label>
 		<?php echo Form::select('tag_id',$tagData['availableTags'] , null, ['placeholder' => 'pick a tag...', 'class' => 'form-control', 'id' => 'tag-select']); ?>
	</div>

	<a href="#" class="btn btn-primary" id="btn-add-tag">Add Tag</a>


	<ul id="existing-tags">
	</ul>


		</div>
    </div>
</div>
