<button {{ $attributes->merge(["class"=>"cursor-pointer font-semibold bg-red-600 text-white
 	px-4 py-2 rounded-[10px] shadow-lg hover:shadow-none
	hover:bg-white hover:text-red-600 hover:inset-shadow-sm hover:inset-shadow-red-600",
	"type" => "button" ])
}}>
	{{ $slot }}
</button>
