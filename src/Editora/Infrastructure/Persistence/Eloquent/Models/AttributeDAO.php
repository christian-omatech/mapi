<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AttributeDAO extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'mage_attributes';

    protected $fillable = [
        'instance_id',
        'parent_id',
        'key',
    ];

    protected function instance(): BelongsTo
    {
        return $this->belongsTo(InstanceDAO::class, 'instance_id', 'id');
    }

    protected function values(): HasMany
    {
        return $this->hasMany(ValueDAO::class, 'attribute_id', 'id');
    }

    protected function childrens(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    protected function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
}
