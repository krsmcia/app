<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 shadow-sm transition-all duration-150 hover:border-red-300 hover:bg-red-50 hover:text-red-700 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-red-500/20 disabled:cursor-not-allowed disabled:opacity-50'
]) }}>
    {{ $slot }}
</button>