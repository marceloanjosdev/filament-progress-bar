@php
    $pollingEnabled = (bool) config('filament-progress-bar.polling.enabled', true);
    $idleInterval = (int) config('filament-progress-bar.polling.idle_interval_ms', 5000);
    $activeInterval = (int) config('filament-progress-bar.polling.active_interval_ms', 1000);
    $endpoint = (string) config('filament-progress-bar.polling.route', '/filament-progress-bar/progress');

    $initialItems = app(\MarceloAnjosDev\FilamentProgressBar\Progress\ProgressManager::class)->all();

    $metaDisplay = (string) config('filament-progress-bar.display.meta', 'both');
@endphp

<div
    @if ($pollingEnabled)
        x-data="{
            items: @js($initialItems),
            endpoint: @js($endpoint),
            idleMs: {{ $idleInterval }},
            activeMs: {{ $activeInterval }},
            timer: null,
            currentMs: null,

            start(ms) {
                this.stop();
                this.currentMs = ms;
                this.timer = setInterval(() => this.refresh(), ms);
            },

            stop() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            },

            refresh() {
                fetch(this.endpoint, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                })
                    .then(r => r.json())
                    .then(data => {
                        this.items = Array.isArray(data.items) ? data.items : [];

                        const shouldBeActive = this.items.length > 0;
                        const targetMs = shouldBeActive ? this.activeMs : this.idleMs;

                        if (this.currentMs !== targetMs) {
                            this.start(targetMs);
                        }
                    })
                    .catch(() => {
                        if (this.currentMs !== this.idleMs) {
                            this.start(this.idleMs);
                        }
                    });
            },

            hasTotal(item) {
                return Number.isInteger(item?.total) && item.total > 0;
            },

            percent(item) {
                if (Number.isInteger(item?.percent)) {
                    return Math.max(0, Math.min(100, item.percent));
                }

                if (Number.isInteger(item?.current) && Number.isInteger(item?.total) && item.total > 0) {
                    return Math.max(0, Math.min(100, Math.round((item.current / item.total) * 100)));
                }

                return 0;
            },

            metaText(item) {
                if (! this.hasTotal(item)) {
                    return '';
                }

                const percent = this.percent(item);
                const current = item.current;
                const total = item.total;

                switch (this.metaDisplay) {
                    case 'percent':
                        return `${percent}%`;

                    case 'total':
                        return `${current}/${total}`;

                    case 'both':
                    default:
                        return `${current}/${total} · ${percent}%`;
                }
            },

            metaDisplay: @js($metaDisplay),

            init() {
                this.start(this.items.length > 0 ? this.activeMs : this.idleMs);
                this.refresh();
            }


        }"
    @endif
>
    <template x-if="items.length > 0">
        <div class="fpb-shell">
            <div class="fpb-wrap">
                <div class="fpb-list">
                    <template x-for="item in items" :key="item.key">
                        <div class="fpb-card">
                            <div class="fpb-top">
                                <div style="min-width: 0;">
                                    <div class="fpb-title" x-text="item.label ?? item.key"></div>

                                    <template x-if="item.message">
                                        <div class="fpb-subtitle" x-text="item.message"></div>
                                    </template>
                                </div>

                                <!-- Option C: hide meta when there is no total -->
                                <template x-if="hasTotal(item)">
                                    <div class="fpb-meta" x-text="metaText(item)"></div>
                                </template>
                            </div>

                            <div class="fpb-track">
                                <!-- determinate -->
                                <template x-if="hasTotal(item)">
                                    <div class="fpb-bar" :style="`width: ${percent(item)}%`"></div>
                                </template>

                                <!-- indeterminate -->
                                <template x-if="! hasTotal(item)">
                                    <div class="fpb-bar fpb-bar--indeterminate"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
