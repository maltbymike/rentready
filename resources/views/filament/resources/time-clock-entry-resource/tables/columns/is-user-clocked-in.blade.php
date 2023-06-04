<div class="w-full">
    @if($getRecord()->isClockedIn())
        <x-tools.status-pill class="bg-success-600 text-white">Yes</x-tools.status-pill>
    @else  
        <x-tools.status-pill class="bg-danger-600 text-white">No</x-tools.status-pill>
    @endif
</div>