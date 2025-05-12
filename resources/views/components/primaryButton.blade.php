<button {{ $attributes->merge(["class"=>"cursor-pointer font-semibold bg-white text-red-600
	px-4 py-2 rounded-[10px] shadow-lg hover:shadow-none
	hover:inset-shadow-sm hover:inset-shadow-red-600",
	"type" => "button" ])
}}>
	{{ $slot }}
</button>
