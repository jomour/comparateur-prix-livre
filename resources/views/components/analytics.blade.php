{{-- Google Analytics --}}
@if(config('analytics.google_analytics.enabled') && config('analytics.google_analytics.tracking_id'))
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.google_analytics.tracking_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('analytics.google_analytics.tracking_id') }}', {
            'page_title': '{{ $meta['title'] ?? '' }}',
            'page_location': '{{ $meta['url'] ?? '' }}',
            'custom_map': {
                'dimension1': 'language',
                'dimension2': 'user_type'
            }
        });
        gtag('config', '{{ config('analytics.google_analytics.tracking_id') }}', {
            'language': '{{ app()->getLocale() }}',
            'user_type': '{{ auth()->check() ? 'authenticated' : 'guest' }}'
        });
    </script>
@endif

{{-- Google Tag Manager --}}
@if(config('analytics.google_tag_manager.enabled') && config('analytics.google_tag_manager.container_id'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ config('analytics.google_tag_manager.container_id') }}');</script>
    <!-- End Google Tag Manager -->
@endif

{{-- Facebook Pixel --}}
@if(config('analytics.facebook_pixel.enabled') && config('analytics.facebook_pixel.pixel_id'))
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ config('analytics.facebook_pixel.pixel_id') }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ config('analytics.facebook_pixel.pixel_id') }}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->
@endif

{{-- Hotjar --}}
@if(config('analytics.hotjar.enabled') && config('analytics.hotjar.site_id'))
    <!-- Hotjar Tracking Code -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:{{ config('analytics.hotjar.site_id') }},hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
@endif

{{-- Microsoft Clarity --}}
@if(config('analytics.clarity.enabled') && config('analytics.clarity.project_id'))
    <!-- Microsoft Clarity -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "{{ config('analytics.clarity.project_id') }}");
    </script>
@endif 