<div class="comment">
	<div><?php echo CHtml::link($data->author->username, array('user/view','id'=>$data->author->_id)) ?> on <?php echo $data->create_time->toDateTime()->format('d/m/Y h:i:sa') ?></div>
	<p><?php echo nl2br(CHtml::encode($data->body)) ?></p>
	<p>In reply to: <?php echo CHtml::link($data->article->title, array('article/view','id'=>$data->article->_id)) ?></p>
</div>