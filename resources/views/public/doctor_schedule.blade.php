
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
        .status-available { background: linear-gradient(135deg, #10B981, #34D399); }
        .status-onleave { background: linear-gradient(135deg, #EF4444, #F87171); }
        .filter-chip { transition: all 0.2s ease; }
        .filter-chip:hover { transform: scale(1.05); }
        .floating-element { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .gradient-text { background: linear-gradient(135deg, #0F766E, #14B8A6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
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
                                <option value="">Semua Klinik</option>
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
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Doctors Grid -->
    <section class="py-12 bg-gray-50">
        {{-- <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> --}}
    {{-- <section class="max-w-7xl mx-auto px-4"> --}}
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 gap-6 text-sm md:text-base">
            @forelse($doctors as $doctor)
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden p-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 md:w-20 md:h-20 flex-shrink-0 order-first rounded-2xl overflow-hidden bg-gray-100">
                            @if($doctor->profile_photo_path)
                                <img src="{{ Storage::url($doctor->profile_photo_path) }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/logogram-rssh.svg')}}" alt="No Image" class="object-scale-down">
                                {{-- <div class="w-full h-full flex mask-radial-at-center mask-radial-from-100% bg-[url('{{ asset('images/logogram-rssh.svg')}}')] bg-cover bg-center"></div> --}}
                            @endif
                        </div>
                        <div class="grid grid-flow-row md:grid-flow-col flex-1 justify-between">
                            <div class="flex-1 grow order-last md:order-none">
                                <h2 class="text-md md:text-lg font-medium">{{ $doctor->name }} @if($doctor->title) , {{ $doctor->title }} @endif</h2>
                                <div class="text-xs md:text-sm text-teal-600">{{ implode(', ', $doctor->specializations->pluck('name')->toArray()) }}</div>
                            </div>
                            @php
                                $onLeave = $doctor->leaves->first(function($l) use($today){
                                    return $today->between($l->start_date, $l->end_date ?? $l->start_date);
                                });
                            @endphp
                            <div class="order-none md:order-last">
                                @if($onLeave || $doctor->status === 'on_leave')
                                    <span class="px-2 py-2/3 md:py-1 rounded text-white text-[11px] md:text-sm status-onleave">Cuti</span>
                                @else
                                    <span class="px-2 py-2/3 md:py-1 rounded text-white text-[11px] md:text-sm status-available">Tersedia</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- <div class="m-3 text-xs md:text-sm text-gray-600">
                        <strong>Jadwal Praktik</strong>
                        <ul class="mt-2 text-xs md:text-sm text-gray-700">
                            @foreach($doctor->schedules->groupBy('day_of_week') as $day => $items)
                                @php
                                    $dayLabel = $day instanceof \App\Enums\DayOfWeek ? $day->label() : (\App\Enums\DayOfWeek::tryFrom($day)?->label() ?? $day);
                                @endphp
                                <li class="flex justify-between mb-1">
                                    <span>{{ $dayLabel }}</span>
                                    <span>
                                        @foreach($items as $it)
                                            <span class="inline-block ml-2 bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ \Carbon\Carbon::parse($it->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($it->end_time)->format('H:i') }}</span>
                                        @endforeach
                                    </span>
                                </li>
                            @endforeach
                            @if($doctor->schedules->isEmpty())
                                <li class="text-gray-400 italic text-center">Belum ada jadwal</li>
                            @endif
                        </ul>
                    </div> --}}
                    <div class="m-3 text-xs md:text-sm text-gray-600">
                        <strong class="text-gray-400">Jadwal Praktik</strong>
                        <ul class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2 text-xs md:text-sm text-gray-700">
                            @foreach($doctor->schedules->groupBy('day_of_week') as $day => $items)
                                @php
                                    $queryDay = request()->query('day');
                                    $dayLabel = $day instanceof \App\Enums\DayOfWeek ? $day->label() : (\App\Enums\DayOfWeek::tryFrom($day)?->label() ?? $day);
                                @endphp
                                <li class="flex flex-col mb-2 border border-gray-200 rounded-lg p-2 {{ ($queryDay !== null && $queryDay == $day) ? 'bg-blue-50 border-blue-300 border-2 mix-blend-multiply':'' }}">
                                    <span class="mb-1 text-center text-blue-800 {{ ($queryDay !== null && $queryDay == $day) ? 'font-bold':'font-semibold' }}">{{ $dayLabel }}</span>
                                    <span class="flex gap-2">
                                        @foreach($items as $it)
                                            <span class="grow text-center inline-block bg-green-200 mix-blend-multiply text-gray-700 p-2 rounded text-xs">{{ \Carbon\Carbon::parse($it->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($it->end_time)->format('H:i') }}</span>
                                        @endforeach
                                    </span>
                                </li>
                            @endforeach
                            @if($doctor->schedules->isEmpty())
                                <li class="col-span-full text-gray-400 italic text-center">Belum ada jadwal</li>
                            @endif
                        </ul>
                    </div>

                    <!-- Actions -->
                    {{-- <div class="flex gap-3">
                        <button class="flex-1 bg-teal-600 text-white py-2 px-4 rounded-lg hover:bg-teal-700 transition-colors font-medium text-sm md:text-base">
                            Lihat Detail
                        </button>
                        <button class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm md:text-base">
                            Buat Janji
                        </button>
                    </div> --}}
                </div>
            @empty
                <div class=" col-span-2 text-gray-400 italic text-center">Dokter atau spesialisasi tidak ditemukan untuk hari {{ \App\Enums\DayOfWeek::tryFrom(request()->query('day'))?->label() }}.</div>
            @endforelse
        </div>

        <div class="max-w-7xl mx-auto px-4 mt-6">
            {{ $doctors->links() }}
        </div>
    </section>

    <!-- Footer -->
    {{-- <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-2xl font-bold gradient-text mb-4">RS Samsoe Hidajat</h3>
                <p class="text-gray-400 mb-6">Platform terpercaya untuk mencari jadwal praktik dokter</p>
                <div class="border-t border-gray-800 pt-6">
                    <p class="text-gray-500 text-sm">© 2025 RS Samsoe Hidajat. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer> --}}
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
