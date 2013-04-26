<?php

class Profile extends Private_Controller {

public function __construct()
    {
        parent::__construct();
    }
	
	function index() {
		$this->load->library('form_validation');
		 if ($this->form_validation->run() == FALSE)
			{
							
			}
			else
			{
			

			
			if($_FILES['image']['name']!='') {
				//echo 'bubu';
				
				//if($query==1) {
				//echo 'bubu2';
				$this->load->helper('string');
				$file_name = random_string('alnum', 16);
				
				$ext = strrpos($_FILES['image']['name'], '.');
				$extension = substr($_FILES['image']['name'], $ext);
				
				while(file_exists("uploads/avatars_raw/".$file_name.$extension))
				{
					$file_name = random_string('alnum', 16);
				}
				//echo $file_name.$extension;
				$config['upload_path'] = './uploads/avatars_raw';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size']	= '10240';
				$config['max_width']  = '5120';
				$config['max_height']  = '5120';
				$config['file_name']  = $file_name;

				$this->load->library('upload', $config);

				if ( ! $this->upload->do_upload("image"))
				{
					//handle error

				}
				else
				{
					$this->load->library('image_lib'); 
					$config['image_library'] = 'gd2';
					$config['quality'] = '85%';
					$upload_info = $this->upload->data();
					$config['source_image']	= $this->path."avatars_raw/".$upload_info['file_name'];
					$config['new_image'] = $this->path."avatars_raw/resized_".$upload_info['file_name'];
					list($width, $height) = getimagesize($config['source_image']);
					$new_res = 850; //maksimalna rezoluciq predi crop
					if($width>$new_res || $height>$new_res) {
						if($width>$height) {
							$set_width = $new_res;
							$config['width']	 = $set_width;
							$config['height']	= round($set_width*($height/$width),0);
						}
						else
						{
							$set_height = $new_res;
							$config['width']	 = round($set_height*($width/$height),0);
							$config['height']	= $set_height;
						}
					}
					else
					{
						$config['width'] = $width;
						$config['height'] = $height;	 
					}
						
					$this->image_lib->initialize($config);
					if ( ! $this->image_lib->resize())
					{
						echo $this->image_lib->display_errors();
					}
					else
					{
					unlink("uploads/avatars_raw/".$upload_info['file_name']);
					//prenasochvane kam crop stranicata
					redirect(site_url()."/profile/crop/".$upload_info['file_name']);
					
					}
				}
				
			
			
			//}
			}
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			}
		$this->data['content'] = 'pages/profile';
		$this->load->view('template/template',$this->data);	
	}
	
	
	function crop($filename='') {
		$this->data['avatar'] = base_url()."uploads/avatars_raw/resized_".$filename;
		
		list($width, $height) = getimagesize($this->data['avatar']);
		
		if($width>840) { $this->data['avatar_w']=840; $this->data['avatar_h'] = 840*($height/$width); }
		else { $this->data['avatar_w']=$width; $this->data['avatar_h'] = $height; }
		
		if(min($width,$height)<150) { $target_px = min($width,$height); } else { $target_px = 150; }
		$this->data['target_px'] = $target_px;
		$this->data['filename'] = $filename;
		if (isset($_POST['subm']))
		{
	
$ext = strrpos($filename, '.');
$extension = substr($filename, $ext+1);
$targ_w = $target_px;
$targ_h = $target_px;
$jpeg_quality = 100;
$save_to = "uploads/avatars/".$filename;
$src = base_url()."uploads/avatars_raw/resized_".$filename;
if($extension=='jpeg'||$extension=='jpg') {
$img_r = imagecreatefromjpeg($src);
}
elseif($extension=='png') {
$img_r = imagecreatefrompng($src);
}
elseif($extension=='gif') {
$img_r = imagecreatefromgif($src);
}

$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);

if($extension=='jpeg'||$extension=='jpg') {
imagejpeg($dst_r, $save_to, $jpeg_quality);	
}
elseif($extension=='png') {
imagepng($dst_r, $save_to);
}
elseif($extension=='gif') {
imagegif($dst_r, $save_to);
}
$row = $this->User_model->get_profile($this->user_id);
$query = $this->User_model->update_avatar($this->user_id,$filename);
if($query==1) {
unlink("uploads/avatars_raw/resized_".$filename);
if($row->avatar!='') {
	unlink("uploads/avatars/".$row->avatar);
}
//success
redirect(site_url()."/success");	
}	
		}
		
		// JQTRANSFORM
		$this->data['head_addons'] = '<link rel="stylesheet" type="text/css" href="'.base_url().'scripts/jqtransform/jqtransform.css" />
<script type="text/javascript" src="'.base_url().'scripts/jqtransform/jquery.jqtransform.js"></script>
<script language="javascript">
		$(function(){
			$("form").jqTransform({imgPath:"'.base_url().'/scripts/jqtransform/img/"});
		});
</script>';

		// JCROP
		$this->data['head_addons'] .= '<link rel="stylesheet" type="text/css" href="'.base_url().'scripts/jcrop/jquery.Jcrop.css" />
<script type="text/javascript" src="'.base_url().'scripts/jcrop/jquery.Jcrop.min.js"></script>
<script type="text/javascript">

		jQuery(function($){

      // Create variables (in this scope) to hold the API and image size
      var jcrop_api, boundx, boundy;
      
      $("#target").Jcrop({
        onChange: updatePreview,
        onSelect: updatePreview,
		setSelect:   [ '.$target_px.', '.$target_px.', 0, 0 ],
		minSize:   [ '.$target_px.', '.$target_px.' ],
        aspectRatio: 1
      },function(){
        // Use the API to get the real image size
        var bounds = this.getBounds();
        boundx = bounds[0];
        boundy = bounds[1];
        // Store the API in the jcrop_api variable
        jcrop_api = this;
      });

      function updatePreview(c)
      {
        if (parseInt(c.w) > 0)
        {
          var rx = 50 / c.w;
          var ry = 50 / c.h;
		  var rx2 = '.$target_px.' / c.w;
          var ry2 = '.$target_px.' / c.h;

          $("#preview").css({
            width: Math.round(rx * boundx) + "px",
            height: Math.round(ry * boundy) + "px",
            marginLeft: "-" + Math.round(rx * c.x) + "px",
            marginTop: "-" + Math.round(ry * c.y) + "px"
          });
		  $("#preview_big").css({
            width: Math.round(rx2 * boundx) + "px",
            height: Math.round(ry2 * boundy) + "px",
            marginLeft: "-" + Math.round(rx2 * c.x) + "px",
            marginTop: "-" + Math.round(ry2 * c.y) + "px"
          });
        }
		$("#x").val(c.x);
		$("#y").val(c.y);
		$("#w").val(c.w);
		$("#h").val(c.h);
      };
	  
	
	  

    });
	
	

			function checkCoords()
			{
				if (parseInt($("#w").val())) return true;
				alert("Please select a crop region then press submit.");
				return false;
			};

</script>';
		$this->data['content'] = 'pages/profile_crop';
		$this->load->view('template/template', $this->data);
	
	
	}
	

}


?>