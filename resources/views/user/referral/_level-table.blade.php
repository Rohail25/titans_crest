<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Join Date</th>
                <th>Total Deposit</th>
                <th>Total Earned</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $member)
                <tr>
                    <td><strong>#{{ $member->id }}</strong></td>
                    <td>{{ $member->created_at->format('Y-m-d') }}</td>
                    <td><strong>${{ number_format($member->total_deposit, 2) }}</strong></td>
                    <td><strong>${{ number_format($member->total_earned, 2) }}</strong></td>
                    <td>
                        @if($member->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif($member->status === 'banned')
                            <span class="badge bg-danger">Banned</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($member->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">{{ $emptyText }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
