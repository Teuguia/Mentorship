<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Mentorat') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-900">
    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-4 lg:px-8">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 font-bold text-white">
                        M
                    </div>
                    <span class="text-xl font-bold text-blue-700">Mentorship</span>
                </a>
                <p class="mt-4 max-w-md text-sm leading-6 text-slate-500">
                    Une plateforme pour trouver le bon mentor, organiser vos sessions et progresser avec un accompagnement adapte.
                </p>
            </div>

            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-900">Navigation</h2>
                <div class="mt-4 grid gap-3 text-sm">
                    <a href="{{ route('home') }}" class="text-slate-500 hover:text-blue-600">Accueil</a>
                    <a href="{{ route('web.mentors.index') }}" class="text-slate-500 hover:text-blue-600">Mentors</a>
                    <a href="{{ auth()->check() ? route('sessions.index') : route('login') }}" class="text-slate-500 hover:text-blue-600">Sessions</a>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-900">Compte</h2>
                <div class="mt-4 grid gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-blue-600">Tableau de bord</a>
                        <a href="{{ route('profile.edit') }}" class="text-slate-500 hover:text-blue-600">Profil</a>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-500 hover:text-blue-600">Connexion</a>
                        <a href="{{ route('register') }}" class="text-slate-500 hover:text-blue-600">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} Mentorship. Tous droits reserves.</p>
                <a href="{{ route('become.mentor') }}" class="font-semibold text-blue-600 hover:text-blue-700">
                    Devenir mentor
                </a>
            </div>
        </div>
    </footer>
</body>
</html>
