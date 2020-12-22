<?php

namespace App\General;

use App\General\CMS\ManageTag;
use App\Marketplace\Common\Tag;
use Maatwebsite\Excel\Facades\Excel;

class ManageTagDownload
{
    public function importExcel($data)
    {
        if (isset($data['csv'])) {
            $file = $data['csv'];
            $path = $file->getRealPath();
            $data = Excel::load($path, function ($reader) {})->get();

            if (!empty($data) && $data->count()) {
                foreach ($data->toArray() as $key => $value) {
                    if (!empty($value)) {
                        $tag   = Tag::find($value['id']);

                        if ($tag) {
                            $names = ['en' => $value['en'], 'zh-CN' => $value['zh_cn']];
                            (new ManageTag)->updateName($tag, $names);
                            if (app()->environment('local')) {
                                \Log::info('importing tags: ' . $value['zh_cn'] . ' / ' . $tag->translations()->get()->where('lang', 'zh-CN')->first()->name);
                                if ($value['zh_cn'] != $tag->translations()->get()->where('lang', 'zh-CN')->first()->name) {
                                    \Log::warning('import value error');
                                }
                            }
                        }
                    }
                }
                return $tag->translations()->get();
            }
            abort(404, 'FILE_EMPTY');

        }
        abort(404, 'FILE_NOT_FOUND');
    }

    public function downloadExcel($data, $type)
    {

        Excel::create('tagtranslations_csv', function ($excel) use ($data) {
            $excel->sheet('mySheet', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download($type);
    }
}
