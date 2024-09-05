<script src="https://cdn.tailwindcss.com"></script>

<div class="flex flex-col h-screen">
    <div class="flex justify-center grow bg-gray-900 h-0 pt-5">
        <img src="/proxy?url={{ urlencode($images[$index]) }}" alt="" class="h-full"/>
    </div>

    <div class="flex justify-center gap-5 h-[15vh] shrink-0 py-5 bg-gray-950">
        @foreach ($images->slice(max(0, $index - 6), 8) as $index => $image)
            <a href="{{"/comic/{$index}"}}" class="h-full">
                <img src="/proxy?url={{ urlencode($image) }}" alt="" class="h-full"/>
            </a>
        @endforeach
    </div>
</div>
