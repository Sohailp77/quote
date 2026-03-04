<x-mail::message>
# Hello {{ $quote->customer_name }},

Please find the attached quotation **{{ $quote->reference_id }}** from {{ config('app.provider_name') ?? config('app.name') }}.

@if($customMessage)
{{ $customMessage }}
@else
We have prepared this quote based on our recent discussion. Let us know if you have any questions.
@endif

Best regards,  
{{ config('app.provider_name') ?? config('app.name') }}
</x-mail::message>
