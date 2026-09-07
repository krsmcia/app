<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-600 shadow-sm transition-all duration-150 hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-emerald-500/20 disabled:cursor-not-allowed disabled:opacity-50'
]) }}>
    {{ $slot }}
</button>