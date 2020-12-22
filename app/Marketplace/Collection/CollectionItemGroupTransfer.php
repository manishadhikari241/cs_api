<?php

namespace App\Marketplace\Collection;

use App\Marketplace\Collection\CollectionItemGroupTransfer;
use App\Marketplace\Designs\Design;
use App\Marketplace\Shopping\Sales;
use App\Marketplace\Payments\Gateways\Freecheckout;
use App\Marketplace\Designs\FreeDesign;

class CollectionItemGroupTransfer
{
    public static function handle($confirmation)
    {
        $group = $confirmation->group;
        $items = $group->items;
        if ($group->is_transferred) {
            abort(422, 'THIS_ITEM_IS_ALREADY_TRANSFERRED');
        }
        if (!$confirmation) {
            abort(422, 'ITEM_NOT_YET_CONFIRMED_BY_ANY_USER');
        }
        foreach ($items as $item) {
            if ($item->is_transferred) {
                abort(422, 'THIS_ITEM_IS_ALREADY_TRANSFERRED');
            }
            self::transferrable($confirmation, $item);
        }
        \DB::transaction(function () use ($confirmation, $group, $items, $item) {
            $group = clone ($item->group->replicate());
            $group->forceFill([ 'collection_id'  => $confirmation->collection_id ]);
            $group->push();
            foreach ($items as $item) {
                self::transfer($confirmation, $group, $item);
            }
        }, 3);
        return $confirmation;
    }

    protected static function transferrable($confirmation, $item)
    {
        $type = $item->item_type;
        switch ($type) {
            case 'App\Marketplace\Designs\Design':
                return ItemTransfer::designOK($confirmation, $item);
                break;
            case 'App\Marketplace\Collection\UserUpload':
                return ItemTransfer::uploadOK($confirmation, $item);
                break;
            case 'App\Marketplace\Collection\Swatch':
                return true;
                break;
            case 'App\Marketplace\Collection\CollectionColor':
                return true;
                break;
            default:
                abort(422, 'INVALID_TYPE');
                break;
        }
    }

    protected static function transfer($confirmation, $group, $item)
    {
        $type = $item->item_type;
        switch ($type) {
            case 'App\Marketplace\Designs\Design':
                return CollectionItemGroupTransfer::design($confirmation, $group, $item);
                break;
            case 'App\Marketplace\Collection\UserUpload':
                return CollectionItemGroupTransfer::upload($confirmation, $group, $item);
                break;
            case 'App\Marketplace\Collection\Swatch':
                return CollectionItemGroupTransfer::swatch($confirmation, $group, $item);
                break;
            case 'App\Marketplace\Collection\CollectionColor':
                return CollectionItemGroupTransfer::color($confirmation, $group, $item);
                break;
            default:
                abort(422, 'INVALID_TYPE');
                break;
        }
    }

    public static function design($confirmation, $group, $item)
    {
        $design = Design::where('id', $item->item_id)->first();
        if ($design->status === Design::IS_DOWNLOAD_ONLY) {
            $freeDesign = FreeDesign::where('design_id', $design->id)->firstOrFail();
            if (!$confirmation->user->freeDesigns()->find($freeDesign->id)) {
                $confirmation->user->freeDesigns()->save($freeDesign);
            }
            return self::handleSuccessTransfer($confirmation, $group, $item);
        }
        $sales  = Sales::where('product_id', $design->id)->where('owner_id', \Auth::id())->first();
        // dd($sales);
        // if (!$sales || $sales->owner_id !== \Auth::id() || ($design->buyer_id && $design->buyer_id !== \Auth::id())) {abort(422, 'DESIGN_NOT_PURCHASED_BY_COLLECTION_OWNER');}
        // abort(404);
        if ($sales) {
            $sales->forceFill(['owner_id' => $confirmation->user_id])->save();
        } else {
            // nothing, assume previous this design has been transferred in previous items in group
        }
        // \Log::info('After', $sales->toArray());

        if ($design->status === Design::IS_SOLD) {
            // $design->buyer_id = $confirmation->user_id;
            $design->owner_id = $confirmation->user_id;
            $design->save();
        }
        // also transfer collection_item collection_id to retailer's collection_id
        return self::handleSuccessTransfer($confirmation, $group, $item);
    }

    public static function upload($confirmation, $group, $item)
    {
        $userupload = UserUpload::find($item->item_id);
        if (!$userupload->source) {
            abort(422, 'UPLOADED_FILE_DOESNT_HAVE_AI_FILE');
        }
        $userupload->forceFill(['owner_id' => $confirmation->user_id])->save();
        return self::handleSuccessTransfer($confirmation, $group, $item);
    }

    public static function swatch($confirmation, $group, $item)
    {
        $swatch = Swatch::find($item->item_id);
        return self::handleSuccessTransfer($confirmation, $group, $item);
    }

    public static function color($confirmation, $group, $item)
    {
        return self::handleSuccessTransfer($confirmation, $group, $item);
    }

    public static function handleSuccessTransfer($confirmation, $group, $item)
    {
        $item->group->forceFill(['is_transferred' => 1])->save();
        // $item->forceFill(['is_transferred' => 1]);
        $item->replicate()->push();
        $item->forceFill(['collection_id' => $confirmation->collection_id, 'group_id' => $group->id])->save();
        return $item;
    }
}
