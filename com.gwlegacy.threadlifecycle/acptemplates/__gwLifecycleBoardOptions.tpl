{* Per-board thread lifecycle settings, injected into the board ACP add/edit form. *}
<dl>
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" name="gwLifecycleEnabled" value="1"{if $gwLifecycleEnabled} checked{/if}>
			Thread lifecycle (self-close &amp; auto-close)
		</label>
		<small>Enables the self-close button and auto-close for this board.</small>
	</dd>
</dl>
<dl>
	<dt><label for="gwSelfCloseDelay">Self-close after (seconds)</label></dt>
	<dd>
		<input type="number" id="gwSelfCloseDelay" name="gwSelfCloseDelay" value="{$gwSelfCloseDelay}" min="0" class="short">
		<small>Minimum thread age before the author may close their own thread. 0 disables the self-close button.</small>
	</dd>
</dl>
<dl>
	<dt><label for="gwAutoCloseAfter">Auto-close after inactivity (seconds)</label></dt>
	<dd>
		<input type="number" id="gwAutoCloseAfter" name="gwAutoCloseAfter" value="{$gwAutoCloseAfter}" min="0" class="short">
		<small>A thread with no activity for this long is closed automatically. 0 disables auto-close. (7776000 = 90 days.)</small>
	</dd>
</dl>
