<ul class="thumbnails">
	{foreach $images as $image}
		<li class ="item">
			<div class="thumbnail">
				<a target="_blank" href="{$image->src}">
					<div style="width:150px;height:150px;background: url('{$image->src}') no-repeat;background-size: contain">
					</div>
				</a>
				<a class="deleteImage delete" rel="/media/si/delete/?id={$image->id}&gallery_id={$gallery->id}" href="javascript:;">
					<img src="/f/admin/i/blank.png" alt="">
				</a>
			</div>
		</li>
	{/foreach}
</ul>