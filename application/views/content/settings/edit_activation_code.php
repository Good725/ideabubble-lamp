<h2>Edit code</h2>

<?=Form::open('', array('method'=>'POST','class'=>'form-horizontal')); ?>
  
  <div class="form-group">
    <?=Form::label('file', 'Code', array('class'=>'col-sm-2 control-label')); ?>
    <div class="col-sm-7">
      <?=Form::input('code', $code['code'], array('required','class'=>'form-control')); ?>
    </div>
  </div>
  
  <div class="form-group">
    <?=Form::label('group_id', '', array('class'=>'col-sm-2 control-label')); ?>
    <div class="col-sm-7">
      <?=Form::select('group_id', Arr::merge(array(null=>'- Choose group -'), $groups), $code['group_id'], array('required','class'=>'form-control')); ?>
    </div>
  </div>

<div class="form-group">
    <?=Form::label('role_id', '', array('class'=>'col-sm-2 control-label')); ?>
    <div class="col-sm-7">
      <?=Form::select('role_id', Arr::merge(array(null=>'- Choose role -'), $roles), $code['role_id'], array('required','class'=>'form-control')); ?>
    </div>
  </div>
  
  <div class="form-actions">
    <?=Form::button(NULL, 'Update', array('class'=>'btn btn-primary', 'id'=>'import')); ?> 
      <a href="<?=URL::base().'admin/settings/list_userreq_codes'?>" class="btn">Back to list</a>
  </div>
  
  <?=Form::close(); ?>
