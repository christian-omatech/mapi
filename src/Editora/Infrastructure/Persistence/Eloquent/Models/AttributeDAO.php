<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected function values(): HasMany
    {
        return $this->hasMany(ValueDAO::class, 'attribute_id', 'id');
    }
}
