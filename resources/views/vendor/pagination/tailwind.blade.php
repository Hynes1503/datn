@props([
    'paginator',
    'elements' => null,
])

@if (! is_null($elements))
<div {{ $attributes->merge(['class' => 'flex items-center justify-between mt-6']) }}>
    <div class="flex-1 flex items-center justify-between sm:justify-end">
        <div class="flex space-x-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                    Previous
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 border border-blue-500 rounded-lg">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                    Next
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>
    </div>
</div>
@endif