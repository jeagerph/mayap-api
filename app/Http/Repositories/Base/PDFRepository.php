<?php

namespace App\Http\Repositories\Base;

use App\Traits\FileStorage;

class PDFRepository
{
    use FileStorage;

	public function export($file, $filename, $folder = 'test/')
	{
        $path = formPath($folder, $filename);

        $this->savePDF($path, $file);

		return env('CDN_URL', '') . '/storage/' . $path;
	}

    public function fileName($from, $to, $name, $format = '.pdf')
    {
        $dateFrom = (new \Carbon\Carbon($from))->format('M-d-Y');
        $dateTo = (new \Carbon\Carbon($to))->format('M-d-Y');

        $filename = $name . '-';
        $filename .= $dateFrom . '-' . $dateTo . '-';
        $filename .= time() . $format;

        return $filename;
    }
}
?>