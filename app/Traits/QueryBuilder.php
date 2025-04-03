<?php

namespace App\Traits;

trait QueryBuilder
{
	public $search;

    public $filter;
    
    public $sort;

    public $range;

    public $custom;

    public $operand;

    public $take;

    public $limit = 10;

	public $all;
	
	public $total;
    
    public $debug = false;

    public $searchByWords = false;

    public $searchByRequestFields = false;

	private $query;

	private $queryStrings = ['search', 'filter', 'sort', 'range', 'take', 'limit', 'all', 'debug', 'total'];

	private $qFunctions = ['Search', 'Filter', 'Sort', 'Range', 'Custom'];
	
	public function build($custom = NULL, $operand = 'where')
    {
        $custom && $this->custom = $custom;
        $custom && $this->operand = $operand;

        $this->assignQueryValues();

        \DB::enableQueryLog();

        $result = $this->__queryBuilder();

        if($this->debug && env('APP_DEBUG')):

            return \DB::getQueryLog();

        else:

            return $result;

        endif;
	}
	
	private function assignQueryValues()
    {
        foreach($this->queryStrings as $queryString):
            !$this->$queryString && $this->$queryString = request()->get($queryString);
        endforeach;
	}

    private function __queryBuilder()
    {
        $this->query = $this->query();

		self::__queryLoader();
		
		if($this->total):
            return $this->query->get()->count();

        elseif ($this->all || $this->take):
            return ['data' => $this->query->take($this->take)->get()];

        else:
            $items = $this->query->paginate($this->limit)
                                ->appends(request()->query());

            $query = request()->query();
            $query['page'] = $items->currentPage();
    
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $items->items(),
                $items->total(),
                $items->perPage(),
                $items->currentPage(),
                [
                    'path' => forceHttps(\Request::url()),
                    'query' => $query
                ]
            );            

