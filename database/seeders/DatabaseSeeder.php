<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Mentee;
use App\Models\Mentor;
use App\Models\Review;
use App\Models\Session;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $domains = collect([
            ['name' => 'Business', 'description' => 'Stratégie, leadership, finance.'],
            ['name' => 'Technologie', 'description' => 'Développement, data, IA.'],
            ['name' => 'Marketing', 'description' => 'Growth, marque, contenu.'],
            ['name' => 'Design', 'description' => 'UX/UI, produit, identité visuelle.'],
            ['name' => 'Développement', 'description' => 'Web, mobile, architecture.'],
        ])->map(fn ($data) => Domain::firstOrCreate(['name' => $data['name']], $data));

        $mentorPhotos = [
            'https://i.pravatar.cc/300?img=12',
            'https://i.pravatar.cc/300?img=32',
            'https://i.pravatar.cc/300?img=45',
            'https://i.pravatar.cc/300?img=51',
            'https://i.pravatar.cc/300?img=68',
            'https://i.pravatar.cc/300?img=15',
        ];

        $mentors = collect();
        $africanNames = [
            'Aminata Diop', 'Ibrahima Ndiaye', 'Fatoumata Coulibaly', 'Cheikh Bamba',
            'Awa Traoré', 'Mamadou Diallo', 'Mariam Koné', 'Seydou Camara',
            'Aïssatou Ba', 'Ousmane Sow', 'Khadija Benyoussef', 'Youssef El Amrani',
            'Noura El Idrissi', 'Imane Ziani', 'Chidera Okafor', 'Ngozi Eze',
            'Kofi Mensah', 'Ama Boateng', 'Kwame Asante', 'Zainab Abubakar',
        ];

        foreach (range(1, 20) as $index) {
            $user = User::updateOrCreate(
                ['email' => "mentor{$index}@example.com"],
                [
                    'name' => $africanNames[($index - 1) % count($africanNames)],
                    'password' => bcrypt('password'),
                    'role' => 'mentor',
                ]
            );

            $mentor = Mentor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => $faker->paragraph(3),
                    'expertise_title' => Arr::random([
                        'Coach Leadership',
                        'Mentor Produit',
                        'Spécialiste Growth',
                        'Expert Data',
                        'Développeur Senior',
                    ]),
                    'years_experience' => $faker->numberBetween(3, 15),
                    'hourly_rate' => $faker->randomFloat(2, 15, 120),
                    'availability' => Arr::random(['En ligne', 'Douala', 'Yaoundé', 'À distance']),
                    'profile_photo' => $mentorPhotos[($index - 1) % count($mentorPhotos)] ?? 'https://i.pravatar.cc/300?img=10',
                ]
            );

            $mentor->domains()->sync($domains->random($faker->numberBetween(1, 3))->pluck('id')->all());
            $mentors->push($mentor);
        }

        $mentees = collect();
        foreach (range(1, 6) as $index) {
            $user = User::updateOrCreate(
                ['email' => "mentee{$index}@example.com"],
                [
                    'name' => $faker->name(),
                    'password' => bcrypt('password'),
                    'role' => 'mentee',
                ]
            );

            $mentee = Mentee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'goals' => $faker->sentence(),
                    'profession' => $faker->jobTitle(),
                    'level' => Arr::random(['Junior', 'Intermédiaire', 'Senior']),
                ]
            );

            $mentee->domains()->sync($domains->random($faker->numberBetween(1, 2))->pluck('id')->all());
            $mentees->push($mentee);
        }

        if (Session::count() === 0) {
            $sessions = collect();
            foreach (range(1, 8) as $index) {
                $mentor = $mentors->random();
                $mentee = $mentees->random();

                $sessions->push(Session::create([
                    'mentor_id' => $mentor->id,
                    'mentee_id' => $mentee->id,
                    'title' => Arr::random(['Session découverte', 'Coaching carrière', 'Roadmap produit', 'Revue CV', 'Mock interview']),
                    'description' => $faker->paragraph(),
                    'scheduled_at' => now()->addDays($faker->numberBetween(1, 20))->setTime($faker->numberBetween(9, 18), 0),
                    'status' => Arr::random(['pending', 'confirmed', 'completed']),
                    'meeting_link' => 'https://meet.example.com/'.Str::random(10),
                    'notes' => null,
                ]));
            }

            $testimonials = [
                'Un accompagnement clair et motivant. Je recommande vivement.',
                'Excellent mentorat, des conseils concrets et actionnables.',
                'Très bonne séance, j’ai progressé rapidement grâce aux retours.',
                'Écoute, pédagogie et suivi : tout ce qu’il faut pour avancer.',
            ];

            foreach ($sessions->take(4) as $session) {
                Review::create([
                    'session_id' => $session->id,
                    'mentor_id' => $session->mentor_id,
                    'mentee_id' => $session->mentee_id,
                    'rating' => $faker->numberBetween(4, 5),
                    'comment' => $testimonials[array_rand($testimonials)],
                ]);
            }
        }

        $sampleSessions = Session::query()
            ->with(['mentor.user', 'mentee.user'])
            ->take(6)
            ->get();

        foreach ($sampleSessions as $session) {
            $conversation = Conversation::firstOrCreate(
                [
                    'mentor_id' => $session->mentor_id,
                    'mentee_id' => $session->mentee_id,
                ],
                [
                    'session_id' => $session->id,
                    'call_room' => 'mentorconnect-'.$session->mentor_id.'-'.$session->mentee_id.'-demo',
                    'last_message_at' => now()->subDays(rand(0, 4)),
                ]
            );

            if ($conversation->messages()->exists()) {
                continue;
            }

            $messages = [
                [$session->mentee->user->id, 'Bonjour, je voulais confirmer notre prochaine session.'],
                [$session->mentor->user->id, 'Oui, tout est bon pour moi. Pensez a preparer vos questions cles.'],
                [$session->mentee->user->id, 'Parfait, merci. Je vous enverrai aussi mon CV avant l appel.'],
            ];

            foreach ($messages as [$senderId, $body]) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $senderId,
                    'body' => $body,
                    'read_at' => now(),
                ]);
            }

            $conversation->forceFill([
                'last_message_at' => now(),
            ])->save();
        }
    }
}
