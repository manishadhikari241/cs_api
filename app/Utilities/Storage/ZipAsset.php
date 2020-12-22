<?php

namespace App\Utilities\Storage;

use App\User;

class ZipAsset
{
    protected $zip;
    protected $tempFile;

    public function __construct($zipContent, $image = false)
    {
        ini_set('memory_limit','512M');
        $this->zip      = new \ZipArchive;
        $this->tempFile = @tempnam('tmp', 'zip');

        if ($image) {
            $this->zip->open($this->tempFile, \ZipArchive::CREATE);
            $this->zip->addFromString($image.'.jpg', $zipContent);
            $this->zip->close();
        } else {
            file_put_contents($this->tempFile, $zipContent);
        }
    }

    public function addLicence(User $user)
    {
        // $lang = $user->lang_pref === 'en' ? "en" : 'zh_CN';
        $this->zip->open($this->tempFile);
        $this->zip->addFromString("extended_licence.pdf", file_get_contents(app()->basePath() . "/app/Utilities/Storage/extended_licence.pdf"));
        $this->zip->close();
        return $this;
    }

    public function addTrialLicence(User $user)
    {
        // $lang = $user->lang_pref === 'en' ? "en" : 'zh_CN';
        $this->zip->open($this->tempFile);
        $this->zip->addFromString("personal_use_only.pdf", file_get_contents(app()->basePath() . "/app/Utilities/Storage/personal_use_only.pdf"));
        $this->zip->close();
        return $this;
    }

    public function addExclusiveOwnership(User $user)
    {
        // $lang = $user->lang_pref === 'en' ? "en" : 'zh_CN';
        $this->zip->open($this->tempFile);
        $this->zip->addFromString("exclusive_ownership.pdf", file_get_contents(app()->basePath() . "/app/Utilities/Storage/exclusive_ownership.pdf"));
        $this->zip->close();
        return $this;
    }

    public function dump()
    {
        $content = file_get_contents($this->tempFile);
        unlink($this->tempFile);
        return $content;
    }

    public function getLicenceContent()
    {
        return '';
    }
}
