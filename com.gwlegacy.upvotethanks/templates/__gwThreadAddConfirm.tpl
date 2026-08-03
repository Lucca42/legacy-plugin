{* Submit-time confirmation on the thread-add page when the first post will be locked.
   A capture-phase listener runs before WBB's own submit handler on the save button. *}
{if $gwConfirmThreadLock|isset && $gwConfirmThreadLock}
	<script data-relocate="true">
		(function () {
			document.addEventListener('click', function (event) {
				var target = event.target;
				if (!(target instanceof Element)) {
					return;
				}
				var button = target.closest('button[data-type="save"]');
				if (!button || button.closest('article')) {
					return;
				}
				if (!window.confirm('The first post of this thread will not be editable after posting. Please review it before continuing.')) {
					event.preventDefault();
					event.stopImmediatePropagation();
				}
			}, true);
		})();
	</script>
{/if}
