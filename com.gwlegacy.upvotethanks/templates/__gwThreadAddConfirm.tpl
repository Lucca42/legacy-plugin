{* Submit-time confirmation on the thread-add page when the first post will be locked.
   A capture-phase listener runs before WBB's own submit handler on the save button.
   The console line is a temporary diagnostic to confirm the footer injection fires. *}
<script data-relocate="true">
	(function () {
		var lock = {if $gwConfirmThreadLock|isset && $gwConfirmThreadLock}true{else}false{/if};
		console.log('GWUP-CONFIRM footer fired; threadLock=' + lock);
		if (!lock) {
			return;
		}
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
