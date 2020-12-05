<?php

namespace App\View\Components\Dropzones;

use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class Multiple extends Component
{
    public object|null $files;

    /**
     * Create a new component instance.
     *
     * @param string|int $index
     * @param string $folder
     * @param int $fileId
     * @param string $inputName
     */
    public function __construct(
        public string $inputName,
        public string $folder = "/",
        public string|int $index = 1,
        private int $fileId = 0,
    )
    {
        $this->files = DB::table('files')->where('id', $fileId)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.dropzones.multiple');
    }
}