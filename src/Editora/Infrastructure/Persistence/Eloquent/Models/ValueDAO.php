<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ValueDAO extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'mage_values';

    protected $fillable = [
        'attribute_id',
        'language',
        'value',
        'extra_data',
    ];

    protected function attribute(): BelongsTo
    {
        return $this->belongsTo(AttributeDAO::class, 'attribute_id', 'id');
    }
}
