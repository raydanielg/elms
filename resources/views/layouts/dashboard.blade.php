<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'ELMS'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: { 50:'#FBEDEF',100:'#F5D0D6',200:'#EBA1AD',300:'#C55B6E',400:'#7C1528',500:'#5A0917',600:'#4A0712',700:'#3A050E',800:'#2A030A',900:'#1A0205' },
                        orange: { 50:'#FFF5EB',100:'#FEE8CC',200:'#FDD1A0',300:'#F9A54E',400:'#F6891F',500:'#D66F0E',600:'#B85A0A',700:'#8A4408',800:'#5C2E05',900:'#3A1D03' },
                        success: { 50:'#E8F5EE',100:'#C5E5D2',200:'#8FCCA8',300:'#5AB37E',400:'#2E9A5C',500:'#1E7A46',600:'#186238',700:'#124A2A',800:'#0C321C',900:'#061A0E' },
                        warning: { 50:'#FBF3E0',100:'#F5E2B8',200:'#EBCB7A',300:'#E0B43C',400:'#D69A1E',500:'#B98207',600:'#966A05',700:'#735204',800:'#503A02',900:'#2D2201' },
                        danger: { 50:'#FCEAEA',100:'#F8C5C5',200:'#F08A8A',300:'#E85050',400:'#D63A3A',500:'#C22F2F',600:'#A22525',700:'#821B1B',800:'#621111',900:'#420707' },
                        info: { 50:'#E8F2FB',100:'#C5E0F5',200:'#8FC2E9',300:'#5AA4DD',400:'#2E87CB',500:'#1D6FA5',600:'#185A87',700:'#134569',800:'#0E304B',900:'#091B2D' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
        .animate-fade { animation: fadeIn 0.3s ease-out both; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { background: rgba(255,255,255,0.06); }
        .sidebar-link.active { background: rgba(255,255,255,0.08); color: #fff; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #2A030A; }
        ::-webkit-scrollbar-thumb { background: #5A0917; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #F6891F; }
        .card-sm { transition: all 0.2s cubic-bezier(0.4,0,0.2,1); }
        .card-sm:hover { transform: translateY(-2px); box-shadow: 0 8px 30px -8px rgba(0,0,0,0.1); }
        .btn-loading { position: relative; color: transparent !important; pointer-events: none; }
        .btn-loading::after { content: ''; position: absolute; top: 50%; left: 50%; width: 18px; height: 18px; margin: -9px 0 0 -9px; border: 2.5px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: btnSpin 0.6s linear infinite; }
        @keyframes btnSpin { to { transform: rotate(360deg); } }
        .swal2-popup { font-family: 'Nunito', sans-serif !important; border-radius: 12px !important; }
        .swal2-toast { font-family: 'Nunito', sans-serif !important; border-radius: 10px !important; box-shadow: 0 4px 24px rgba(0,0,0,0.12) !important; }
        .swal2-icon { border-radius: 50% !important; }
        .swal2-title { font-size: 14px !important; font-weight: 700 !important; padding: 0 !important; }
        .swal2-html-container { font-size: 12px !important; margin: 0 !important; }
        .swal2-confirm { border-radius: 8px !important; font-weight: 700 !important; font-size: 12px !important; padding: 6px 16px !important; }
    </style>
</head>
<body class="font-['Nunito',sans-serif] antialiased bg-[#F7F4F1] text-[#1C1B1B]">

    {{-- Mobile Overlay --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="dashSidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-maroon-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
        {{-- Brand --}}
        <div class="h-16 flex items-center px-6 border-b border-maroon-800/50 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <div class="ml-2.5 leading-tight">
                <span class="text-white font-bold text-sm tracking-wide block">ELMS</span>
                <span class="text-orange-400 text-[10px] font-medium tracking-wider uppercase">Learning Platform</span>
            </div>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            {{-- Dashboard --}}
            <div class="sidebar-group">
                <a href="{{ route('home') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard</span>
                </a>
            </div>

            {{-- Learning --}}
            <div class="pt-3 pb-1 px-3">
                <span class="text-[10px] font-bold text-maroon-300/40 uppercase tracking-wider">Learning</span>
            </div>

            <div class="sidebar-group">
                <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Courses</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Assessments</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    <span>Certificates</span>
                </a>
            </div>

            {{-- Management --}}
            <div class="pt-3 pb-1 px-3">
                <span class="text-[10px] font-bold text-maroon-300/40 uppercase tracking-wider">Management</span>
            </div>

            <div class="sidebar-group">
                <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Users</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Institutions</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Billing & Plans</span>
                </a>
            </div>

            {{-- System --}}
            <div class="pt-3 pb-1 px-3">
                <span class="text-[10px] font-bold text-maroon-300/40 uppercase tracking-wider">System</span>
            </div>

            <div class="sidebar-group">
                <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Analytics</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-maroon-100 text-sm font-medium">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Settings</span>
                </a>
            </div>

        </div>

        {{-- Bottom User --}}
        <div class="p-4 border-t border-maroon-800/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-maroon-300/60">{{ Auth::user()->email ?? '' }}</p>
                </div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('dash-logout').submit();" class="text-maroon-300/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
                <form id="dash-logout" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-bold text-gray-800">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                {{-- Search --}}
                <div class="hidden md:flex items-center bg-gray-50 rounded-xl px-3 py-2 border border-gray-200 focus-within:border-maroon-300 focus-within:ring-2 focus-within:ring-maroon-100 transition-all">
                    <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="globalSearch" placeholder="Search..." class="bg-transparent text-sm outline-none w-48 text-gray-700 placeholder-gray-400">
                </div>

                {{-- Notifications --}}
                <button class="relative p-2 rounded-xl hover:bg-gray-100 text-gray-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger-500 rounded-full"></span>
                </button>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6 animate-fade">
            @yield('content')
        </main>

    </div>

    {{-- SweetAlert2 Alert System --}}
    <script>
    (function() {
        function showAlert(type, title, message) {
            const Swal = window.Swal || window.Sweetalert2;
            if (!Swal) return;
            const colors = {
                success: '#5A0917',
                error: '#C22F2F',
                warning: '#B98207',
                info: '#1D6FA5'
            };
            const SwalMixin = Swal.mixin ? Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                customClass: { popup: 'swal2-toast' },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            }) : null;
            if (SwalMixin) {
                SwalMixin.fire({
                    icon: type,
                    title: title + (message ? ': ' + message : ''),
                    iconColor: colors[type] || '#0D3E63'
                });
            } else {
                Swal.fire({
                    icon: type,
                    title: title,
                    text: message || '',
                    confirmButtonColor: colors[type] || '#0D3E63',
                    confirmButtonText: 'OK'
                });
            }
        }
        window.showAlert = showAlert;
        window.showToast = showAlert;

        @if(session('status'))
            showAlert('success', 'Success!', '{{ session('status') }}');
        @endif
        @if(session('success'))
            showAlert('success', 'Success!', '{{ session('success') }}');
        @endif
        @if(session('error'))
            showAlert('error', 'Oops...', '{{ session('error') }}');
        @endif
        @if(session('warning'))
            showAlert('warning', 'Warning', '{{ session('warning') }}');
        @endif
        @if(session('info'))
            showAlert('info', 'Info', '{{ session('info') }}');
        @endif

        @if($errors->any())
            @php $allErrors = $errors->all(); @endphp
            showAlert('error', 'Validation Error', '{{ implode("\n", $allErrors) }}');
        @endif
    })();

    function toggleSidebar() {
        const sidebar = document.getElementById('dashSidebar');
        const overlay = document.getElementById('mobileOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // GLOBAL AJAX + SWEETALERT2 SYSTEM
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    (function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: { popup: 'swal2-toast' },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        window.Toast = Toast;

        const ConfirmMixin = Swal.mixin({
            customClass: {
                popup: 'swal2-popup',
                confirmButton: 'swal2-confirm bg-danger-500 hover:bg-danger-600 text-white',
                cancelButton: 'swal2-confirm bg-gray-200 hover:bg-gray-300 text-gray-700'
            },
            buttonsStyling: false,
        });
        window.ConfirmMixin = ConfirmMixin;

        function showLoader() {
            let loader = document.getElementById('ajaxProgress');
            if (!loader) {
                loader = document.createElement('div');
                loader.id = 'ajaxProgress';
                loader.style.cssText = 'position:fixed;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#5A0917,#F6891F,#5A0917);background-size:200% 100%;animation:ajaxProgress 1s linear infinite;z-index:9999;';
                document.body.appendChild(loader);
                if (!document.getElementById('ajaxProgressStyle')) {
                    const style = document.createElement('style');
                    style.id = 'ajaxProgressStyle';
                    style.textContent = '@keyframes ajaxProgress{0%{background-position:100% 0}100%{background-position:-100% 0}}';
                    document.head.appendChild(style);
                }
            }
            loader.style.display = 'block';
        }
        function hideLoader() {
            const loader = document.getElementById('ajaxProgress');
            if (loader) loader.style.display = 'none';
        }

        window.notify = function(type, title, message) {
            const colors = { success: '#5A0917', error: '#C22F2F', warning: '#B98207', info: '#1D6FA5' };
            Toast.fire({
                icon: type,
                title: title + (message ? ': ' + message : ''),
                iconColor: colors[type] || '#5A0917'
            });
        };

        window.confirmAction = function(options) {
            return Swal.fire({
                title: options.title || 'Are you sure?',
                text: options.text || '',
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: options.confirmText || 'Yes, proceed',
                cancelButtonText: options.cancelText || 'Cancel',
                customClass: {
                    popup: 'swal2-popup',
                    confirmButton: 'swal2-confirm ' + (options.confirmClass || 'bg-danger-500 hover:bg-danger-600 text-white'),
                    cancelButton: 'swal2-confirm bg-gray-200 hover:bg-gray-300 text-gray-700'
                },
                buttonsStyling: false,
                reverseButtons: true,
            });
        };

        window.ajaxRequest = function(url, method, data, options) {
            options = options || {};
            showLoader();

            const headers = {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };

            const fetchOptions = {
                method: method,
                headers: headers,
                credentials: 'same-origin'
            };

            if (data instanceof FormData) {
                fetchOptions.body = data;
                if (method !== 'POST') {
                    data.append('_method', method);
                    fetchOptions.method = 'POST';
                }
            } else if (data) {
                headers['Content-Type'] = 'application/json';
                fetchOptions.body = JSON.stringify(data);
            }

            return fetch(url, fetchOptions)
                .then(r => {
                    const contentType = r.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        return r.json().then(d => ({ data: d, status: r.status, ok: r.ok }));
                    }
                    return r.text().then(html => ({ html, status: r.status, ok: r.ok }));
                })
                .then(result => {
                    hideLoader();
                    if (result.data !== undefined) {
                        if (!result.ok) {
                            if (result.status === 422 && result.data.errors) {
                                const msgs = Object.values(result.data.errors).flat();
                                notify('error', 'Validation Error', msgs.join('. '));
                            } else {
                                notify('error', 'Error', result.data.message || 'Something went wrong');
                            }
                            if (options.onError) options.onError(result.data);
                            return Promise.reject(result.data);
                        }
                        if (result.data.message) {
                            notify(result.data.type || 'success', result.data.title || 'Success', result.data.message);
                        }
                        if (options.onSuccess) options.onSuccess(result.data);
                        if (result.data.redirect) {
                            setTimeout(() => { window.location.href = result.data.redirect; }, 1000);
                        } else if (options.reload !== false) {
                            setTimeout(() => { window.location.reload(); }, 800);
                        }
                        return result.data;
                    }
                    if (result.html && options.onHtml) {
                        options.onHtml(result.html);
                    }
                    return result;
                })
                .catch(err => {
                    hideLoader();
                    if (err && err.message) notify('error', 'Error', err.message);
                    return Promise.reject(err);
                });
        };

        function bindAjaxForms() {
            document.querySelectorAll('form[data-ajax], form.ajax-form').forEach(form => {
                if (form._ajaxBound) return;
                form._ajaxBound = true;

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = form.querySelector('button[type="submit"]');
                    const method = (form.querySelector('input[name="_method"]')?.value || form.method || 'POST').toUpperCase();
                    const isDelete = method === 'DELETE';
                    const confirmMsg = form.dataset.confirm || (isDelete ? 'Delete this item?' : null);

                    function submitForm() {
                        if (btn) { btn.disabled = true; btn.classList.add('btn-loading'); }
                        const formData = new FormData(form);
                        ajaxRequest(form.action, method, formData, {
                            reload: form.dataset.noReload !== 'true',
                            onSuccess: (data) => {
                                if (form.dataset.closeModal) {
                                    const modal = document.getElementById(form.dataset.closeModal);
                                    if (modal) modal.classList.add('hidden');
                                }
                                if (form.dataset.resetOnSuccess === 'true') form.reset();
                            },
                            onError: () => {
                                if (btn) { btn.disabled = false; btn.classList.remove('btn-loading'); }
                            }
                        });
                    }

                    if (confirmMsg) {
                        confirmAction({
                            title: confirmMsg,
                            text: form.dataset.confirmText || '',
                            confirmText: form.dataset.confirmText || 'Yes, delete it!',
                            icon: form.dataset.confirmIcon || 'warning',
                            confirmClass: form.dataset.confirmClass || 'bg-danger-500 hover:bg-danger-600 text-white',
                        }).then(result => {
                            if (result.isConfirmed) submitForm();
                        });
                    } else {
                        submitForm();
                    }
                });
            });
        }

        function bindDeleteButtons() {
            document.querySelectorAll('form[data-confirm]:not([data-ajax]):not(.ajax-form)').forEach(form => {
                if (form._confirmBound) return;
                form._confirmBound = true;
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const confirmMsg = form.dataset.confirm;
                    const confirmText = form.dataset.confirmText || 'Yes, proceed';
                    const confirmClass = form.dataset.confirmClass || 'bg-danger-500 hover:bg-danger-600 text-white';
                    confirmAction({
                        title: confirmMsg,
                        text: form.dataset.confirmText || 'This action cannot be undone.',
                        confirmText: confirmText,
                        icon: form.dataset.confirmIcon || 'warning',
                        confirmClass: confirmClass,
                    }).then(result => {
                        if (result.isConfirmed) {
                            form._confirmBound = false;
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('[data-delete-url]').forEach(btn => {
                if (btn._deleteBound) return;
                btn._deleteBound = true;
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.dataset.deleteUrl;
                    const confirmMsg = this.dataset.confirm || 'Delete this item?';
                    const confirmText = this.dataset.confirmText || 'Yes, delete it!';
                    const rowId = this.dataset.rowId;

                    confirmAction({
                        title: confirmMsg,
                        text: this.dataset.confirmText || 'This action cannot be undone.',
                        confirmText: confirmText,
                    }).then(result => {
                        if (result.isConfirmed) {
                            ajaxRequest(url, 'DELETE').then(data => {
                                if (rowId) {
                                    const row = document.getElementById(rowId);
                                    if (row) {
                                        row.style.transition = 'opacity 0.3s';
                                        row.style.opacity = '0';
                                        setTimeout(() => row.remove(), 300);
                                    }
                                }
                            });
                        }
                    });
                });
            });
        }

        function bindActionButtons() {
            document.querySelectorAll('[data-action-url]').forEach(btn => {
                if (btn._actionBound) return;
                btn._actionBound = true;
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.dataset.actionUrl;
                    const method = this.dataset.actionMethod || 'POST';
                    const confirmMsg = this.dataset.confirm;

                    function doAction() {
                        if (btn) { btn.disabled = true; btn.classList.add('btn-loading'); }
                        ajaxRequest(url, method, null, {
                            reload: true,
                            onSuccess: () => {
                                if (btn) { btn.disabled = false; btn.classList.remove('btn-loading'); }
                            },
                            onError: () => {
                                if (btn) { btn.disabled = false; btn.classList.remove('btn-loading'); }
                            }
                        });
                    }

                    if (confirmMsg) {
                        confirmAction({
                            title: confirmMsg,
                            text: this.dataset.confirmText || '',
                            confirmText: this.dataset.confirmText || 'Yes, proceed',
                            confirmClass: this.dataset.confirmClass || 'bg-maroon-500 hover:bg-maroon-600 text-white',
                            icon: this.dataset.confirmIcon || 'question',
                        }).then(result => {
                            if (result.isConfirmed) doAction();
                        });
                    } else {
                        doAction();
                    }
                });
            });
        }

        window.openModal = function(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('hidden');
        };
        window.closeModal = function(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('hidden');
        };

        function initAjax() {
            bindAjaxForms();
            bindDeleteButtons();
            bindActionButtons();
        }

        const observer = new MutationObserver(() => initAjax());
        observer.observe(document.body, { childList: true, subtree: true });

        initAjax();
    })();
    </script>
    @stack('scripts')
</body>
</html>
