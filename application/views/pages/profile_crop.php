<form id="form1" name="form1" method="post" enctype="multipart/form-data">
	<div style="display:none;width:50px;height:50px;overflow:hidden;">
		<img src="<?php echo $avatar;?>" id="preview" alt="Preview" />
	</div>
	<div style="width:<?php echo $target_px;?>px;height:<?php echo $target_px;?>px;overflow:hidden;">
		<img src="<?php echo $avatar;?>" id="preview_big" alt="Preview" />
	</div>
	<img src="<?php echo $avatar;?>" id="target" alt="Avatar" width="<?php echo $avatar_w;?>px" height="<?php echo $avatar_h;?>px" />	
	<input type="hidden" id="x" name="x">
	<input type="hidden" id="y" name="y">
	<input type="hidden" id="w" name="w">
	<input type="hidden" id="h" name="h">
	<input type="hidden" name="subm"/>
	<input type="submit" name="submit" value="submit"/>
</form>
