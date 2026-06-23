<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';

    public const CATEGORIES = [
        'technology' => 'Technology',
        'business' => 'Business',
        'lifestyle' => 'Lifestyle',
        'health' => 'Health',
        'travel' => 'Travel',
        'education' => 'Education',
        'finance' => 'Finance',
        'food' => 'Food',
        'sports' => 'Sports',
        'general' => 'General',
    ];

    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt',
        'body', 'image', 'category', 'status', 'published', 'published_at',
        'submitted_at', 'approved_at', 'approved_by', 'rejection_reason',
        'meta_title', 'meta_description', 'views',
    ];

    protected $casts = [
        'published'    => 'boolean',
        'published_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('approved', true);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('published', true)
            ->where('status', self::STATUS_PUBLISHED)
            ->latest('published_at');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Auto-generate slug from title
    protected static function booted()
    {
        static::creating(function ($post) {
            if (!$post->slug) {
                $post->slug = static::uniqueSlug($post->title);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                $post->slug = static::uniqueSlug($post->title, $post->id);
            }
        });
    }

    // Reading time helper
    public function readingTime()
    {
        $words = str_word_count(strip_tags($this->body));
        return max(1, ceil($words / 220)) . ' min read';
    }

    public function statusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    private static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $counter = 2;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
