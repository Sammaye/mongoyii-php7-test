<?php 
$js=<<<JAVSCRIPT

$(function(){
	$('.comment a.delete').on('click',function(e){
		e.preventDefault();
		c=$(this).closest('.comment');
		
		$.ajax({
			url: $(this).attr('href'),
			dataType: 'json',
			type: 'POST'		
		}).done(function(data){
			if(data.success){
				c.remove(); // Take the comment away
			}else{
				// Some error handling here
			}
		});
	});
});

JAVSCRIPT;
Yii::app()->getClientScript()->registerScript('deleteComment', $js);

?>
<div class="comment">
	<div><?php echo CHtml::link($model->author->username, array('user/view','id'=>$model->author->_id)) ?> on <?php echo $model->create_time->toDateTime()->format('d/m/Y h:i:sa') ?>
		<?php if(Yii::app()->user->isAdmin()||(string)Yii::app()->user->id===(string)$model->userId){
			echo CHtml::link('Delete',array('comment/delete','id'=>$model->_id),array('class'=>'delete'));
		} ?>
	</div>
	<p><?php echo nl2br(CHtml::encode($model->body)) ?></p>
</div>