@extends('layouts.dashboard')

@section('page_title', 'Institution Leaderboard')

@section('content')
<div class="space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Leaderboard</h2><p class="text-sm text-gray-500 mt-1">Top learners across your institution</p></div>

    <div class="flex gap-2 animate-slide-down">
        @foreach(['all_time' => 'All Time', 'weekly' => 'This Week', 'monthly' => 'This Month'] as $key => $label)
        <a href="{{ route('leaderboards.tenant', ['period' => $key]) }}" class="px-4 py-2 rounded-lg text-xs font-bold {{ $period === $key ? 'bg-maroon-600 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left font-bold">Rank</th><th class="px-5 py-3 text-left font-bold">Student</th><th class="px-5 py-3 text-left font-bold">Points</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rankings as $row)
                    <tr class="hover:bg-gray-50 {{ $row['rank'] <= 3 ? 'bg-gradient-to-r from-maroon-50 to-transparent' : '' }}">
                        <td class="px-5 py-3">
                            @if($row['rank'] === 1)<span class="text-xl">🥇</span>@elseif($row['rank'] === 2)<span class="text-xl">🥈</span>@elseif($row['rank'] === 3)<span class="text-xl">🥉</span>@else<span class="font-bold text-gray-700">{{ $row['rank'] }}</span>@endif
                        </td>
                        <td class="px-5 py-3 font-semibold text-gray-700">{{ $row['name'] }}</td>
                        <td class="px-5 py-3 font-bold text-maroon-600">{{ number_format($row['points']) }} XP</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">No data yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
