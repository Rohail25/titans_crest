<div class="table-responsive">
    <table class="table align-middle mb-0 text-blue-200">
        <thead class="bg-[#041a3d] text-white">
            <tr>
                <th class="border-[#d4af37]/20">User ID</th>
                <th class="border-[#d4af37]/20">Join Date</th>
                <th class="border-[#d4af37]/20">Total Deposit</th>
                <th class="border-[ #d4af37]/20">Total Earned</th>
                <th class="border-[#d4af37]/20">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $member)
                <tr class="border-[#d4af37]/10 hover:bg-[#041a3d]/60 transition duration-500">
                    <td class="text-black"><strong>TC-{{ $member->id }}</strong></td>
                    <td class="text-blue-300">{{ $member->created_at->format('Y-m-d') }}</td>
                    <td class="text-black"><strong>${{ number_format($member->total_deposit, 2) }}</strong></td>
                    <td class="text-black"><strong>${{ number_format($member->total_earned, 2) }}</strong></td>
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
                    <td colspan="5" class="text-center text-blue-300 py-4">{{ $emptyText }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
