
@push('styles')
    <style>
        .hero-bg {
            background: linear-gradient(135deg, #0F766E 0%, #14B8A6 50%, #5EEAD4 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('{{ asset('images/hero-bg.jpg') }}') center;
            background-size: cover;
            opacity: 0.3;
            z-index: 1;
        }
        .hero-content { position: relative; z-index: 2; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-8px) rotateX(5deg); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .status-available { background: linear-gradient(135deg, var(--brand-green), #34D399); }
        .status-onleave { background: linear-gradient(135deg, #EF4444, #F87171); }
        .filter-chip { transition: all 0.2s ease; }
        .filter-chip:hover { transform: scale(1.05); }
        .floating-element { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .gradient-text { background: linear-gradient(135deg, #0F766E, #14B8A6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .morning,
        .afternoon,
        .night {
            position: relative;
        }

        /* shared base style */
        .morning::before,
        .afternoon::before,
        .night::before {
            background-color: yellow;
            color: blue;
            position: absolute;
            top: auto;
            right: -8px;
            width: 20px;
            height: 20px;
            z-index: 20;
            background: white;        /* optional tapi recommended */
            border-radius: 50%;       /* bikin icon lebih clean */
            padding: 2px;
        }

        /* specific icons */
        .morning::before {
            content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ecc94b' class='size-4'%3E%3Cpath d='M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z' /%3E%3C/svg%3E");
        }

        .afternoon::before {
            content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2390cdf4' class='size-4'%3E%3Cpath fill-rule='evenodd' d='M4.5 9.75a6 6 0 0 1 11.573-2.226 3.75 3.75 0 0 1 4.133 4.303A4.5 4.5 0 0 1 18 20.25H6.75a5.25 5.25 0 0 1-2.23-10.004 6.072 6.072 0 0 1-.02-.496Z' clip-rule='evenodd' /%3E%3C/svg%3E");
        }

        .night::before {
            content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23434190' class='size-4'%3E%3Cpath fill-rule='evenodd' d='M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z' clip-rule='evenodd' /%3E%3C/svg%3E");
        }

    </style>
@endpush

@push('scripts')
    <!-- Typed.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.12/typed.min.js"></script>

    <script>
        function doctorApp() {
            return {
                init() {
                    console.log('Alpine.js initialized');
                    this.initializeTypedText();
                },

                initializeTypedText() {
                    setTimeout(() => {
                        new Typed('#typed-text', {
                            strings: [
                                'Temukan Dokter Terbaik untuk Anda',
                                // 'Buat Janji dengan Mudah',
                                'Kesehatan Anda Prioritas Kami'
                            ],
                            typeSpeed: 50,
                            backSpeed: 30,
                            backDelay: 2000,
                            loop: true,
                            showCursor: true,
                            cursorChar: '|'
                        });
                    }, 1000);
                },
            };
        }

        // Watch for filter changes
        document.addEventListener('alpine:init', () => {
            Alpine.data('doctorApp', doctorApp);
        });

    </script>
@endpush

<x-guest-layout>
<div x-data="doctorApp()">
    <!-- Hero Section -->
    <section class="hero-bg pt-6 pb-4">
        <div class="hero-content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
            <div class="text-center">
                <div class="floating-element mb-8">
                    {{-- <svg class="mx-auto h-20 w-20 text-white opacity-80" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 7V9C15 10.1 15.9 11 17 11V20C17 21.1 16.1 22 15 22H13C11.9 22 11 21.1 11 20V14H9V20C9 21.1 8.1 22 7 22H5C3.9 22 3 21.1 3 20V11C4.1 11 5 10.1 5 9V7L21 9Z"></path>
                    </svg> --}}
                    <img src="{{ asset('images/logo-rssh-white.svg') }}" alt="RS Samsoe Hidajat" class="h-20 mx-auto">
                    {{-- <img src="https://rssamsoehidajat.com/wp-content/uploads/2025/01/logo-rssh.png" alt="RS Samsoe Hidajat" class="h-10 mx-auto mt-4"> --}}
                </div>
                <h1 class="text-3xl md:text-6xl font-bold text-white mb-6">
                    <span id="typed-text"></span>
                </h1>
                <p class="text-xl text-teal-100 mb-8 max-w-3xl mx-auto">
                    Temukan dokter terbaik sesuai kebutuhan Anda.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-white w-full sm:w-52">
                        <div class="text-3xl font-bold">{{ $doctorsCount }}</div>
                        <div class="text-sm">Dokter Tersedia</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-white w-full sm:w-52">
                            <div class="text-3xl font-bold">{{ $clinicsCount }}</div>
                            <div class="text-sm">Klinik</div>
                        </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-50 rounded-2xl p-4 sm:p-8 shadow-lg">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Cari Dokter</h2>
                <form method="GET">
                    <!-- Search Bar -->
                    <div class="mb-6">
                        <div class="relative">
                            <input type="text" name="name" value="{{ request('name') }}" placeholder="Nama dokter" class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" />
                            <svg class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Specialization Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Klinik</label>
                            <select name="clinic" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                           {{-- <select id="clinic-select" name="clinic" aria-label="Filter Klinik"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"> --}}
                                <option value="" {{ !request('clinic') ? 'selected' : ''}}>Semua Klinik</option>
                                @foreach($clinics as $c)
                                    <option value="{{ $c->id }}" {{ request('clinic') == $c->id ? 'selected':'' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Day Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hari Praktik</label>
                            <select name="day" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">Semua Hari</option>
                                @php $days = \App\Enums\DayOfWeek::options(); @endphp
                                @foreach($days as $value => $label)
                                    <option value="{{ $value }}" {{ request('day') == $value ? 'selected':'' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex">
                        <button type="submit" class="ml-auto px-4 py-2 rounded text-white w-24" style="background:var(--brand-blue)">Cari</button>
                        <a href="{{ route(Route::currentRouteName()) }}" class="px-4 py-2 ms-4 bg-gray-200 rounded w-24 text-center">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Doctors Grid -->
    <section class="py-12 bg-gray-50">
        {{-- <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-sm md:text-base">
            @forelse($doctors as $doctor)
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden p-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 md:w-20 md:h-20 flex-shrink-0 order-first rounded-2xl overflow-hidden bg-gray-100">
                            @if($doctor->profile_photo_path)
                                <img src="{{ Storage::url($doctor->profile_photo_path) }}" alt="{{ $doctor->name }}" loading="lazy" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/logogram-rssh.svg')}}" alt="No Image" loading="lazy" class="object-scale-down">
                                {{-- <div class="w-full h-full flex mask-radial-at-center mask-radial-from-100% bg-[url('{{ asset('images/logogram-rssh.svg')}}')] bg-cover bg-center"></div> -- }}
                            @endif
                        </div>
                        <div class="grid grid-flow-row md:grid-flow-col flex-1 justify-between">
                            <div class="flex-1 grow order-last md:order-none">
                                <h2 class="text-md xl:text-lg font-medium">{{ $doctor->name }} @if($doctor->title) , {{ $doctor->title }} @endif</h2>
                                <div class="text-xs md:text-sm text-teal-600">{{ implode(', ', $doctor->specializations->pluck('name')->toArray()) }}</div>
                            </div>
                            @php
                                $onLeave = $doctor->leaves->first(function($l) use($today){
                                    return $today->between($l->start_date, $l->end_date ?? $l->start_date);
                                });
                            @endphp
                            <div class="order-none md:order-last">
                                @if($onLeave)
                                    <span class="px-2 py-2/3 md:py-1 rounded text-white text-[11px] md:text-sm status-onleave">Cuti</span>
                                @else
                                    <span class="px-2 py-2/3 md:py-1 rounded text-white text-[11px] md:text-sm status-available">Tersedia</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="m-3 text-xs md:text-sm text-gray-600">
                        <strong class="text-gray-400">Jadwal Praktik</strong>
                        <ul class="grid grid-cols-2 xl:grid-cols-3 gap-2 mt-2 text-xs md:text-sm text-gray-700">
                            @php
                                // Group schedules by clinic name first, fallback to 'Umum' if missing
                                $byClinic = $doctor->schedules->groupBy(fn($s) => $s->clinic?->name ?? 'Umum');
                                $queryDay = request()->query('day');
                            @endphp

                            @foreach($byClinic as $clinicName => $clinicSchedules)
                                <li class="col-span-full">
                                    <div class="mb-2 text-sm font-semibold text-gray-700">{{ $clinicName }}</div>
                                    <div class="grid grid-cols-2 xl:grid-cols-3 gap-2">
                                        @foreach($clinicSchedules->sortBy('day_of_week')->groupBy('day_of_week') as $day => $items)
                                            @php
                                                $dayLabel = $day;
                                                if (is_numeric($day)) {
                                                    $enum = \App\Enums\DayOfWeek::tryFrom((int) $day);
                                                    if ($enum) {
                                                        $dayLabel = $enum->label();
                                                    }
                                                } else {
                                                    $found = null;
                                                    foreach (\App\Enums\DayOfWeek::cases() as $c) {
                                                        if (strtolower($c->dayName()) === strtolower($day) || strtolower($c->name) === strtolower($day)) {
                                                            $found = $c;
                                                            break;
                                                        }
                                                    }
                                                    if ($found) {
                                                        $dayLabel = $found->label();
                                                    }
                                                }
                                            @endphp
                                            <div class="flex flex-col mb-2 border border-gray-200 rounded-lg p-2 {{ ($queryDay !== null && $queryDay == $day) ? 'bg-blue-50 border-blue-300 border-2':'' }}">
                                                <span class="mb-1 text-center text-blue-800 {{ ($queryDay !== null && $queryDay == $day) ? 'font-bold':'font-semibold' }}">{{ $dayLabel }}</span>
                                                <div class="flex gap-2 flex-wrap justify-center">
                                                    @foreach($items as $it)
                                                        <span class="text-center inline-block bg-green-200 mix-blend-multiply text-gray-700 p-2 rounded text-xs">{{ \Carbon\Carbon::parse($it->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($it->end_time)->format('H:i') }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </li>
                            @endforeach
                            @if($doctor->schedules->isEmpty())
                                <li class="col-span-full text-gray-400 italic text-center">Belum ada jadwal</li>
                            @endif
                        </ul>
                    </div>

                    @if ($doctor->leaves->isNotEmpty())
                    <div class="flex p-4 my-4 text-sm text-red-700 rounded bg-red-50 border border-red-100" role="alert">
                        <svg class="w-4 h-4 me-2 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <span class="sr-only">Alert</span>
                        <div>
                            <span class="font-medium">Jadwal Cuti:</span>
                            <ul class="mt-2 list-disc list-outside space-y-1 ps-2.5 text-red-600">
                                @foreach ($doctor->leaves->all() as $leave)
                                    <li>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} @if($leave->end_date > $leave->start_date) - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }} @endif</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    {{-- <div class="flex gap-3">
                        <button class="flex-1 bg-teal-600 text-white py-2 px-4 rounded-lg hover:bg-teal-700 transition-colors font-medium text-sm md:text-base">
                            Lihat Detail
                        </button>
                        <button class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm md:text-base">
                            Buat Janji
                        </button>
                    </div> -- }}
                </div>
            @empty
                <div class=" col-span-2 text-gray-400 italic text-center">Dokter atau spesialisasi tidak ditemukan untuk hari {{ \App\Enums\DayOfWeek::tryFrom(request()->query('day'))?->label() }}.</div>
            @endforelse

        </div>

        <div class="max-w-7xl mx-auto px-4 mt-6">
            {{ $doctors->links() }}
        </div> --}}

        @if($clinics->count())
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 gap-6 text-sm md:text-base">
            @foreach($clinics as $clinic)
                <div class="p-4 bg-white shadow rounded">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-[var(--brand-blue)]">{{ $clinic->name }}</h3>
                            @if(!empty($clinic->address))
                                <div class="text-sm text-gray-500">{{ $clinic->address }}</div>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">Dokter: {{ $clinic->schedules->groupBy('doctor_id')->count() }}</div>
                    </div>

                    {{-- Doctors list --}}
                    @if($clinic->doctors->count())
                        <div class="grid gap-6">
                            @php
                                $byDoctor = $clinic->schedules->groupBy('doctor_id');
                            @endphp
                            @foreach($byDoctor as $doctorId => $schedules)
                                @php $doctor = $schedules->first()->doctor; @endphp
                                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="text-base lg:text-lg font-semibold">{{ $doctor->name }}</h4>
                                                    @if($doctor->specializations && $doctor->specializations->count())
                                                        <div class="text-xs lg:text-base text-gray-500 capitalize text-slate-500">
                                                            {{ $doctor->specializations->pluck('name')->join(', ') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    @if(isset($doctor->leaves) && $doctor->leaves->count())
                                                        @php
                                                            $onLeave = $doctor->leaves->filter(function($l) use($today) {
                                                                $start = \Carbon\Carbon::parse($l->start_date);
                                                                $end = \Carbon\Carbon::parse($l->end_date);
                                                                return $start->lte($today) && $end->gte($today);
                                                            })->count() > 0;
                                                        @endphp
                                                        @if($onLeave)
                                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Cuti</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Schedules for this clinic only --}}
                                            @php
                                                // schedules was eager loaded; still ensure we only show those for this clinic
                                                $schedules = $doctor->schedules->filter(function($s) use ($clinic) {
                                                    return $s->clinic_id == $clinic->id;
                                                })->sortBy(function($s) {
                                                    return $s->day_of_week . $s->start_time;
                                                });

                                                // Group berdasarkan hari
                                                $grouped = $schedules->groupBy('day_of_week');

                                                // 1–7 = Senin–Minggu (atau sesuaikan kalau enum kamu beda)
                                                $days = \App\Enums\DayOfWeek::cases();
                                            @endphp

                                            @if($schedules->count())
                                                <div class="mt-3 grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">

                                                @foreach($days as $dayEnum)
                                                    @php
                                                        $dow = $dayEnum->value; // misal: 1 = Senin
                                                        $label = $dayEnum->label();
                                                        $slots = $grouped->get($dow, collect());
                                                        $isToday = \Carbon\Carbon::today()->dayOfWeekIso == $dow;
                                                    @endphp

                                                    <div class="p-3 border rounded-md min-h-[64px] flex flex-col justify-start
                                                                {{ $isToday ? 'ring-2 ring-accent/20 bg-accent/5' : 'bg-soft-50' }}">
                                                        <div class="text-sm font-semibold text-slate-700 border-b-2">{{ $label }}</div>

                                                        @if($slots->isNotEmpty())
                                                        <div class="mt-2 space-y-1">
                                                            @foreach($slots as $slot)
                                                                <div class="text-sm text-slate-800 font-medium {{ $slot->period }}">
                                                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                                                    –
                                                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @else
                                                            <div class="mt-2 text-xs italic text-slate-400">No Schedule</div>
                                                        @endif
                                                    </div>
                                                @endforeach

                                            </div>
                                            @else
                                                <div class="flex flex-1 mt-2 text-sm text-slate-400 italic items-center min-h-[80px]">Tidak ada jadwal di klinik ini.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-1 text-sm text-slate-400 italic items-center justify-center min-h-[128px]">Tidak ada jadwal dokter pada klinik ini.</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="max-w-7xl mx-auto px-4 mt-6">
            {{ $clinics->links() }}
        </div>
    @else
        <div class="p-6 bg-white rounded shadow text-center text-gray-600">
            Tidak ada hasil ditemukan.
        </div>
    @endif
    </section>

    <!-- Footer -->
    <footer id="footer" class="bg-white text-gray-800" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
        <!-- Top: logo + alamat + kontak + informasi -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-8 items-start">
            <!-- Column 1: Logo -->
                <div class="flex items-center col-span-3 md:justify-start justify-center">
                    <a href="https://rssamsoehidajat.com" aria-label="Home">
                    <img
                        src="https://rssamsoehidajat.com/wp-content/uploads/2023/11/logo-rssh-1.webp"
                        alt="RS Samsoe Hidajat logo"
                        width="94" height="81"
                        class="w-24 h-auto object-contain"
                        loading="eager"
                        decoding="async"
                    >
                    </a>

                    <!-- Column 2: Alamat -->
                    <div class="md:col-span-1 ml-8">
                        <h3 class="inline-flex items-center border-l-4 border-[#08549c] pl-3 text-base font-semibold mb-2">Alamat</h3>
                        <p class="text-sm leading-relaxed">
                        Jl. RE Martadinata No. 9, Arteri Utara, Tawangsari, Semarang Barat, Jawa Tengah, Indonesia.
                        </p>
                    </div>
                </div>


                <!-- Column 3: Kontak -->
                <div class="col-span-2">
                    <h3 class="inline-flex items-center border-l-4 border-[#08549c] pl-3 text-base font-semibold mb-2">Kontak</h3>

                    <div class="text-sm space-y-1">
                    <p>
                        <strong class="w-28 inline-block">Emergency call</strong> :
                        <a href="tel:0816766999" class="ml-1" aria-label="Call emergency">
                        <span class="text-[#cf2e2e]">0816 766 999</span>
                        </a>
                    </p>

                    <p>
                        <strong class="w-28 inline-block">Hotline</strong> :
                        <a href="https://wa.me/+62816862222" target="_blank" rel="noopener noreferrer" class="ml-1">
                        <span class="text-[#08549c]">0816 86 2222</span>
                        </a>
                    </p>

                    <p>
                        <strong class="w-28 inline-block">Customer care</strong> :
                        <span class="ml-1 text-[#08549c]">(024) 8600-2222</span>
                    </p>

                    <p>
                        <strong class="w-28 inline-block">Email</strong> :
                        <a href="mailto:cs@rssamsoehidajat.com" target="_blank" rel="noopener noreferrer" class="ml-1 text-[#08549c]">
                        cs@rssamsoehidajat.com
                        </a>
                    </p>
                    </div>
                </div>

                <!-- Informasi links (baris terpisah di bawah grid, agar tetap rapi di mobile) -->
                <div class="">
                <h3 class="inline-flex items-center border-l-4 border-[#08549c] pl-3 text-base font-semibold mb-3">Informasi</h3>

                <nav aria-label="Footer information" class="mt-2">
                    <ul class="flex flex-col sm:flex-col space-y-2 sm:space-y-0 text-sm">
                    <li><a href="https://rssamsoehidajat.com/berita-terbaru/" class="hover:underline">Berita</a></li>
                    <li><a href="https://www.google.com/maps/place/RS+SAMSOE+HIDAJAT/..." class="hover:underline" target="_blank" rel="noopener noreferrer">Lokasi</a></li>
                    <li><a href="https://rssamsoehidajat.com/karir/" class="hover:underline">Karir</a></li>
                    </ul>
                </nav>
                </div>
            </div>


        </div>

        <!-- Bottom: copyright + socials -->
        <div class="border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                <p>Copyright © 2025 - PT Samsoe Hidajat Medika</p>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Social icons -->
                <a
                href="https://www.facebook.com/people/RS-Samsoe-Hidajat/100087513987240"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Facebook"
                class="p-2 rounded hover:bg-gray-100"
                >
                <!-- Facebook SVG (keperluan styling: ukuran riil 20px) -->
                <svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" class="block">
                    <path fill="currentColor" d="M20,10.1c0-5.5-4.5-10-10-10S0,4.5,0,10.1c0,5,3.7,9.1,8.4,9.9v-7H5.9v-2.9h2.5V7.9C8.4,5.4,9.9,4,12.2,4c1.1,0,2.2,0.2,2.2,0.2v2.5h-1.3c-1.2,0-1.6,0.8-1.6,1.6v1.9h2.8L13.9,13h-2.3v7C16.3,19.2,20,15.1,20,10.1z"></path>
                </svg>
                </a>

                <a
                href="https://www.instagram.com/rs.samsoehidajat/"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Instagram"
                class="p-2 rounded hover:bg-gray-100"
                >
                <!-- Instagram SVG -->
                <svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" class="block">
                    <circle cx="10" cy="10" r="3.3" fill="currentColor"></circle>
                    <path fill="currentColor" d="M14.2,0H5.8C2.6,0,0,2.6,0,5.8v8.3C0,17.4,2.6,20,5.8,20h8.3c3.2,0,5.8-2.6,5.8-5.8V5.8C20,2.6,17.4,0,14.2,0zM10,15c-2.8,0-5-2.2-5-5s2.2-5,5-5s5,2.2,5,5S12.8,15,10,15z M15.8,5C15.4,5,15,4.6,15,4.2s0.4-0.8,0.8-0.8s0.8,0.4,0.8,0.8S16.3,5,15.8,5z"></path>
                </svg>
                </a>
            </div>
            </div>
        </div>
        </footer>

</div>
</x-guest-layout>
