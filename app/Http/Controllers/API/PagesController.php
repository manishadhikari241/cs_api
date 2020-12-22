<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\General\Contact;
use App\Http\Controllers\Controller;
use App\Page;
use App\PageBlock;
use App\PageBlockTranslation;
use App\PageTranslation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PagesController extends Controller
{

    public function show(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)->first();
        if (!$page)
            return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND);

        $page->translations = [
            'en' => PageTranslation::where('page_id', $page->id)->where('lang', 'en')->first(),
            'ch' => PageTranslation::where('page_id', $page->id)->where('lang', 'ch')->first()
        ];
        if ($request->slug == 'about') {
            $page->team = User::whereNotNull('avatar')->get();
        }
        if ($request->slug == 'contact') {
            $page['email'] = Contact::where('id', 1)->first();
        }

        return $page;
    }


    public function showBlocks(Request $request, $slug)
    {
        $pageblocks = PageBlock::where('page_slug', $slug)->orderBy('order', 'asc')->get();
        foreach ($pageblocks as $pageblock) {
            $pageblock->translations = [
                'en' => PageBlockTranslation::where('page_block_id', $pageblock->id)->where('lang', 'en')->first(),
                'ch' => PageBlockTranslation::where('page_block_id', $pageblock->id)->where('lang', 'ch')->first()
            ];
        }
        return $pageblocks;
    }

}
