<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReferralService;
use App\Services\WalletService;

class ReferralController extends Controller
{
    public function __construct(
        protected ReferralService $referralService,
        protected WalletService $walletService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $referralStats = $this->referralService->getReferralStats($user);

        $teamLevels = $this->getTeamLevels($user);
        $level1Users = $teamLevels['level1Users'];

        $directSummary = [
            'total_direct_referrals' => $level1Users->count(),
            'total_direct_deposit' => (float) $level1Users->sum('total_deposit'),
            'total_referral_commission_earned' => (float) ($referralStats['commission_earned'] ?? 0),
        ];

        return view('user.referral.index', [
            'referral' => $referralStats,
            'directSummary' => $directSummary,
            'level1Users' => $level1Users,
        ]);
    }

    public function team(): View
    {
        $user = Auth::user();
        $teamLevels = $this->getTeamLevels($user);

        return view('user.referral.team', [
            'level1Users' => $teamLevels['level1Users'],
            'level2Users' => $teamLevels['level2Users'],
            'level3Users' => $teamLevels['level3Users'],
            'level4Users' => $teamLevels['level4Users'],
            'level5Users' => $teamLevels['level5Users'],
        ]);
    }

    private function getTeamLevels(User $user): array
    {
        $level1Users = $this->getLevelUsers(collect([$user->id]));
        $level2Users = $this->getLevelUsers($level1Users->pluck('id'));
        $level3Users = $this->getLevelUsers($level2Users->pluck('id'));
        $level4Users = $this->getLevelUsers($level3Users->pluck('id'));
        $level5Users = $this->getLevelUsers($level4Users->pluck('id'));

        return [
            'level1Users' => $level1Users,
            'level2Users' => $level2Users,
            'level3Users' => $level3Users,
            'level4Users' => $level4Users,
            'level5Users' => $level5Users,
        ];
    }

    private function getLevelUsers(Collection $referrerIds): Collection
    {
        if ($referrerIds->isEmpty()) {
            return collect();
        }

        return User::query()
            ->select(['id', 'referred_by', 'status', 'created_at'])
            ->whereIn('referred_by', $referrerIds->all())
            ->with(['wallet:id,user_id,total_earned'])
            ->withSum([
                'deposits as total_deposit' => static function ($query) {
                    $query->where('status', 'confirmed');
                },
            ], 'amount')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(static function (User $referralUser) {
                $referralUser->total_deposit = (float) ($referralUser->total_deposit ?? 0);
                $referralUser->total_earned = (float) ($referralUser->wallet?->total_earned ?? 0);

                return $referralUser;
            });
    }
}
