<?php
$step=array_key_exists('step',$args)?$args['step']:1;
$steps=array(
	array(
		"num"=>1,
		"name"=>"入力"
	),
	array(
		"num"=>2,
		"name"=>"確認"
	),
	array(
		"num"=>3,
		"name"=>"完了"
	)
);
?>
		<div class="fSteps">
		<?php foreach($steps as $s):?>
          <div<?php if($s['num']==$step):?> class="on"<?php endif;?> id="st<?= $s['num'];?>"><span class="n en"><?= $s['num'];?></span><?= $s['name'];?></div>
		<?php endforeach;?>
        </div>