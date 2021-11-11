<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ValueDAO extends Model
{
    use SoftDeletes;

    protected $table = 'mage_values';

    protected $fillable = [
        'uuid',
        'attribute_id',
        'language',
        'value',
        'extra_data',
    ];
}
