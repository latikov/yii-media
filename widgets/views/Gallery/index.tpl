<div id="insertPanel{$this->id}">
	<div class="btn_r">
		{CHtml::link("Добавить", 'javascript:;', ['class'=>'btn btn-primary', 'id'=>"uploadFile{$this->id}"])}
	</div>
	<div style="float:left; width: 370px;" id="forLoading{$this->id}">
	</div>

	{widget name='common.extensions.plupload.Plupload' data=[
		'options' => array_merge($options,[
			'url' => "/media/si/?&type={$this->type}&gallery_id={$this->id}",
			'drop_element' => "insertPanel{$this->id}",
			'container'=>"insertPanel{$this->id}",
			'init' => [
				'FileUploaded' => "js: function(up, files, response){
					res = eval(\"(\" + response.response + \")\");
					if (res.status=='success')
					jQuery('#insertPanel{$this->id} .previewContainer').html(res.div);
					if (res.status=='error')
						alert(res.message);
					$('#forLoading{$this->id} span').addClass('text-success').removeClass('ajax-loader-appended');
				}",
				'UploadProgress' => "js: function(up, file)	{
					$('#forLoading{$this->id} span').html(file.percent + '%');
				}",
				'FilesAdded' => "js: function(up, files) {
					while (up.files.length > 1) {
						up.removeFile(up.files[0]);
					}
					up.start();
					$.each(files, function(i, file) {
						jQuery( '#forLoading{$this->id}').html(file.name +' ('+ plupload.formatSize(file.size) +') '+
								'<span class=\"ajax-loader-appended\">0%</span>');
					});
				}"
			]
		]),
		'id' => "uploaderFile{$this->id}",
		'button'=>"uploadFile{$this->id}"
	]}

	<div class="previewContainer preview{$this->id}" style="clear: both; padding-top: 10px;">
		{include "Gallery/list.tpl"}
	</div>
	<br style="clear: both;" />
</div>

<div class="clearfix3">&#160;</div>

{widget name='common.widgets.jui.PModalWindow' data=[
	'links'=>[
		".edit-gallery-photo"=>[
		'afterUpdate'=>"js:function() { $.fn.yiiListView.update('promo-gallery-list'); }"
	],
	'.thumbnail .updateImage'=>[
		'afterClose'=>"js:function() { $.fn.yiiListView.update('promo-gallery-list'); }"
	]
]
]}

{registerScript id="deleteImage" position=CClientScript::POS_READY}
$('body').delegate('a.deleteImage','click',function(){
	var link= $(this);
	if(confirm('Are you sure you want to delete this record?')) {
		jQuery.ajax({
			'type':'POST',
			'success':function(data) {
				var res = eval('(' + data + ')');
				jQuery('#insertPanel{$this->id} .previewContainer').html(res.div);
			},
			'url':link.attr('rel'),
			'cache':false
		});
		return false;
	} else {
		return false;
	}
});
{/registerScript}