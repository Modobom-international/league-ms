<style>
    .stat-card {
        padding: 1rem;
        color: white;
        border-radius: 0.5rem;
    }
    .stat-card .icon {
        font-size: 2rem;
    }
    .bg-purple { background-color: #6f42c1; }
    .bg-teal { background-color: #20c997; }
    .bg-orange { background-color: #fd7e14; }
    .bg-red { background-color: #dc3545; }
    .map-container {
        height: 250px;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .name-team {
        color: green;
    }
</style>
<div class="container py-4">
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Top Teams -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white fw-bold">{{'Ranking'}}</div>
                <ul class="list-group list-group-flush">
                    @foreach($ranking as $index => $rank)
                        @if ($rank->league && $rank->league->format_of_league === 'knockout')
                            <?php
                            if ($rank->league && $rank->league->type === 'knockout') {
                                $icon = $rank->places . '.';
                            } else {
                                $icons = ['🥇', '🥈', '🥉'];
                                $icon = $icons[$index] ?? ($index + 1) . '.';
                            }

                            $name1 = $rank->user->name ?? '---';
                            $name2 = $rank->user->partner->name ?? '';

                            $teamName = $name2 ? $name1 . ' / ' . $name2 : $name1;
                            ?>
                            <li class="list-group-item" style="font-size: 16px; color: green">
                                {{ $icon }} {{ $teamName  }}

                            </li>
                        @else
                            <?php
                            $icons = ['🥇', '🥈', '🥉'];
                            if ($rank->eliminated_round == 'semi-finals' || $rank->eliminated_round == 'final') {
                                $icon = $rank->places . '.';
                            } else {
                                $icon = $index + 1 . ",";
                            }

                            $name1 = $rank->user->name ?? '---';
                            $name2 = $rank->user->partner->name ?? '';

                            $teamName = $name2 ? $name1 . ' / ' . $name2 : $name1;
                            ?>
                            <li class="list-group-item" style="font-size: 16px; color: green">
                                {{ $icon }} {{ $teamName  }}

                                <span class="float-end text-black">{{ $rank->point }} điểm</span>
                            </li>
                        @endif
                    @endforeach

                    <div class="card-footer text-center"><a href="">{{'View all'}}</a></div>
                </ul>
            </div>

            <!-- Rounds -->
            <div class="card mb-3">
                <div class="card-header bg-secondary fw-bold text-white">{{'Schedule'}}</div>
                @foreach($firstThreeSchedules as $item)
                    <div class="card-body" style="font-size: 16px; display: flex">
                        <strong>	⚔️</strong>
                        <div class="name-team">
                            {{$item->player1Team1->name ?? "" }}
                            @if($item->player1Team1 && $item->player1Team1->partner)
                                / {{ $item->player1Team1->partner->name ?? "" }} &nbsp;  &nbsp;
                            @endif
                        </div>
                        <div>
                            &nbsp; vs
                        </div>
                        <div class="name-team">
                            &nbsp;  &nbsp; {{$item->player1Team2->name ?? ""}}
                            @if($item->player1Team2 && $item->player1Team2->partner)
                                / {{ $item->player1Team2->partner->name ?? "" }}
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="card-footer text-center"><a href="{{route('leagueSchedule.info', $leagueInfor->slug)}}">{{'View all'}}</a></div>
            </div>


            <!-- Map -->
            <div class="card">
                <div class="card-header">{{'Location'}}</div>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?..." width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <img src="{{asset('/images/bg-league.png')}}" alt="Banner" class="img-fluid">
            </div>
            <!-- Stats -->
            <h4 class="fw-bold">📈 {{'General statistics'}}</h4>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="stat-card bg-secondary">
                        <div class="fw-bold">{{'Total Matches'}}</div>
                        <div class="display-6">🎮 <h5 class="card-title text-white">{{$countMatch. " matches"}}</h5></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-teal">
                        <div class="fw-bold">{{'Total Players'}}</div>
                        <div class="display-6 row">
                            👥
                            <h5 class="card-title text-white">{{$countPlayer . " players"}}</h5>
                        </div>
                    </div>
                </div>
                @php
                    function getTeamName($rank) {
                        $name1 = $rank->user->name ?? '---';
                        $name2 = $rank->user->partner->name ?? '';
                        return $name2 ? "$name1 - $name2" : $name1;
                    }
                @endphp
                <div class="col-md-4">
                    <div class="stat-card bg-red">
                        <div class="fw-bold">{{ 'Highest Ranked Team' }}</div>
                            <div class="display-6">🔥
                                @if ($topRank)
                                <h5 class="card-title text-white">
                                    {{ getTeamName($topRank) }}
                                    @if($topRank->league && $topRank->league->format_of_league == "round-robin")
                                        ({{ $topRank->point ?? '-' }})
                                    @endif
                                </h5>
                                @else
                                    <h5 class="text-white">No data</h5>
                                @endif
                            </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <p class="fw-bold">{{ 'Lowest Ranked Team' }}</p>
                            <div class="display-6">🔻
                                @if ($bottomRank)
                                <h5 class="card-title text-white">
                                    {{ getTeamName($bottomRank) }}
                                    @if($bottomRank->league && $bottomRank->league->format_of_league == "round-robin")
                                        ({{ $bottomRank->point ?? '-' }})
                                    @endif
                                </h5>
                                @else
                                    <h5 class="text-white">No data</h5>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
