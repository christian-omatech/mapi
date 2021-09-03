<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class RelationDAO extends Model
{
    use SoftDeletes;

    protected $table = 'mage_relations';

    protected $fillable = [
        'key',
        'parent_instance_id',
        'child_instance_id',
        'order',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(InstanceDAO::class, 'child_instance_id', 'id');
    }
}
