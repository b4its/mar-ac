<div
    x-data="{
        theme: @entangle('theme').live,
        apply() {
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
        },
        persist() {
            localStorage.setItem('theme', this.theme);
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: this.theme }));
        },
        set(value) {
            this.theme = value;
        },
        init() {
            const stored = localStorage.getItem('theme');
            if (stored === 'dark' || stored === 'light') {
                this.theme = stored;
            } else if (stored === 'system') {
                this.theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            this.$watch('theme', () => {
                this.apply();
                this.persist();
            });

            window.addEventListener('storage', (event) => {
                if (event.key !== 'theme') return;
                if (event.newValue === 'dark' || event.newValue === 'light') {
                    this.theme = event.newValue;
                }
            });

            this.apply();
        },
    }"
    x-init="init()"
>
    <button
        type="button"
        @click="set(theme === 'dark' ? 'light' : 'dark')"
        class="bauhaus-btn flex items-center gap-2 bg-white px-3 py-2 text-xs leading-none text-slate-700 hover:bg-blue-50 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
        :aria-label="theme === 'dark' ? 'Beralih ke tema terang' : 'Beralih ke tema gelap'"
        wire:loading.attr="disabled"
    >
        <svg x-show="theme === 'dark'" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="5" />
            <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
        </svg>
        <svg x-show="theme !== 'dark'" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
        </svg>
        <span x-text="theme === 'dark' ? 'Terang' : 'Gelap'">Gelap</span>
    </button>
</div>
