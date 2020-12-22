<?php

namespace App\Marketplace\Collection;

use App\Marketplace\Designs\Design;
use App\Marketplace\Shopping\Sales;

class ItemTransfer
{
    public static function designOK($confirmation, $item)
    {
        $design = Design::where('id', $item->item_id)->first();
        if ($design->status === Design::IS_DOWNLOAD_ONLY) {
            return true;
        }
        $sales  = Sales::where('product_id', $design->id)->where('owner_id', \Auth::id())->first();
        // dd($sales);
        if (!$sales || $sales->owner_id !== \Auth::id() || ($design->buyer_id && $design->buyer_id !== \Auth::id())) {
            abort(422, 'DESIGN_NOT_PURCHASED_BY_COLLECTION_OWNER');
        }
        return true;
    }
    // public static function design($confirmation, $item)
    // {
    //     $design = Design::where('id', $item->item_id)->first();
    //     $sales  = Sales::where('product_id', $design->id)->where('owner_id', $item->collection->user_id)->first();
    //     $sales->owner_id = $confirmation->user_id;
    //     $sales->save();
    //     if ($design->status === Design::IS_SOLD) {
    //         // $design->buyer_id = $confirmation->user_id;
    //         $design->owner_id = $confirmation->user_id;
    //         $design->save();
    //     }
    //     // also transfer collection_item collection_id to retailer's collection_id
    //     $item->forceFill(['is_transferred' => 1]);
    //     $item->replicate()->push();
    //     $item->forceFill(['collection_id' => $confirmation->collection_id])->save();
    //     return $item;
    // }


    public static function uploadOK($confirmation, $item)
    {
        $userupload = UserUpload::find($item->item_id);
        if (!$userupload->source) {
            abort(422, 'UPLOADED_FILE_DOESNT_HAVE_AI_FILE');
        }
        return true;
    }

    // public static function upload($confirmation, $item)
    // {
    //     $userupload = UserUpload::find($item->item_id);
    //     $userupload->forceFill(['owner_id' => $confirmation->user_id])->save();
    //     $item->forceFill(['is_transferred' => 1]);
    //     $item->replicate()->push();
    //     $item->forceFill(['collection_id' => $confirmation->collection_id])->save();
    //     return $item;
    // }

    // public static function swatch($confirmation, $item)
    // {
    //     $swatch = Swatch::find($item->item_id);
    //     $item->forceFill(['is_transferred' => 1]);
    //     $item->replicate()->push();
    //     $item->forceFill(['collection_id' => $confirmation->collection_id])->save();
    //     return $item;
    // }

    // public static function color($confirmation, $item)
    // {
    //     $item->forceFill(['is_transferred' => 1]);
    //     $item->replicate()->push();
    //     $item->forceFill(['collection_id' => $confirmation->collection_id])->save();
    //     return $item;
    // }
}
