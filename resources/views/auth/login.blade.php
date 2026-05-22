<x-guest-layout>
    <div class="background-overlay fixed inset-0 z-0 bg-cover bg-center opacity-50" 
         style="background-image: url('{{ asset('images/login-bg.jpg') }}'); filter: blur(3px);">
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center relative z-10 px-4 sm:px-0 bg-transparent">
        
        <div class="glass-panel w-full sm:max-w-md p-10 rounded-3xl text-center">
            
            <div class="mb-6">
                <h1 class="text-4xl font-black text-[#2d3092] tracking-tighter">MKKK</h1>
            </div>

            <h2 class="text-2l font-bold text-[#1d1e18] mb-8">Welcome Back</h2>

            <x-auth-session-status class="mb-4 text-sm font-medium text-green-600" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-5">
                @csrf

                <div class="text-left">
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        placeholder="Email Address" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        class="form-input text-[#1d1e18] placeholder-[#848889]"
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-semibold text-red-600" />
                </div>

                <div class="text-left">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Password" 
                        required 
                        autocomplete="current-password"
                        class="form-input text-[#1d1e18] placeholder-[#848889]"
                    >
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-semibold text-red-600" />
                    
                    @if (Route::has('password.request'))
                        <div class="text-right mt-2">
                            <a class="text-xs text-[#2d3092] hover:underline opacity-80 font-medium transition-opacity" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="flex items-center text-left pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 rounded border-white/50 bg-white/40 text-[#2d3092] focus:ring-[#2d3092] focus:ring-offset-0 transition-all cursor-pointer"
                        >
                        <span class="ms-2 text-xs text-[#1d1e18]/80 group-hover:text-[#1d1e18] transition-colors select-none">
                            {{ __('Remember me') }}
                        </span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    id="loginBtn"
                    class="w-full py-3 mt-4 bg-[#2d3092] text-white font-semibold rounded-full shadow-lg shadow-[#2d3092]/20 hover:bg-[#252755] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300"
                >
                    {{ __('Log in') }}
                </button>
            </form>

        </div>

        <!-- Privacy Policy Modal -->
        <div id="privacyModal" class="modal-overlay" onclick="closeModalOnOutside(event, 'privacyModal')">
            <div class="modal-container" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3 class="font-semibold text-base">Privacy Policy</h3>
                    <button type="button" onclick="closeModal('privacyModal')" class="modal-close" aria-label="Close">&times;</button>
                </div>
                <div class="modal-scroll modal-body">
                    <p class="body-text">MKKK Mall Parking Management System collects and processes vehicle plate numbers and entry/exit times solely for parking fee calculation and security purposes.</p>
                    <p class="body-text"><strong>Data Collection:</strong> We collect plate numbers, timestamps, and parking slot assignments. Payment information is processed through secure third-party gateways.</p>
                    <p class="body-text"><strong>Data Retention:</strong> Parking records are retained for 30 days for audit purposes, after which they are automatically archived.</p>
                    <p class="body-text"><strong>Your Rights:</strong> You may request deletion of your data by contacting mall administration.</p>
                    <p class="caption">Last updated: May 20, 2026</p>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('privacyModal')" class="btn-primary">Close</button>
                </div>
            </div>
        </div>

        <!-- Terms of Service Modal -->
        <div id="termsModal" class="modal-overlay" onclick="closeModalOnOutside(event, 'termsModal')">
            <div class="modal-container" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3 class="font-semibold text-base">Terms of Service</h3>
                    <button type="button" onclick="closeModal('termsModal')" class="modal-close" aria-label="Close">&times;</button>
                </div>
                <div class="modal-scroll modal-body">
                    <p class="body-text">By using the MKKK Mall Parking Management System, you agree to comply with mall parking rules and regulations.</p>
                    <p class="body-text"><strong>Parking Fees:</strong> Fees are calculated based on actual duration parked. The first 30 minutes are free. Hourly rates are as displayed at entry.</p>
                    <p class="body-text"><strong>Lost Tickets:</strong> Lost tickets will incur the maximum daily rate as published at the entrance.</p>
                    <p class="body-text"><strong>Liability:</strong> MKKK Mall is not responsible for theft, damage, or loss of vehicles or personal belongings.</p>
                    <p class="body-text"><strong>PWD Slots:</strong> Accessible parking is reserved for valid PWD permit holders only. Violators may be towed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('termsModal')" class="btn-primary">Close</button>
                </div>
            </div>
        </div>        

        <footer class="mt-12 text-center text-xs text-[#1d1e18] opacity-60 space-y-2">
            <p>Copyright © 2026 MKKK. All rights reserved.</p>
            <div class="space-x-4">
                <button type="button" onclick="openModal('privacyModal')" class="text-sm text-[#2d3092] hover:text-white transition-colors">Privacy Policy</button>
                <button type="button" onclick="openModal('termsModal')" class="text-sm text-[#2d3092] hover:text-white transition-colors">Terms of Service</button>
            </div>
        </footer>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function closeModalOnOutside(event, id) {
            if (event.target === document.getElementById(id)) {
                closeModal(id);
            }
        }
    </script>
</x-guest-layout>