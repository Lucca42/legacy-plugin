{include file='header'}

{literal}
<style>
	/* Everything comes from the active style's variables, so the page follows the site
	   theme (light or dark) without a color of its own. The accent is the primary button
	   color, the brown of the Legacy style. */
	.gwHom { --gwAccent: var(--wcfButtonPrimaryBackground); --gwAccentText: var(--wcfButtonPrimaryText); --gwSoft: var(--wcfSidebarBackground); --gwLine: var(--wcfContentBorderInner); --gwDim: var(--wcfContentDimmedText); }
	.gwHomSearch { display: flex; gap: 8px; max-width: 460px; }
	.gwHomSearch input { flex: 1; min-width: 0; }
	.gwHomNotice { margin-top: 12px; }
	.gwHomMine { margin-top: 12px; display: flex; flex-wrap: wrap; gap: 8px 12px; align-items: center; color: var(--gwDim); }
	.gwHomMine p { margin: 0; }

	.gwHomHero { text-align: center; padding: 10px 0 24px; }
	.gwHomEyebrow { font-size: 12px; letter-spacing: .18em; text-transform: uppercase; color: var(--gwAccent); font-weight: 600; margin: 0 0 6px; }
	.gwHomRank { font-weight: 600; font-size: clamp(24px, 4vw, 34px); margin: 0; line-height: 1.2; color: var(--wcfContentHeadlineText); }
	.gwHomRank.gwHomNone { color: var(--gwDim); font-weight: 500; font-size: clamp(18px, 3vw, 24px); }
	.gwHomChar { color: var(--gwDim); font-size: 15px; margin: 2px 0 16px; }
	.gwHomScore { font-size: 44px; font-weight: 600; line-height: 1; color: var(--gwAccent); font-variant-numeric: tabular-nums; }
	.gwHomScore small { font-size: 18px; font-weight: 500; color: var(--gwDim); }
	.gwHomScoreLabel { font-size: 12px; letter-spacing: .16em; text-transform: uppercase; color: var(--gwDim); margin-top: 4px; }
	.gwHomMeter { display: flex; gap: 3px; justify-content: center; margin: 16px auto 0; max-width: 420px; }
	.gwHomMeter i { flex: 1; height: 12px; border-radius: 2px; background: var(--gwLine); }
	.gwHomMeter i.gwLit { background: var(--gwAccent); }
	.gwHomMeter i.gwMilestone { height: 18px; margin-top: -3px; }
	.gwHomMissingLine { margin: 14px 0 0; color: var(--gwDim); }
	.gwHomShare { margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; align-items: center; }
	.gwHomShare p { margin: 0; width: 100%; color: var(--gwDim); font-size: 13px; }
	.gwHomShare p:empty { display: none; }

	.gwHomMonuments { display: grid; gap: 16px; margin-bottom: 16px; }
	.gwHomMon { background: var(--wcfContentContainerBackground); border: 1px solid var(--gwLine); border-radius: var(--wcfBorderRadius, 3px); padding: 20px; }
	.gwHomMonHead { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
	.gwHomMonHead h3 { font-weight: 600; font-size: 20px; margin: 0; color: var(--wcfContentHeadlineText); }
	.gwHomPts { font-size: 13px; font-weight: 600; color: var(--gwAccent); border: 1px solid var(--gwAccent); border-radius: var(--wcfBorderRadius, 3px); padding: 1px 8px; font-variant-numeric: tabular-nums; }
	.gwHomPts.gwMax { background: var(--gwAccent); color: var(--gwAccentText); }
	.gwHomDetailBtn { margin-left: auto; }
	.gwHomMonSum { color: var(--gwDim); margin: 4px 0 14px; }
	.gwHomNiches { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 14px; }
	.gwHomNiche { width: 8px; height: 18px; border-radius: 4px 4px 1px 1px; border: 1px solid var(--gwLine); background: var(--gwSoft); }
	.gwHomNiche.gwFilled { background: var(--gwAccent); border-color: var(--gwAccent); }
	.gwHomNiche.gwRetired { border-style: dotted; background: transparent; opacity: .6; }
	.gwHomSplit { display: grid; grid-template-columns: 1fr 260px; gap: 24px; align-items: start; }
	.gwHomItems { columns: 2; column-gap: 24px; margin: 0; padding: 0; list-style: none; }
	.gwHomItems li { break-inside: avoid; padding: 1px 0; }
	.gwHomItems li::before { content: "\2726"; color: var(--gwAccent); margin-right: 8px; font-size: 10px; }
	.gwHomEmpty { color: var(--gwDim); font-style: italic; margin: 0; }
	.gwHomTally { display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 14px; }
	.gwHomTally b { display: block; font-size: 26px; font-weight: 600; color: var(--gwAccent); line-height: 1.1; }
	.gwHomTally span { font-size: 12px; letter-spacing: .1em; text-transform: uppercase; color: var(--gwDim); }
	.gwHomRules { list-style: none; margin: 0; padding: 12px 14px; font-size: 13px; background: var(--gwSoft); border-radius: var(--wcfBorderRadius, 3px); }
	.gwHomRules li { display: flex; align-items: baseline; gap: 8px; color: var(--gwDim); padding: 2px 0; }
	.gwHomRules li.gwDone { color: var(--wcfContentText); }
	.gwHomRules .gwMark { width: 12px; flex: none; color: var(--gwLine); }
	.gwHomRules li.gwDone .gwMark { color: var(--gwAccent); }
	.gwHomRules .gwMid { flex: 1; min-width: 0; }
	.gwHomRules .gwP { margin-left: auto; font-size: 12px; color: var(--gwDim); font-variant-numeric: tabular-nums; }
	.gwHomRules li.gwDone .gwP { color: var(--gwAccent); font-weight: 600; }
	.gwHomBar { height: 3px; background: var(--gwLine); margin-top: 3px; border-radius: 2px; }
	.gwHomBar i { display: block; height: 100%; background: var(--gwAccent); opacity: .5; border-radius: 2px; }

	.gwHomTrack { background: var(--wcfContentContainerBackground); border: 1px solid var(--gwLine); border-radius: var(--wcfBorderRadius, 3px); padding: 20px; }
	.gwHomTrack h3 { font-weight: 600; font-size: 20px; margin: 0 0 2px; color: var(--wcfContentHeadlineText); }
	.gwHomTrack > p { color: var(--gwDim); margin: 0 0 14px; }
	.gwHomRow { display: grid; grid-template-columns: 42px 1fr; gap: 14px; padding: 8px 0; border-top: 1px solid var(--gwLine); }
	.gwHomRow:first-of-type { border-top: 0; }
	.gwHomRow .gwN { font-size: 13px; color: var(--gwDim); text-align: right; padding-top: 2px; font-variant-numeric: tabular-nums; }
	.gwHomRow.gwGot .gwN { color: var(--gwAccent); font-weight: 600; }
	.gwHomRow .gwName { color: var(--gwDim); }
	.gwHomRow.gwGot .gwName { color: var(--wcfContentText); font-weight: 500; }
	.gwHomRow .gwDesc { color: var(--gwDim); font-size: 13px; }
	.gwHomRow.gwTitle { background: var(--gwSoft); margin: 0 -20px; padding: 8px 20px; }
	.gwHomRow .gwCrown { font-size: 11px; letter-spacing: .14em; text-transform: uppercase; color: var(--gwDim); }
	.gwHomRow.gwGot .gwCrown { color: var(--gwAccent); }
	.gwHomRow .gwBig { font-size: 17px; font-weight: 600; }

	.gwHomDialog { border: 1px solid var(--gwLine); border-radius: var(--wcfBorderRadius, 3px); background: var(--wcfContentContainerBackground); color: var(--wcfContentText); padding: 0; max-width: 720px; width: calc(100% - 40px); max-height: 82vh; }
	.gwHomDialog::backdrop { background: rgba(0, 0, 0, .5); }
	.gwHomDlgHead { display: flex; align-items: center; gap: 14px; padding: 16px 20px 12px; border-bottom: 1px solid var(--gwLine); }
	.gwHomDlgHead h3 { font-weight: 600; font-size: 20px; margin: 0; color: var(--wcfContentHeadlineText); }
	.gwHomDlgHead p { margin: 0; color: var(--gwDim); font-size: 13px; }
	.gwHomDlgHead .button { margin-left: auto; }
	.gwHomDlgBody { padding: 14px 20px 20px; overflow-y: auto; max-height: calc(82vh - 70px); }
	.gwHomDlgBody h5 { margin: 12px 0 2px; font-size: 12px; letter-spacing: .12em; text-transform: uppercase; color: var(--gwDim); font-weight: 600; }
	.gwHomDlgBody h5:first-child { margin-top: 0; }
	.gwHomDlgBody p { color: var(--gwDim); margin: 0; }
	.gwHomDlgBody p + p { margin-top: 10px; }
	.gwHomDlgBody ul { columns: 2; column-gap: 26px; margin: 6px 0 0; padding: 0; list-style: none; }
	.gwHomDlgBody li { break-inside: avoid; padding: 1px 0; }
	.gwHomDlgBody li::before { content: "\00b7"; color: var(--gwDim); margin-right: 9px; }

	@media (max-width: 640px) {
		.gwHomSplit { grid-template-columns: 1fr; }
		.gwHomItems, .gwHomDlgBody ul { columns: 1; }
		.gwHomMon, .gwHomTrack { padding: 16px; }
		.gwHomRow.gwTitle { margin: 0 -16px; padding: 8px 16px; }
	}
</style>
{/literal}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">Hall of Monuments</h1>
		<p class="contentHeaderDescription">What a Guild Wars character leaves behind, and what it unlocks in Guild Wars 2.</p>
	</div>
</header>

<div class="gwHom">
<section class="section">
	<form id="gwHomForm" class="gwHomSearch" method="get" action="#" data-link="{link controller='Hom' application='wcf'}character=__NAME__{/link}">
		<input type="text" id="gwHomName" class="long" maxlength="20" placeholder="Character name" value="{if $characterName}{$characterName}{/if}" autocomplete="off" spellcheck="false" autofocus>
		<button type="submit" class="button buttonPrimary">Read</button>
	</form>
	{if $invalidName}
		<p class="error gwHomNotice">That is not a Guild Wars character name. Letters and spaces only, 3 to 20 characters.</p>
	{elseif $notFound}
		<p class="error gwHomNotice">No character named <strong>{$characterName}</strong>. Check the spelling, names are case sensitive.</p>
	{elseif $lookupFailed}
		<p class="error gwHomNotice">The Hall of Monuments service did not answer. Try again in a minute.</p>
	{/if}
	{if $__wcf->user->userID && $myBadge && !$profile}
		<div class="gwHomMine" id="gwHomBadge">
			<p>On your profile: <strong>{$myBadge[characterName]}</strong>{if $myBadge[title]}, {$myBadge[title]}{/if}, {$myBadge[points]} / 50</p>
			<a href="{link controller='Hom' application='wcf'}character={$myBadge[characterName]|rawurlencode}{/link}" class="button small">View</a>
			<button type="button" id="gwHomUnpublish" class="button small">Remove from my profile</button>
			<p id="gwHomBadgeMsg"></p>
		</div>
	{/if}
</section>

{if $profile}
	{if $profile->empty}
		<section class="section">
			<p class="info"><strong>{$characterName}</strong> exists but has no statue dedicated yet. A hall is published once the character logs out of the game.</p>
		</section>
	{else}
		<section class="section gwHomHero">
			<p class="gwHomEyebrow">Hall of Monuments</p>
			{if $profile->rank}
				<h2 class="gwHomRank">{$profile->rank}</h2>
			{else}
				<h2 class="gwHomRank gwHomNone">No title yet</h2>
			{/if}
			<p class="gwHomChar">{$characterName}</p>
			<div class="gwHomScore">{$profile->total}<small>/50</small></div>
			<div class="gwHomScoreLabel">reward points</div>
			<div class="gwHomMeter">{foreach from=$profile->meter item=step}<i class="{if $step[lit]}gwLit{/if}{if $step[milestone]} gwMilestone{/if}"></i>{/foreach}</div>
			<p class="gwHomMissingLine">
				{if $profile->missingTotal}{$profile->missingTotal} statue{if $profile->missingTotal > 1}s{/if} across the account still undedicated, open a monument for the list.{else}Every statue on the account is dedicated. Nothing left.{/if}
			</p>
			<div class="gwHomShare" id="gwHomBadge" data-character="{$characterName}">
				{if $__wcf->user->userID}
					<button type="button" id="gwHomPublish" class="button small buttonPrimary">
						{if $myBadge && $myBadge[characterName] == $characterName}Update my Legacy profile{elseif $myBadge}Share {$characterName} on my Legacy profile instead{else}Share on my Legacy profile{/if}
					</button>
					{if $myBadge}
						<button type="button" id="gwHomUnpublish" class="button small">Remove from my profile</button>
					{/if}
					<p id="gwHomBadgeMsg">{if $myBadge && $myBadge[characterName] != $characterName}Your profile currently shows {$myBadge[characterName]}.{/if}</p>
				{else}
					<a href="{link controller='Login' application='wcf'}{/link}" class="button small">Log in to share on your Legacy profile</a>
				{/if}
			</div>
		</section>

		<div class="gwHomMonuments">
			{foreach from=$profile->monuments key=key item=monument}
				<section class="gwHomMon">
					<div class="gwHomMonHead">
						<h3>{$monument[title]}</h3>
						<span class="gwHomPts{if $monument[max]} gwMax{/if}">{$monument[total]} / {$monument[possible]}</span>
						<button type="button" class="button small gwHomDetailBtn" data-key="{$key}">{if $monument[missing]|count}{$monument[missing]|count} missing{else}Details{/if}</button>
					</div>
					<p class="gwHomMonSum">{$monument[summary]}</p>
					<div class="gwHomSplit">
						<div>
							{if $key == 'devotion'}
								<div class="gwHomTally">
									{foreach from=$monument[tally] key=rarity item=n}<div><b>{$n}</b><span>{$rarity}</span></div>{/foreach}
								</div>
							{/if}
							<div class="gwHomNiches">{foreach from=$monument[niches] item=niche}<i class="gwHomNiche{if $niche[retired]} gwRetired{elseif $niche[filled]} gwFilled{/if}"></i>{/foreach}</div>
							{if $key != 'devotion'}
								{if $monument[owned]|count}
									<ul class="gwHomItems">{foreach from=$monument[owned] item=name}<li>{$name}</li>{/foreach}</ul>
								{else}
									<p class="gwHomEmpty">No statue dedicated.</p>
								{/if}
							{/if}
						</div>
						<ul class="gwHomRules">
							{foreach from=$monument[rules] item=rule}
								<li{if $rule[earned]} class="gwDone"{/if}>
									<span class="gwMark">{if $rule[earned]}&#10022;{else}&middot;{/if}</span>
									<span class="gwMid">{$rule[label]}{if !$rule[earned] && $rule[percent]}<div class="gwHomBar"><i style="width: {$rule[percent]}%;"></i></div>{/if}</span>
									<span class="gwP">+{$rule[points]}</span>
								</li>
							{/foreach}
						</ul>
					</div>
				</section>
			{/foreach}
		</div>

		<section class="gwHomTrack">
			<h3>What this unlocks in Guild Wars 2</h3>
			<p>{if $profile->total >= 30}All 30 items are earned. Past that, each tier grants a title only.{else}{$profile->itemsUnlocked} item{if $profile->itemsUnlocked != 1}s{/if} out of 30, plus a title every 5 points.{/if}</p>
			{foreach from=$profile->track item=row}
				<div class="gwHomRow{if $row[unlocked]} gwGot{/if}{if $row[title]} gwTitle{/if}">
					<div class="gwN">{$row[points]}</div>
					<div>
						{if $row[items]|count}
							<div class="gwName">{$row[names]}</div>
							<div class="gwDesc">{$row[descs]}</div>
						{/if}
						{if $row[title]}
							<div class="gwCrown">{if $row[items]|count}and the title{else}reward title{/if}</div>
							<div class="gwName gwBig">{$row[title]}</div>
						{/if}
					</div>
				</div>
			{/foreach}
		</section>

		<dialog class="gwHomDialog" id="gwHomDialog">
			<div class="gwHomDlgHead">
				<div>
					<h3 id="gwHomDlgTitle"></h3>
					<p id="gwHomDlgSub"></p>
				</div>
				<button type="button" class="button small" id="gwHomDlgClose">Close</button>
			</div>
			<div class="gwHomDlgBody">
				{foreach from=$profile->monuments key=key item=monument}
					<div data-key="{$key}" data-title="{$monument[title]}" data-sub="{if $key == 'devotion'}Four counters, no list{elseif $monument[missing]|count}{$monument[missing]|count} of {$monument[pool]} not dedicated{else}Complete, every statue dedicated{/if}" hidden>
						{if $key == 'devotion'}
							<p>{$monument[count]} miniature{if $monument[count] != 1}s{/if} dedicated: {$monument[tally][common]} common, {$monument[tally][uncommon]} uncommon, {$monument[tally][rare]} rare, {$monument[tally][unique]} unique.</p>
							<p>The hall only records these four counts, never which miniatures they were. Only the in-game view at Kimmes the Historian lists them.</p>
						{elseif !$monument[missing]|count}
							<p>Complete, every statue dedicated.</p>
						{elseif $monument[missingGroups]|count}
							{foreach from=$monument[missingGroups] key=family item=types}
								<h5>{$family}, {$types|count}</h5>
								<ul>{foreach from=$types item=type}<li>{$type}</li>{/foreach}</ul>
							{/foreach}
						{else}
							<ul>{foreach from=$monument[missing] item=name}<li>{$name}</li>{/foreach}</ul>
						{/if}
						{if $key == 'honor' && $profile->retiredHonor}
							<p>One statue sits in a slot whose title was removed from the game. It is counted in the hall but has no name here.</p>
						{/if}
					</div>
				{/foreach}
			</div>
		</dialog>
	{/if}
{/if}
</div>

{literal}
<script data-relocate="true">
	require(['WoltLabSuite/Core/Ajax'], (Ajax) => {
		const form = document.getElementById('gwHomForm');
		const input = document.getElementById('gwHomName');
		form.addEventListener('submit', (event) => {
			event.preventDefault();
			const name = input.value.trim().replace(/\s+/g, ' ');
			if (!name) return;
			window.location.href = form.dataset.link.replace('__NAME__', encodeURIComponent(name));
		});

		const dialog = document.getElementById('gwHomDialog');
		if (dialog) {
			document.querySelectorAll('.gwHomDetailBtn[data-key]').forEach((button) => {
				button.addEventListener('click', () => {
					dialog.querySelectorAll('.gwHomDlgBody [data-key]').forEach((body) => {
						const on = body.dataset.key === button.dataset.key;
						body.hidden = !on;
						if (on) {
							document.getElementById('gwHomDlgTitle').textContent = body.dataset.title;
							document.getElementById('gwHomDlgSub').textContent = body.dataset.sub;
						}
					});
					dialog.querySelector('.gwHomDlgBody').scrollTop = 0;
					if (!dialog.open) dialog.showModal();
				});
			});
			document.getElementById('gwHomDlgClose').addEventListener('click', () => dialog.close());
			dialog.addEventListener('click', (event) => {
				const r = dialog.getBoundingClientRect();
				if (event.clientX < r.left || event.clientX > r.right || event.clientY < r.top || event.clientY > r.bottom) dialog.close();
			});
		}

		const badge = document.getElementById('gwHomBadge');
		if (!badge) return;
		const msg = document.getElementById('gwHomBadgeMsg');
		const action = async (name, payload) => {
			try {
				const r = await Ajax.dboAction(name, 'wcf\\data\\hom\\badge\\HomBadgeAction').payload(payload).dispatch();
				if (r.ok) window.location.reload();
				else msg.textContent = r.error || 'Something went wrong.';
			} catch (e) {
				msg.textContent = 'Something went wrong.';
			}
		};
		document.getElementById('gwHomPublish')?.addEventListener('click', (event) => {
			event.currentTarget.disabled = true;
			action('publish', { character: badge.dataset.character });
		});
		document.getElementById('gwHomUnpublish')?.addEventListener('click', (event) => {
			event.currentTarget.disabled = true;
			action('unpublish', {});
		});
	});
</script>
{/literal}

{include file='footer'}
