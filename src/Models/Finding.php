<?php namespace Sanatorium\Hoofmanager\Models;

use Cartalyst\Attributes\EntityInterface;
use Illuminate\Database\Eloquent\Model;
use Platform\Attributes\Traits\EntityTrait;
use Cartalyst\Support\Traits\NamespacedEntityTrait;

class Finding extends Model implements EntityInterface {

	use EntityTrait, NamespacedEntityTrait;

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'findings';

	/**
	 * {@inheritDoc}
	 */
	protected $guarded = [
		'id',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $with = [
		'values.attribute',
		'part',
		'subpart',
		'disease',
        'treatment',
	];

	/**
	 * {@inheritDoc}
	 */
	protected static $entityNamespace = 'sanatorium/hoofmanager.finding';

	public function part()
	{
		return $this->belongsTo('Sanatorium\Hoofmanager\Models\Part', 'part_id', 'id');
	}

	public function subpart()
	{
		return $this->belongsTo('Sanatorium\Hoofmanager\Models\Subpart', 'subpart_id', 'id');
	}

	public function disease()
	{
		return $this->belongsTo('Sanatorium\Hoofmanager\Models\Disease', 'disease_id');
	}

	public function treatment()
	{
		return $this->belongsTo('Sanatorium\Hoofmanager\Models\Treatment', 'treatment_id');
	}

	public function item()
    {
        return $this->belongsTo('Sanatorium\Hoofmanager\Models\Item', 'item_id');
    }

}