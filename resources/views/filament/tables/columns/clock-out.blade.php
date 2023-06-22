

<table style="border-collapse: separate; border-spacing: 0.75rem 0;">
    
    @if($getRecord()->clock_out_at)
    <tr>
        <th class="text-left">Clocked</th>
        <td>{{ $getRecord()->clock_out_at->format("D Y-m-d") }}</td>
        <td>{{ $getRecord()->clock_out_at->format("h:i:s A") }}</td>
    </tr>
    @endif

    @if($getRecord()->clock_out_requested)
    <tr>
        <th class="text-left">Requested</th>
        <td>{{ $getRecord()->clock_out_requested->format("D Y-m-d") }}</td>
        <td>{{ $getRecord()->clock_out_requested->format("h:i:s A") }}</td>
    </tr>
    @endif

    @if($getRecord()->clock_out_approved)
    <tr>
        <th class="text-left">Approved</th>
        <td>{{ $getRecord()->clock_out_approved->format("D Y-m-d") }}</td>
        <td>{{ $getRecord()->clock_out_approved->format("h:i:s A") }}</td>
    </tr>
    @endif

</table>
