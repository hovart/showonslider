<h2>Checked Products</h2>
{if $images}

<ul id="bxslider">
{foreach from=$images key=key item=image}
  	<li>
		<a href="http://www.prestashop.com" title="This is a sample picture">
		<img src="{$image['image_url']}" height="124" width="124" />
		</a>
	</li>
{/foreach}			
</ul>

{else}
<p>No checked Products found</p>
{/if}