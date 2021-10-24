<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RelationDAO extends Model
{
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(InstanceDAO::class, 'parent_instance_id', 'id');
    }
}
