<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'tags';

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'slug',
        'count',
        'namespace',
    ];

    /**
     * Returns the most used tags across the app, even
     * for different users.
     */
    public function scopePopular($query, $namespace) {
      if (isset($namespace)) {
        $query = $query->where('namespace', $namespace);
      }

      return $query->orderBy('tags.count', 'DESC');
    }

    /**
     * Returns popular tags for the given author in the given namespace
     */
    public function scopePopularByAuthor($query, $author_id, $namespace) {
      return $query->select(DB::raw('select tags.id, tags.slug, tags.name, tags.namespace, count(tagged.tag_id) as count'))
                  ->join('tagged', 'tagged.tag_id', '=', 'tags.id')
                  ->join('transactions', 'transactions.id', '=', 'tagged.taggable_id')
                  ->where('tagged.taggable_type', $namespace)
                  ->where('transactions.user_id', $author_id)
                  ->groupBy('tagged.tag_id')
                  ->orderBy('count DESC');
    }
}
