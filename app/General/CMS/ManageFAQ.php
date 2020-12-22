<?php

namespace App\General\CMS;

class ManageFAQ
{

    public function create($data)
    {
        $faq             = new FAQ();
        $faq->is_active  = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $faq->type       = $data['type'];
        $faq->sort_order = $data['sort_order'];
        $faq->save();

        if (!is_array($data['question'])) {
            $faq->translations()->save(new FAQTranslation([
                'id'       => $faq->id,
                'question' => $data['question'] ?? null,
                'answer'   => $data['answer'] ?? null,
                'lang'     => 'en',
            ]));
        } else {
            $this->updateQuestion($faq, $data['question'] ?? []);
            $this->updateAnswer($faq, $data['answer'] ?? []);
        }

        return $faq;
    }

    public function update($faq, $data)
    {
        if (isset($data['question'])) {
            $this->updateQuestion($faq, $data['question']);
        }
        if (isset($data['answer'])) {
            $this->updateAnswer($faq, $data['answer']);
        }
        $faq->is_active  = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $faq->type       = $data['type'];
        $faq->sort_order = $data['sort_order'];
        $faq->save();

        return $faq;
    }

    public function updateQuestion($faq, $questions = [])
    {
        if (!is_array($questions)) {$questions = [$questions];}
        $faq->load('translations');
        foreach ($questions as $key => $value) {
            $translation = $faq->translations->where('id', $faq->id)->where('lang', $key)->first();
            if (!$translation) {
                $faq->translations()->save(new FAQTranslation(['id' => $faq->id, 'question' => $value, 'lang' => $key]));
            } else {
                FAQTranslation::where(['id' => $faq->id, 'lang' => $key])->update(['question' => $value]);
            }
        }
    }

    public function updateAnswer($faq, $answers = [])
    {
        if (!is_array($answers)) {$answers = [$answers];}
        $faq->load('translations');
        foreach ($answers as $key => $value) {
            $translation = $faq->translations->where('id', $faq->id)->where('lang', $key)->first();
            if (!$translation) {
                $faq->translations()->save(new FAQTranslation(['id' => $faq->id, 'answer' => $value, 'lang' => $key]));
            } else {
                FAQTranslation::where(['id' => $faq->id, 'lang' => $key])->update(['answer' => $value]);
            }
        }
    }

}
