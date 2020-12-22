<?php

namespace App\Http\Controllers\API\CMS;

use App\General\Contact;
use App\Http\Controllers\Controller;
use App\Page;
use App\PageBlock;
use App\PageBlockTranslation;
use App\PageTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

class PagesController extends Controller
{

    public function index(Request $request)
    {
        $pages = Page::orderBy('id', 'asc');
        if ($request->term)
            $pages = $pages->where('slug', 'like', '%' . $request->term . '%');

        $pages = $pages->paginate(20);

        foreach ($pages as $page) {
            $page->translations = [
                'en' => PageTranslation::where('page_id', $page->id)->where('lang', 'en')->first(),
                'ch' => PageTranslation::where('page_id', $page->id)->where('lang', 'ch')->first()
            ];
        }

        return $pages;
    }

    public function show(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $page->translations = [
            'en' => PageTranslation::where('page_id', $page->id)->where('lang', 'en')->first(),
            'ch' => PageTranslation::where('page_id', $page->id)->where('lang', 'ch')->first()
        ];
        $page['email'] = Contact::where('id', 1)->first();
        return $page;
    }

    public function update(Request $request, $id)
    {

        $pageTranslationEN = PageTranslation::where('page_id', $id)->where('lang', 'en')->first();
        $pageTranslationEN->title = $request->titleEN;
        $pageTranslationEN->body = $request->bodyEN;
        $pageTranslationEN->info_title = $request->infoTitleEN;
        $pageTranslationEN->info_body = $request->infoBodyEN;
        $pageTranslationEN->meta_description = $request->metaDescriptionEN;
        $pageTranslationEN->meta_keywords = $request->metaKeywordsEN;
        $pageTranslationEN->save();

        $pageTranslationCH = PageTranslation::where('page_id', $id)->where('lang', 'ch')->first();
        $pageTranslationCH->title = $request->titleCH;
        $pageTranslationCH->body = $request->bodyCH;
        $pageTranslationCH->info_title = $request->infoTitleCH;
        $pageTranslationCH->info_body = $request->infoBodyCH;
        $pageTranslationCH->meta_description = $request->metaDescriptionCH;
        $pageTranslationCH->meta_keywords = $request->metaKeywordsCH;
        $pageTranslationCH->save();

        if ($request->email || $request->whatsapp || $request->qq || $request->wechat) {
            Contact::where('id', 1)->first()->update([
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'qq' => $request->qq,
                'wechat' => $request->wechat,
            ]);
        }
        if ($request->image) {
            Contact::where('id', 1)->first()->update([
                'image' => $request->image,
            ]);
        }
        return respondOK();

    }

    public function storeClients(Request $request)
    {

        if ($request->hasFile('clients')) {
            $file = $request->file('clients');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $img = ImageManagerStatic::make($file->getRealPath());
            $img->resize(120, 120, function ($constraint) {
                $constraint->aspectRatio();
            });

            $img->stream();

            Storage::disk('public')->put('clients' . '/' . $fileName, $img, 'public');
        }

    }

    public function showBlocks(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $pageblocks = PageBlock::where('page_slug', $page->slug)->orderBy('order', 'asc')->get();
        foreach ($pageblocks as $pageblock) {
            $pageblock->translations = [
                'en' => PageBlockTranslation::where('page_block_id', $pageblock->id)->where('lang', 'en')->first(),
                'ch' => PageBlockTranslation::where('page_block_id', $pageblock->id)->where('lang', 'ch')->first()
            ];
        }
        return $pageblocks;
    }

    public function createBlock(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $nextPosition = PageBlock::where('page_slug', $page->slug)->count();

        $pageBlock = new PageBlock();
        $pageBlock->page_slug = $page->slug;
        $pageBlock->order = $nextPosition;
        $pageBlock->save();

        $pageBlockTranslationEN = new PageBlockTranslation();
        $pageBlockTranslationEN->page_block_id = $pageBlock->id;
        $pageBlockTranslationEN->lang = 'en';
        $pageBlockTranslationEN->save();

        $pageBlockTranslationCH = new PageBlockTranslation();
        $pageBlockTranslationCH->page_block_id = $pageBlock->id;
        $pageBlockTranslationCH->lang = 'ch';
        $pageBlockTranslationCH->save();

        return response()->json($pageBlock, 201);
    }

    public function updateBlock(Request $request, $id, $blockId)
    {
        $pageblock = PageBlock::findOrFail($blockId);

        if ($request->has('image_url'))
            $pageblock->image_url = $request->image_url;
        if ($request->has('button_url'))
            $pageblock->button_url = $request->button_url;
        $pageblock->save();

        $pageBlockTranslationEN = PageBlockTranslation::where('page_block_id', $pageblock->id)->where('lang', 'en')->first();
        $pageBlockTranslationCH = PageBlockTranslation::where('page_block_id', $pageblock->id)->where('lang', 'ch')->first();

        if ($request->has('titleEN'))
            $pageBlockTranslationEN->title = $request->titleEN;
        if ($request->has('titleCH'))
            $pageBlockTranslationCH->title = $request->titleCH;
        if ($request->has('descriptionEN'))
            $pageBlockTranslationEN->description = $request->descriptionEN;
        if ($request->has('descriptionCH'))
            $pageBlockTranslationCH->description = $request->descriptionCH;
        if ($request->has('buttonTextEN'))
            $pageBlockTranslationEN->button_text = $request->buttonTextEN;
        if ($request->has('buttonTextCH'))
            $pageBlockTranslationCH->button_text = $request->buttonTextCH;
        $pageBlockTranslationEN->save();
        $pageBlockTranslationCH->save();

        return respondOK();
    }

    public function sortBlocks(Request $request)
    {
        foreach ($request->order as $order => $blockId) {
            $pageblock = PageBlock::findOrFail($blockId);
            $pageblock->order = $order;
            $pageblock->save();
        }
        return respondOK();
    }

    public function deleteBlock(Request $request, $id, $blockId)
    {
        $pageblock = PageBlock::findOrFail($blockId);
        if ($pageblock->image_url)
            Storage::delete('public/' . $pageblock->image_url);
        $pageblock->delete();

        return respondOK();
    }

}
