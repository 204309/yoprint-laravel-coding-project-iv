<button {{ $attributes->merge(['type' => 'button', 'class' => 'p-3 rounded-xl font-semibold bg-amber-200 text-yellow-950 backdrop-blur-md shadow-zinc-400 shadow-md active:scale-90 hover:bg-orange-300 transition']) }}>
    {{ $slot }}
</button>