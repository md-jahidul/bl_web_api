@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            Banglalink
{{--            {{ config('app.name') }}--}}
        @endcomponent
    @endslot

    {{-- Body --}}
    <strong>Hello, User</strong><br>
    <p>Found a lead request. Please check in CMS</p>

{{--    {{ $slot }}--}}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ "Banglalink" }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
