{* Hide the edit button on the locked first post (server-side already rejects the edit;
   this removes the button so regular users are not offered a dead action). *}
{if $gwUpvoteLockedEdit|isset && $gwUpvoteLockedEdit|count}
	<script data-relocate="true">
		(function () {
			var ids = [{implode from=$gwUpvoteLockedEdit item=gwId}{@$gwId}{/implode}];
			ids.forEach(function (pid) {
				var post = document.querySelector('[data-post-id="' + pid + '"]');
				if (!post) {
					return;
				}
				var button = post.querySelector('.jsMessageEditButton');
				if (button) {
					(button.closest('li') || button).style.display = 'none';
				}
			});
		})();
	</script>
{/if}
