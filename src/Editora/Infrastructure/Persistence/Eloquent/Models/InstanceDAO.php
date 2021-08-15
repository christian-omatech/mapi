<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function values(): HasMany
    {
        return $this->hasMany(ValueDAO::class, 'instance_id', 'id');
    }
}
