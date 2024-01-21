<?php

    if ($getRecord()->clock_in_at == $getState()) {

        if ($getRecord()->clock_in_at) {
            $tooltipText = "Clocked: " . $getRecord()->clock_in_at->format('D Y-m-d h:i:s A');
        } else {
            $tooltipText = "Manually Entered";
        }

        if ($getRecord()->hasClockInChangeRequest('approved')) {
            $displayTime = $getRecord()->clock_in_requested;
            $bgColor = 'bg-success-500';
        } else if ($getRecord()->hasClockInChangeRequest('rejected')) {
            $displayTime = $getRecord()->clock_in_requested;
            $bgColor = 'bg-danger-500';
        } else if ($getRecord()->hasClockInChangeRequest('unapproved')) {
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

        if ($getRecord()->hasClockOutChangeRequest('approved')) {
            $displayTime = $getRecord()->clock_out_requested;
            $bgColor = 'bg-success-500';
        } else if ($getRecord()->hasClockOutChangeRequest('rejected')) {
            $displayTime = $getRecord()->clock_out_requested;
            $bgColor = 'bg-danger-500';
        } else if ($getRecord()->hasClockOutChangeRequest('unapproved')) {
            $displayTime = $getRecord()->clock_out_requested;
            $bgColor = 'bg-warning-500';
        } else {
            $displayTime = $getState();
            $bgColor = '';
            $tooltipText = 'Clocked';
        }
    }
?>

<div x-data="{ tooltip: '{{ $tooltipText }}' }">
    <button x-tooltip="tooltip" class="px-3 {{ $bgColor }} rounded-full">
        @if ($displayTime)
            {{ $displayTime->format('D Y-m-d h:i A') }}
        @endif
    </button>
</div>
