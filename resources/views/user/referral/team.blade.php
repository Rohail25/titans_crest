@extends('layouts.user')

@section('page-title', 'Team')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sitemap"></i> Team Structure (Level 1 to Level 10)</h5>
            </div>
            <div class="card-body">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 1 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level1Users,
                            'emptyText' => 'No referrals found at Level 1.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 2 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level2Users,
                            'emptyText' => 'No referrals found at Level 2.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 3 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level3Users,
                            'emptyText' => 'No referrals found at Level 3.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 4 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level4Users,
                            'emptyText' => 'No referrals found at Level 4.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 5 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level5Users,
                            'emptyText' => 'No referrals found at Level 5.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 6 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level6Users,
                            'emptyText' => 'No referrals found at Level 6.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 7 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level7Users,
                            'emptyText' => 'No referrals found at Level 7.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 8 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level8Users,
                            'emptyText' => 'No referrals found at Level 8.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 9 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level9Users,
                            'emptyText' => 'No referrals found at Level 9.'
                        ])
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-0">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Level 10 Team</h5>
                    </div>
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level10Users,
                            'emptyText' => 'No referrals found at Level 10.'
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
