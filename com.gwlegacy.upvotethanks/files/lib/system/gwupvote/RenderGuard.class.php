<?php

namespace wcf\system\gwupvote;

/**
 * Render-once helper. A single instance is assigned to the template; renderOnce()
 * returns true only the first time, so a fragment injected at a template event that
 * fires once per post still emits shared markup (e.g. a script) a single time.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class RenderGuard
{
    private bool $done = false;

    public function renderOnce(): bool
    {
        if ($this->done) {
            return false;
        }
        $this->done = true;

        return true;
    }
}
