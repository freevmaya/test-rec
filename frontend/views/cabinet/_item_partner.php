<?
	use common\models\User;
	use common\models\User_settings;	
?>
<div class="card">
	<div class="partner-item card-body">	
		<a href="<?=$link?>"><div class="image" style="background-image: url(<?=$model->imageUrl()?>)"></div></a>
		<div class="detail">
			<h3 class="card-title"><?=$model->user->username?></h3>
			<div>
				<div><?=$model->user->email;?></div>
				<div><?=$model->phone;?></div>
			</div>
		</div>
	</div>
</div>