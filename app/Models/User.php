<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use \Laravel\Jetstream\HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'age',
        'poids',
        'taille',
        'sexe',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // Jetstream Team Relationships
    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'user_id');
    }

    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Get the actual current team instance, with fallback to personal team.
     */
    public function getCurrentTeamInstance()
    {
        $team = $this->currentTeam()->getResults();
        if ($team) {
            return $team;
        }
        return $this->ownedTeams()->where('personal_team', true)->first();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
            'poids' => 'float',
            'taille' => 'float',
        ];
    }

    // Role check methods
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isProfessor(): bool
    {
        return $this->role === 'professor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ===== RELATIONS WELLNESS =====
    public function habits()
    {
        return $this->belongsToMany(Habit::class, 'user_habits')
            ->withPivot('current_streak', 'longest_streak', 'started_at', 'is_active')
            ->withTimestamps();
    }

    public function userHabits()
    {
        return $this->hasMany(UserHabit::class);
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'participations')
            ->withPivot('joined_at', 'current_progress', 'completed', 'completed_at', 'points_earned')
            ->withTimestamps();
    }

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function createdChallenges()
    {
        return $this->hasMany(Challenge::class, 'created_by');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function advices()
    {
        return $this->hasMany(Advice::class);
    }

    public function givenAdvices()
    {
        return $this->hasMany(Advice::class, 'advisor_id');
    }

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // ===== RELATIONS NUTRITION =====
    public function repas()
    {
        return $this->hasMany(Repas::class);
    }

    // ===== MÉTHODES NUTRITION =====
    public function calculateBMR()
    {
        // Vérifier que les données nécessaires existent
        if (!$this->age || !$this->poids || !$this->taille || !$this->sexe) {
            return 2000; // Valeur par défaut
        }

        // Formule Harris-Benedict révisée
        if ($this->sexe === 'homme' || $this->sexe === 'male' || $this->sexe === 'M') {
            return 88.362 + (13.397 * $this->poids) + (4.799 * $this->taille) - (5.677 * $this->age);
        } else {
            return 447.593 + (9.247 * $this->poids) + (3.098 * $this->taille) - (4.330 * $this->age);
        }
    }

    public function getCaloriesGoal()
    {
        return $this->calculateBMR() * 1.6; // Facteur d'activité modérée
    }

    public function getTodayRepas()
    {
        return $this->repas()->whereDate('date_consommation', today())->get();
    }

    public function getTodayCalories()
    {
        return $this->repas()
            ->whereDate('date_consommation', today())
            ->sum('calories_total') ?? 0;
    }

    public function getNutritionStats($days = 7)
    {
        $startDate = today()->subDays($days - 1);

        return $this->repas()
            ->where('date_consommation', '>=', $startDate)
            ->selectRaw('
                       AVG(calories_total) as avg_calories,
                       AVG(proteines_total) as avg_proteines,
                       AVG(glucides_total) as avg_glucides,
                       AVG(lipides_total) as avg_lipides,
                       COUNT(*) as total_repas
                   ')
            ->first();
    }


}
