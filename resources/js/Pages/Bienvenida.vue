<template>
    <Head title="Welcome" />

    <div class="relative min-h-screen bg-black text-white overflow-hidden">
        <!-- Fondo animado con resplandor rojo -->
<div class="absolute inset-0 z-0 overflow-hidden">
    <!-- Círculo central que late -->
    <div class="absolute top-1/2 left-1/2 h-[120vh] w-[120vh] bg-purple-600 opacity-20 blur-3xl rounded-full animate-ping-slow transform -translate-x-1/2 -translate-y-1/2"></div>

    <!-- Resplandor estático con gradiente más marcado -->
    <div class="absolute inset-0 bg-gradient-radial from-indigo-700/40 via-indigo-900/20 to-black"></div>
</div>

        <!-- Contenido -->
        <div
            class="relative z-10 flex flex-col min-h-screen items-center justify-between py-8 px-6"
        >
            <!-- Header -->
            <header class="w-full max-w-7xl flex items-center justify-between">
                <svg
                    class="h-10 w-auto text-white/10"
                    fill="currentColor"
                    viewBox="0 0 62 65"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="..." />
                </svg>

                <nav v-if="canLogin" class="space-x-4 text-sm">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="text-white/80 hover:text-white font-medium"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-white/80 hover:text-white font-medium"
                            >Log in</Link
                        >
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="text-white/80 hover:text-white font-medium"
                            >Register</Link
                        >
                    </template>
                </nav>
            </header>

            <!-- Main -->
            <main class="text-center flex flex-col items-center justify-center flex-grow">
                <div class="w-92 h-auto transition-transform duration-500 group-hover:rotate-3 group-hover:scale-110 cursor-pointer">
                    <DinoPirata colorClass="text-sky-500" />
                </div>

                <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white drop-shadow-lg">
                    ¡Bienvenido a <span class="text-purple-400">DarkPirate</span>!
                </h1>
                <p class="mt-3 text-sm md:text-lg text-white/70 drop-shadow-sm">
                    Navegando con Laravel + Inertia + Vue
                </p>
            </main>

            <!-- Footer -->
            <footer class="text-sm text-white/50">
                Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
            </footer>
        </div>
    </div>
</template>

<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import DinoPirata from '@/Components/SVG/DinoPirata.vue'

    defineProps({
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: { type: String, required: true },
        phpVersion: { type: String, required: true },
    });
</script>

<style>
    @keyframes ping-slow {
        0%,
        100% {
            transform: scale(1);
            opacity: 0.2;
        }
        50% {
            transform: scale(1.15);
            opacity: 0.35;
        }
    }

    @keyframes fade-in-up {
        0% {
            transform: translateY(10px);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-ping-slow {
        animation: ping-slow 8s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .bg-gradient-radial {
        background-image: radial-gradient(circle at center, var(--tw-gradient-stops));
    }

    .animate-fade-in-up {
        animation: fade-in-up 1s ease-out;
    }
</style>
