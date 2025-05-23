<style>
    .ranking-table th,
    .ranking-table td {
        vertical-align: middle;
        font-size: 14px;
    }

    .ranking-table .badge {
        font-size: 13px;
        padding: 10px 20px;
        border-radius: 6px;
    }

    .rank-tr>td {
        padding: 12px 16px;
        /* bạn có thể chỉnh lại giá trị tuỳ thích */
        vertical-align: middle;
    }

    thead.rank-head {
        background-color: #f44336 !important;
        /* màu nền mới (đỏ tươi) */
        padding: 12px;
        /* padding toàn bộ */
    }

    thead.rank-head th {
        padding: 12px 8px;
        /* padding từng ô <th> */
        color: black;
        /* màu chữ nếu cần */
    }

    .ranking-table {
        font-size: 16px !important;
    }

    .fs-16 {
        font-size: 25px;
    }

    ul li {
        font-size: 16px;
    }
</style>
<!-- Button trigger modal -->
<div class="d-flex justify-content-end mb-2">
    <div>
        <a href="#" data-bs-toggle="modal" data-bs-target="#rankingRulesModal"
            class="ms-3 text-decoration-underline text-secondary" style="font-size: 16px">{{__('Ranking Rules')}}</a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="rankingRulesModal" tabindex="-1" aria-labelledby="rankingRulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title w-100 text-center fw-bold" id="rankingRulesModalLabel">{{__('Ranking Rules')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4 border rounded" style="border-color: #198754;">
                    <div class="bg-success text-white px-3 py-2 rounded-top fw-bold" style="font-size: 16px">
                        {{__('Round-robin format')}}
                    </div>
                    <ul class="list-group list-group-flush px-3 py-2">
                        <li>{{__(' 1. Total points achieved in all matches.')}}</li>
                        <li>{{__(' 2. Total points each player achieves in head-to-head matches between them.')}} </li>
                        <li>{{__(' 3. Set win-loss ratio for each player in head-to-head matches between them.')}} </li>
                        <li>{{__(' 4. Number of sets won by each player in head-to-head matches between them.')}} </li>
                        <li>{{__(' 5. Point difference of all sets (games) played.')}}</li>
                        <li>{{__(' 6. Total points achieved in all sets (games) played. ')}}</li>
                        <li>{{__(' 7. Point difference of the sets (games) in head-to-head matches between them. ')}}</li>
                        <li>{{__(' 8. Total points achieved in the sets (games) in head-to-head matches between them. ')}}
                        </li>
                        <li>{{__(' 9. If the above rules do not determine the ranking, the organizing committee will manually rank the players. ')}}
                        </li>
                        <li>{{__(' 10. Win 3 points, lose no points ')}}</li>
                    </ul>
                </div>

                <div class="border rounded" style="border-color: #198754;">
                    <div class="bg-success text-white px-3 py-2 rounded-top fw-bold" style="font-size: 16px">
                        {{__('Knockout format')}}
                    </div>
                    <ul class="list-group list-group-flush px-3 py-2">
                        <li>{{__(' 1. Total number of matches played.')}} </li>
                        <li>{{__(' 2. Number of matches won. ')}}</li>
                        <li>{{__(' 3. Number of matches lost. ')}}</li>
                        <li>{{__(' 4. If the above rules do not determine the ranking, the organizing committee will manually rank the players.')}}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($leagueInfor->format_of_league === 'round-robin')
    <h5 class="text-success fw-bold mb-3">{{__('Rank Round-robin')}}</h5>

    @if (count($ranking) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center align-middle ranking-table fs-16"
                   style="font-size: 16px;">
                <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>{{__('Team / Player')}}</th>
                    <th>{{__('Total match')}}</th>
                    <th>{{__('Win')}}</th>
                    <th>{{__('Lose')}}</th>
                    <th>{{__('Point')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($ranking as $index => $rank)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start fw-semibold text-success">
                            {{ $rank->user->name ?? '---'}}
                            @if ($rank->user->partner && optional($rank->league)->type_of_league == 'doubles')
                                + {{ $rank->user->partner->name }}
                            @endif
                        </td>
                        <td>{{ $rank->match_played }}</td>
                        <td>{{ $rank->win }}</td>
                        <td>{{ $rank->lose }}</td>
                        <td><strong>{{ $rank->point }}</strong></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    @else
        <div class="alert alert-primary">{{__('Tournament is updating data.')}}</div>
    @endif
@elseif ($leagueInfor->format_of_league === 'knockout')
    <h5 class="text-success fw-bold mb-3">{{__('Rank Knockout')}}</h5>
    @if (count($ranking) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center align-middle ranking-table fs-16"
                   style="font-size: 16px;">
                <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>{{__('Team / Player')}}</th>
                    <th>{{__('Total match')}}</th>
                    <th>{{__('Win')}}</th>
                    <th>{{__('Lose')}}</th>
                    <th>{{__('Round')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($ranking as $index => $rank)
                    @php
                        $teamName = getTeamNameFromRank($rank);
                    @endphp
                    <tr class="{{ is_null($rank->eliminated_round) }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start fw-semibold text-success">
                            {{ $teamName }}
                        </td>
                        <td>{{ $rank->match_played }}</td>
                        <td>{{ $rank->win }}</td>
                        <td>{{ $rank->lose }}</td>
                        <td>
                            @switch($rank->eliminated_round)
                                @case('champion')
                                <span class="badge bg-warning text-dark">🏆 {{__('champion (win final)')}}</span>
                                @break

                                @case('final')
                                {{__('final (lose)')}}
                                @break

                                @case('semi-finals')
                                {{__('semi-finals')}}
                                @break

                                @case('quarter-finals')
                                {{__('quarter-finals')}}
                                @break

                                @default
                                @if (is_null($rank->eliminated_round))
                                    <span class="text-primary fw-semibold">{{__('Into the Finals')}}</span>
                                @else
                                    {{ $rank->eliminated_round }}
                                @endif
                            @endswitch
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    @else
        <div class="alert alert-primary">{{__('Tournament is updating data')}}.</div>
    @endif
@endif
