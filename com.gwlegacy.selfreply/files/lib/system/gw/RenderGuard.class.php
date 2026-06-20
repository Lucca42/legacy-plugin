<?php

namespace wcf\system\gw;

/**
 * Tiny render-once helper. A single instance is assigned to the template; calling
 * renderOnce() returns true the first time only, so a fragment injected at a template
 * event that fires more than once is still rendered once.
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
