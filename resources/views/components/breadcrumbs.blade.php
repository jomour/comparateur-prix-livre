@props(['page' => null, 'data' => []])

@php
    use App\Services\BreadcrumbService;
    
    $breadcrumbs = BreadcrumbService::generateHtml($page, $data);
    $structuredData = BreadcrumbService::generate($page, $data);
@endphp

{{-- Données structurées JSON-LD --}}
<script type="application/ld+json">
    {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Breadcrumbs HTML --}}
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($breadcrumbs as $index => $breadcrumb)
            <li class="inline-flex items-center">
                @if($index > 0)
                    <svg class="w-6 h-6 text-purple-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @endif
                
                @if($breadcrumb['active'])
                    <span class="ml-1 text-sm font-semibold text-white md:ml-2">
                        {{ $breadcrumb['name'] }}
                    </span>
                @else
                    <a href="{{ $breadcrumb['url'] }}" class="inline-flex items-center text-sm font-medium text-purple-200 hover:text-yellow-300 transition-colors duration-200">
                        @if($index === 0)
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                        @endif
                        {{ $breadcrumb['name'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav> 