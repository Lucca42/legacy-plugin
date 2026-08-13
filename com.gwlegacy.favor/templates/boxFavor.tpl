{* Favor of the Gods box. Status and countdown are computed client-side from our own
   status endpoint (fed by the cron); the countdown ticks locally between refreshes. *}
<div class="gwFavorBox">
	<p id="gwFavorStatus">Loading&hellip;</p>
	<small class="gwFavorCredit" style="display: block;">Data from <a href="https://www.gwtoolbox.com/" rel="noopener noreferrer" target="_blank">gwtoolbox.com</a></small>
	{if $loggedIn}
		<div class="gwFavorSubscribeRow" style="margin-top: 10px;">
			<button type="button" id="gwFavorSubscribe" class="button small">Register notification</button>
		</div>
	{/if}
</div>

{literal}
<script data-relocate="true">
	require(['WoltLabSuite/Core/Ajax'], (Ajax) => {
		const MAX = 20, STALE = 900;
		const statusEl = document.getElementById('gwFavorStatus');
		const subBtn = document.getElementById('gwFavorSubscribe');
		let favor = null;

		const fmt = (s) => {
			const h = Math.floor(s / 3600), m = Math.floor(s / 60) % 60;
			return h ? `${h}h ${String(m).padStart(2, '0')}m` : `${m}m ${String(s % 60).padStart(2, '0')}s`;
		};
		const render = () => {
			if (!favor) { statusEl.textContent = 'Favor unknown'; return; }
			const age = Math.floor(Date.now() / 1000) - favor.updatedAt;
			if (age > STALE) { statusEl.textContent = 'Favor unknown'; return; }
			const left = favor.minutesRemaining * 60 - age;
			if (left > 0) statusEl.textContent = 'Favor of the Gods ' + fmt(left);
			else if (favor.minutesRemaining) statusEl.textContent = 'No favor';
			else statusEl.textContent = 'No favor ' + (MAX - favor.achievementsNeeded) + '/' + MAX;
		};
		const setSub = (on) => { if (subBtn) subBtn.textContent = on ? 'Unregister notification' : 'Register notification'; };

		const refresh = async () => {
			try {
				const r = await Ajax.dboAction('getStatus', 'wcf\\data\\favor\\subscription\\SubscriptionAction').dispatch();
				favor = r.favor || null;
				setSub(!!r.subscribed);
				render();
			} catch (e) {}
		};

		subBtn?.addEventListener('click', async () => {
			subBtn.disabled = true;
			try {
				const r = await Ajax.dboAction('toggle', 'wcf\\data\\favor\\subscription\\SubscriptionAction').dispatch();
				setSub(!!r.subscribed);
			} finally {
				subBtn.disabled = false;
			}
		});

		refresh();
		setInterval(refresh, 60000);
		setInterval(render, 1000);
	});
</script>
{/literal}
