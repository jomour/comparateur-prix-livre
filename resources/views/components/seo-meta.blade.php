@props(['meta', 'structuredData' => null, 'type' => 'website'])

@php
    use App\Services\SeoService;
    
    // Si $meta n'est pas fourni, utiliser les métadonnées de base
    if (!isset($meta)) {
        $meta = SeoService::getBaseMeta();
    }
    
    // Générer les liens alternatifs
    $alternateLinks = SeoService::getAlternateLinks();
    
    // Générer les métadonnées Open Graph et Twitter
    $ogMeta = SeoService::getOpenGraphMeta($meta);
    $twitterMeta = SeoService::getTwitterMeta($meta);
    
    // Générer les données structurées si pas fournies
    if (!$structuredData) {
        $structuredData = SeoService::getStructuredData($type, $meta);
    }
@endphp

{{-- Métadonnées de base --}}
<title>{{ $meta['title'] }}</title>
<meta name="description" content="{{ $meta['description'] }}">
<meta name="keywords" content="{{ $meta['keywords'] }}">
<meta name="author" content="{{ config('app.name') }}">
<meta name="robots" content="index, follow">
<meta name="language" content="{{ $meta['locale'] }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $meta['url'] }}">

{{-- Liens alternatifs pour le hreflang --}}
@foreach($alternateLinks as $lang => $url)
    <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
@endforeach

{{-- Métadonnées Open Graph --}}
@foreach($ogMeta as $property => $content)
    <meta property="{{ $property }}" content="{{ $content }}">
@endforeach

{{-- Métadonnées Twitter Card --}}
@foreach($twitterMeta as $name => $content)
    <meta name="{{ $name }}" content="{{ $content }}">
@endforeach

{{-- Données structurées JSON-LD --}}
<script type="application/ld+json">
    {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Métadonnées supplémentaires pour les performances --}}
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="theme-color" content="#3B82F6">

{{-- Préconnexions pour améliorer les performances --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="preconnect" href="https://www.amazon.fr">
<link rel="preconnect" href="https://www.fnac.com">
<link rel="preconnect" href="https://www.cultura.com">

{{-- Favicon --}}
<link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('images/mangavaluecheck_logo.png') }}">

{{-- Manifest pour PWA --}}
<link rel="manifest" href="{{ asset('manifest.json') }}"> 