        endif;
	}
	
	private function __queryLoader()
    {
        foreach($this->qFunctions as $function):
            if($function == 'Search' && $this->searchByWords):
                self::__querySearchByWords();
            elseif($function == 'Search' && $this->searchByRequestFields):
                self::__querySearchByRequestFields();
            else:
                self::{'__query'.$function}();
            endif;
		endforeach;
    }

	private function __querySearch()
    {
        if($this->isEmpty('search')) return false;

        $this->query->where( function($query)
        {
            $searchValue = $this->search;

            foreach($this->searchFields as $key => $searchFieldName):

                $operator = 'orWhere';
                $searchValue = $this->searchValue($searchValue);

                $relationStringsAndFieldName = explode(':', $searchFieldName);

                $relationStrings = $relationStringsAndFieldName[0];
                $searchFieldName = count($relationStringsAndFieldName) == 1 ? $searchFieldName : $relationStringsAndFieldName[1];

                if(count($relationStringsAndFieldName) > 1 && !empty($relationStrings)):

                    $query->{$operator.'Has'}($relationStrings, function($q) use($searchFieldName, $searchValue, $operator)
                    {
                        $q->where($searchFieldName, 'like', "%{$searchValue}%");
                    });
                else:
                    $query->$operator($searchFieldName, 'like' , "%{$searchValue}%");
                endif;

            endforeach;
        });
	}

	private function __querySearchByWords()
    {
        if($this->isEmpty('search')) return false;

        $this->query->where( function($query)
        {
            $searchValue = $this->search;

            foreach($this->searchFields as $key => $searchFieldName):

                $operator = 'orWhere';
                $searchValue = $this->searchValue($searchValue);

                $relationStringsAndFieldName = explode(':', $searchFieldName);

                $relationStrings = $relationStringsAndFieldName[0];
                $searchFieldName = count($relationStringsAndFieldName) == 1 ? $searchFieldName : $relationStringsAndFieldName[1];

                $keywords = explode(' ', $searchValue);

                if(count($relationStringsAndFieldName) > 1 && !empty($relationStrings)):

                    $query->{$operator.'Has'}($relationStrings, function($q) use($searchFieldName, $searchValue, $operator)
                    {
                        foreach($keywords as $key => $keyword):
                            if($key):
                                $q->orWhere($searchFieldName, 'like', "%{$keyword}%");
                            else:
                                $q->where($searchFieldName, 'like', "%{$keyword}%");
                            endif;
                        endforeach;
                    });

                else:
                    foreach($keywords as $key => $keyword):
                        $query->$operator($searchFieldName, 'like' , "%{$keyword}%");
                    endforeach;
                endif;

            endforeach;
        });
	}

    private function __querySearchByRequestFields()
    {
        if($this->isEmpty('search')) return false;

        $this->query->where( function($query)
        {
            $searchValue = $this->search;

            foreach(request()->query('searchFields') as $key => $searchFieldName):

                $operator = 'orWhere';
                $searchValue = $this->searchValue($searchValue);

                $relationStringsAndFieldName = explode(':', $searchFieldName);

                $relationStrings = $relationStringsAndFieldName[0];
                $searchFieldName = count($relationStringsAndFieldName) == 1 ? $searchFieldName : $relationStringsAndFieldName[1];

                if(count($relationStringsAndFieldName) > 1 && !empty($relationStrings)):

                    $query->{$operator.'Has'}($relationStrings, function($q) use($searchFieldName, $searchValue, $operator)
                    {
                        $q->where($searchFieldName, 'like', "%{$searchValue}%");
                    });
                else:
                    $query->$operator($searchFieldName, 'like' , "%{$searchValue}%");
                endif;

            endforeach;
        });
	}
	
	private function __queryFilter()
    {
        if($this->isEmpty('filter')) return;
        
        $this->query->where(function($query)
        {
            foreach($this->filter as $key => $searchValue):

                if(array_key_exists($key, $this->filterFields)):
                    
                    $operator = $this->operator($searchValue);
                    $searchValue = $this->searchValue($searchValue);

                    $relationStringsAndFieldName = explode(':', $this->filterFields[$key]);

                    $relationStrings = $relationStringsAndFieldName[0];
                    $searchFieldName = count($relationStringsAndFieldName) == 1 ? $this->filterFields[$key] : $relationStringsAndFieldName[1];

                    if(count($relationStringsAndFieldName) > 1 && !empty($relationStrings)):

                        if(strpos($relationStrings, 'slug') !== false): // Customized to Vantagehunt

                            $model = \App\Models\Slug::where('code', $searchValue)->first();
                            
                            if($model):

                                $query->{$operator}($searchFieldName, $model->slug->id);

                            endif;

                        else:

                            $query->{$operator.'Has'}($relationStrings, function($q) use($searchFieldName, $searchValue)
                            {
                                $this->filterQueryValues($q, $searchFieldName, $searchValue);
                            });

                        endif;

                    else:

                        $this->filterQueryValues($query, $searchFieldName, $searchValue);

                    endif;

                endif;
    
            endforeach;
        });
	}

	private function __querySort()
    {
        if($this->isEmpty('sort')) return;

        foreach($this->sort as $key => $searchValue):

            if(array_key_exists($key, $this->sortFields)):

                $relationStringsAndFieldName = explode(':', $this->sortFields[$key]);

                $relationStrings = $relationStringsAndFieldName[0];
                $searchFieldName = count($relationStringsAndFieldName) == 1 ? $searchFieldName : $relationStringsAndFieldName[1];

                if(count($relationStringsAndFieldName) > 1 && !empty($relationStrings)):

                    $this->query->whereHas($relationStrings, function($q) use($searchFieldName, $searchValue)
                    {
                        $q->orderBy($searchFieldName, $searchValue);
                    });

                else:
                    $this->query->orderBy($searchFieldName, $searchValue);
                endif;

            endif;

        endforeach;
    }
	
	private function __queryRange()
    {
        if($this->isEmpty('range')) return;

        $this->query->where(function($query)
        {
            foreach($this->range as $key => $searchValue):

                if(array_key_exists($key, $this->rangeFields)):

                    $relationStringsAndFieldName = explode(':', $this->rangeFields[$key]);
                    
                    $relationStrings = $relationStringsAndFieldName[0];
                    $searchFieldName = count($relationStringsAndFieldName) == 1 ? $searchFieldName : $relationStringsAndFieldName[1];
                    
                    if(count($relationStringsAndFieldName) > 1 && !empty($relationStrings)):
    
                        $query->whereHas($relationStrings, function($q) use($searchFieldName, $searchValue)
                        {
                            $this->rangeQueryValues($q, $searchFieldName, $searchValue);
                        });
    
                    else:
                        $this->rangeQueryValues($query, $searchFieldName, $searchValue);
                    endif;
    
                endif;
    
            endforeach;
        });
	}
	
	private function __queryCustom()
    {
        if(!isset($this->custom)) return;

        $this->query->{$this->operand}($this->custom);
    }

    private function operator($searchValue)
    {
        $operator = 'where';
                    
        if(strpos($searchValue, ':or') !== false) $operator = 'orWhere';

        return $operator;
    }

    private function searchValue($searchValue)
    {
        if(strpos($searchValue, ':or') !== false && strpos($searchValue, 'or') === 0) return explode(':', $searchValue)[1];

        return $searchValue;
    }

    private function filterQueryValues($query, $searchFieldName, $searchValue)
    {
        if(strpos($searchValue, '!') !== false && $searchValue[0] == '!'):

            return $query->where($searchFieldName, '!=', $searchValue);

        elseif(strpos($searchValue, ',') !== false):

            $values = explode(',', $searchValue);

            return $query->whereIn($searchFieldName, $values);

        else:

            return $query->where($searchFieldName, $searchValue);

        endif;
    }

    private function rangeQueryValues($query, $searchFieldName, $searchValue)
    {
        $rangeValues = explode(',', $searchValue);

        return $query->where(function($q) use($searchFieldName, $rangeValues)
        {
            $q->whereBetween($searchFieldName, $rangeValues)
                ->orWhereDate($searchFieldName, $rangeValues[0])
                ->orWhereDate($searchFieldName, $rangeValues[1]);
        });
    }

    private function isEmpty($field)
    {
        return !isset($this->{$field.'Fields'}) || !request()->has($field) || is_null($this->{$field.'Fields'}) || empty(request()->get($field));
    }
}
