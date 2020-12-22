<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LibInspirationsTranslation extends Model
{
    protected $table = 'lib_inspiration_translation';

    public $timestamps    = false;
    public $incrementing  = false;

    protected $primaryKey = ['id', 'lang'];

    protected $fillable = ['id', 'lang', 'name'];

    public function LibInspiration()
    {
        return $this->belongsTo(LibInspiration::class, 'id');
    }

    public function getUploadPath()
    {
        return 'uploads/lib/inspiration/';
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
