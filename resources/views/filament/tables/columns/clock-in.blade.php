@php

    if ($getRecord()->clock_in_at == $getState()) {

        if ($getRecord()->clock_in_at) {
            $tooltipText = "Clocked: " . $getRecord()->clock_in_at->format('D Y-m-d h:i:s A');
        } else {
            $tooltipText = "Manually Entered";
        }

        if ($getRecord()->clock_in_approved) {
            $displayTime = $getRecord()->clock_in_approved;
            $bgColor = 'bg-success-500';
        } else if ($getRecord()->clock_in_requested) {
            $displayTime = $getRecord()->clock_in_requested;
            $bgColor = 'bg-warning-500';
        } else {
            $displayTime = $getState();
            $bgColor = '';
            $tooltipText = 'Clocked';
        }
    } else if($getRecord()->clock_out_at == $getState()) {

        if ($getRecord()->clock_out_at) {
            $tooltipText = "Clocked: " . $getRecord()->clock_out_at->format('D Y-m-d h:i:s A');
        } else {
            $tooltipText = "Manually Entered";
        }

        if ($getRecord()->clock_out_approved) {
            $displayTime = $getRecord()->clock_out_approved;
            $bgColor = 'bg-success-500';
        } else if ($getRecord()->clock_out_requested) {
            $displayTime = $getRecord()->clock_out_requested;
            $bgColor = 'bg-warning-500';
        } else {
            $displayTime = $getState();
            $bgColor = '';
            $tooltipText = 'Clocked';
        }
    }
@endphp

<div x-data="{ tooltip: '{{ $tooltipText }}' }">
    <button x-tooltip="tooltip" class="px-3 {{ $bgColor }} rounded-full">
        @if ($displayTime)
            {{ $displayTime->format('D Y-m-d h:i:s A') }}        
        @endif
    </button>
</div>
