<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

final class InstanceDAO extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'mage_instances';

    protected $fillable = [
        'uuid',
        'class_key',
        'key',
        'status',
        'start_publishing_date',
        'end_publishing_date',
    ];

    protected function attributes(): HasMany
    {
        return $this->hasMany(AttributeDAO::class, 'instance_id', 'id');
    }

    protected function relations(): HasMany
    {
        return $this->hasMany(RelationDAO::class, 'parent_instance_id', 'id');
    }
}
