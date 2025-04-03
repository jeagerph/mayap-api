<?php
    namespace App\Traits;

	use Illuminate\Support\Facades\Auth;
	use Illuminate\Database\Eloquent\SoftDeletes as EloquentSoftDeletes;

    trait SoftDeletes
    {
		use EloquentSoftDeletes;

        /**
         * Override: Perform the actual delete query on this model instance.
         *
         * @return void
         */
        protected function runSoftDelete()
        {
            $query = $this->setKeysForSaveQuery($this->newModelQuery());

            $time = $this->freshTimestamp();

            $columns = [$this->getDeletedAtColumn() => $this->fromDateTime($time)];

            $this->{$this->getDeletedAtColumn()} = $time;

            if ($this->timestamps && ! is_null($this->getUpdatedAtColumn())) {
                $this->{$this->getUpdatedAtColumn()} = $time;

                $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
                $columns['deleted_by'] = Auth::id() ?: 1;
            }

            $query->update($columns);
        }
    }
?>
